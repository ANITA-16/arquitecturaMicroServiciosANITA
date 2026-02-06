<?php

namespace App\Services;

use App\Traits\ConsumesExternalService;

class CartService
{
    use ConsumesExternalService;

    /**
     * The base uri to be used to consume the cart service
     * @var string
     */
    public $baseUri;

    /**
     * The secret to be used to consume the cart service
     * @var string
     */
    public $secret;

    public function __construct()
    {
        $this->baseUri = env('CART_SERVICE_BASE_URL');
        $this->secret = env('CART_SERVICE_SECRET');
        
        // Note: Cart service might not exist yet, so we make it optional
        if (empty($this->baseUri)) {
            \Log::warning('CART_SERVICE_BASE_URL is not configured in .env file');
        }
    }

    /**
     * Get cart items for a user
     * @param int $userId
     * @return array
     */
    public function obtainCart($userId)
    {
        if (empty($this->baseUri)) {
            throw new \RuntimeException('Cart Service is not available');
        }
        
        return $this->performRequest('GET', "/cart/user/{$userId}");
    }

    /**
     * Clear cart for a user after order creation
     * @param int $userId
     * @return array
     */
    public function clearCart($userId)
    {
        if (empty($this->baseUri)) {
            return ['message' => 'Cart Service not available, skipping clear'];
        }

        try {
            return $this->performRequest('DELETE', "/cart/user/{$userId}");
        } catch (\Exception $e) {
            \Log::error("Failed to clear cart for user {$userId}: " . $e->getMessage());
            return ['error' => $e->getMessage()];
        }
    }
}