<?php

namespace App;

use Illuminate\Database\Eloquent\Model;


class Order extends Model
{
    //use SoftDeletes;

    /**
     * Estados válidos para un pedido
     */
    const STATUS_PENDING = 'pending';
    const STATUS_PROCESSING = 'processing';
    const STATUS_SHIPPED = 'shipped';
    const STATUS_DELIVERED = 'delivered';
    const STATUS_CANCELLED = 'cancelled';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'user_id',
        'status',
        'total',
        'items',
        'shipping_address',
        'notes',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array
     */
    protected $casts = [
        'total' => 'decimal:2',
        'items' => 'array', // Automáticamente convierte JSON a array
    ];

    /**
     * The attributes that should be mutated to dates.
     *
     * @var array
     */
    protected $dates = [
        'created_at',
        'updated_at',
        'deleted_at',
    ];

    /**
     * Obtener todos los estados válidos
     *
     * @return array
     */
    public static function getValidStatuses()
    {
        return [
            self::STATUS_PENDING,
            self::STATUS_PROCESSING,
            self::STATUS_SHIPPED,
            self::STATUS_DELIVERED,
            self::STATUS_CANCELLED,
        ];
    }

    /**
     * Verificar si el pedido puede ser cancelado
     *
     * @return bool
     */
    public function canBeCancelled()
    {
        return in_array($this->status, [
            self::STATUS_PENDING,
            self::STATUS_PROCESSING
        ]);
    }

    /**
     * Verificar si el pedido puede cambiar a un nuevo estado
     *
     * @param string $newStatus
     * @return bool
     */
    public function canTransitionTo($newStatus)
    {
        $transitions = [
            self::STATUS_PENDING => [self::STATUS_PROCESSING, self::STATUS_CANCELLED],
            self::STATUS_PROCESSING => [self::STATUS_SHIPPED, self::STATUS_CANCELLED],
            self::STATUS_SHIPPED => [self::STATUS_DELIVERED],
            self::STATUS_DELIVERED => [],
            self::STATUS_CANCELLED => [],
        ];

        return in_array($newStatus, $transitions[$this->status] ?? []);
    }

    /**
     * Calcular el total del pedido basado en items
     *
     * @return float
     */
    public function calculateTotal()
    {
        if (!$this->items) {
            return 0;
        }

        $total = 0;
        foreach ($this->items as $item) {
            $total += ($item['price'] ?? 0) * ($item['quantity'] ?? 0);
        }

        return round($total, 2);
    }
}