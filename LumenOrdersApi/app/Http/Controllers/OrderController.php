<?php

namespace App\Http\Controllers;

use App\Traits\ApiResponser;
use App\Order;
use App\Services\UserService;
use App\Services\BookService;
use App\Services\CartService;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Validation\Rule;

class OrderController extends Controller
{
    use ApiResponser;

    /**
     * The service to consume the user service
     * @var UserService
     */
    public $userService;

    /**
     * The service to consume the book service
     * @var BookService
     */
    public $bookService;

    /**
     * The service to consume the cart service
     * @var CartService
     */
    public $cartService;

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct(
        UserService $userService,
        BookService $bookService,
        CartService $cartService
    ) {
        $this->userService = $userService;
        $this->bookService = $bookService;
        $this->cartService = $cartService;
    }

    /**
     * Return the list of all orders
     * GET /orders
     * @return Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        // Permitir filtrado por status
        $query = Order::query();

        if ($request->has('status')) {
            $query->where('status', $request->status);
        }

        // Ordenar por más reciente
        $orders = $query->orderBy('created_at', 'desc')->get();

        return $this->successResponse($orders);
    }

    /**
     * Get orders for a specific user
     * GET /orders/user/{user_id}
     * @return Illuminate\Http\Response
     */
    public function getUserOrders($userId)
    {
        // Validar que el usuario existe
        //if (!$this->userService->userExists($userId)) {
        //    return $this->errorResponse('User does not exist', Response::HTTP_NOT_FOUND);
        //}

        $orders = Order::where('user_id', $userId)
            ->orderBy('created_at', 'desc')
            ->get();

        return $this->successResponse($orders);
    }


    /**
     * Create a new order
     * POST /orders
     * @return Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        // Validar datos de entrada
        $rules = [
            'user_id' => 'required|integer|min:1',
            'items' => 'required|array|min:1',
            'items.*.book_id' => 'required|integer|min:1',
            'items.*.quantity' => 'required|integer|min:1',
            'items.*.price' => 'required|numeric|min:0',
            'shipping_address' => 'required|string|max:500',
            'notes' => 'nullable|string|max:1000',
        ];

        $this->validate($request, $rules);

        // 1. Validar que el usuario existe
        if (!$this->userService->userExists($request->user_id)) {
            return $this->errorResponse('User does not exist', Response::HTTP_NOT_FOUND);
        }

        // 2. Validar inventario de libros
        $inventoryValidation = $this->bookService->validateInventory($request->items);
        
        if (!$inventoryValidation['valid']) {
            return $this->errorResponse([
                'message' => 'Inventory validation failed',
                'errors' => $inventoryValidation['errors']
            ], Response::HTTP_UNPROCESSABLE_ENTITY);
        }

        // 3. Calcular el total del pedido
        $total = 0;
        $orderItems = [];

        foreach ($request->items as $item) {
            $bookId = $item['book_id'];
            $quantity = $item['quantity'];
            $price = $item['price'];

            $subtotal = $price * $quantity;
            $total += $subtotal;

            // Enriquecer items con información del libro
            $book = $inventoryValidation['books'][$bookId] ?? [];
            $orderItems[] = [
                'book_id' => $bookId,
                'title' => $book['title'] ?? 'Unknown',
                'quantity' => $quantity,
                'price' => $price,
                'subtotal' => $subtotal,
            ];
        }

        // 4. Crear el pedido
        $orderData = [
            'user_id' => $request->user_id,
            'status' => Order::STATUS_PENDING,
            'total' => round($total, 2),
            'items' => $orderItems,
            'shipping_address' => $request->shipping_address,
            'notes' => $request->notes,
        ];

        $order = Order::create($orderData);

        // 5. Opcional: Limpiar el carrito del usuario
        try {
            $this->cartService->clearCart($request->user_id);
        } catch (\Exception $e) {
            // Si falla, no es crítico, el pedido ya fue creado
            \Log::warning("Could not clear cart for user {$request->user_id}: " . $e->getMessage());
        }

        return $this->successResponse($order, Response::HTTP_CREATED);
    }

    /**
     * Get a specific order
     * GET /orders/{id}
     * @return Illuminate\Http\Response
     */
    public function show($id)
    {
        $order = Order::findOrFail($id);
        return $this->successResponse($order);
    }

    /**
     * Update an order (mainly for changing status)
     * PUT /orders/{id}
     * @return Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        $order = Order::findOrFail($id);

        $rules = [
            'status' => [
                'sometimes',
                'required',
                Rule::in(Order::getValidStatuses())
            ],
            'shipping_address' => 'sometimes|string|max:500',
            'notes' => 'nullable|string|max:1000',
        ];

        $this->validate($request, $rules);

        // Validar transiciones de estado
        if ($request->has('status')) {
            $newStatus = $request->status;

            // No permitir cambiar a cancelled aquí, usar DELETE
            if ($newStatus === Order::STATUS_CANCELLED) {
                return $this->errorResponse(
                    'Use DELETE /orders/{id} to cancel an order',
                    Response::HTTP_BAD_REQUEST
                );
            }

            // Validar que la transición es válida
            if (!$order->canTransitionTo($newStatus)) {
                return $this->errorResponse(
                    "Cannot transition from '{$order->status}' to '{$newStatus}'",
                    Response::HTTP_UNPROCESSABLE_ENTITY
                );
            }
        }

        // Solo permitir actualizar shipping_address si está en pending
        if ($request->has('shipping_address') && $order->status !== Order::STATUS_PENDING) {
            return $this->errorResponse(
                'Can only update shipping address for pending orders',
                Response::HTTP_UNPROCESSABLE_ENTITY
            );
        }

        $order->fill($request->only(['status', 'shipping_address', 'notes']));

        if ($order->isClean()) {
            return $this->errorResponse(
                'At least one value must change',
                Response::HTTP_UNPROCESSABLE_ENTITY
            );
        }

        $order->save();

        return $this->successResponse($order);
    }

    /**
     * Cancel an order
     * DELETE /orders/{id}
     * @return Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $order = Order::findOrFail($id);

        // Validar que el pedido puede ser cancelado
        if (!$order->canBeCancelled()) {
            return $this->errorResponse(
                "Cannot cancel order with status '{$order->status}'. Only 'pending' or 'processing' orders can be cancelled.",
                Response::HTTP_UNPROCESSABLE_ENTITY
            );
        }

        // Cambiar estado a cancelled (soft delete)
        $order->status = Order::STATUS_CANCELLED;
        $order->save();
        $order->delete(); // Soft delete

        return $this->successResponse([
            'message' => 'Order cancelled successfully',
            'order' => $order
        ]);
    }

    /**
     * Get order statistics
     * GET /orders/stats/summary
     * @return Illuminate\Http\Response
     */
    public function statistics()
    {
        $stats = [
            'total_orders' => Order::count(),
            'pending' => Order::where('status', Order::STATUS_PENDING)->count(),
            'processing' => Order::where('status', Order::STATUS_PROCESSING)->count(),
            'shipped' => Order::where('status', Order::STATUS_SHIPPED)->count(),
            'delivered' => Order::where('status', Order::STATUS_DELIVERED)->count(),
            'cancelled' => Order::where('status', Order::STATUS_CANCELLED)->count(),
            'total_revenue' => Order::whereIn('status', [
                Order::STATUS_DELIVERED,
                Order::STATUS_SHIPPED
            ])->sum('total'),
        ];

        return $this->successResponse($stats);
    }
}