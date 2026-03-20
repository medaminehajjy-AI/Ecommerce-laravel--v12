<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Order extends Model
{
    protected $fillable = [
        'user_id',
        'total_price',
        'status',
    ];

    protected $casts = [
        'total_price' => 'decimal:2',
    ];

    const STATUS_PENDING = 'pending';
    const STATUS_PAID = 'paid';
    const STATUS_PROCESSING = 'processing';
    const STATUS_SHIPPING = 'shipping';
    const STATUS_DELIVERED = 'delivered';
    const STATUS_CANCELED = 'canceled';

    const VALID_STATUSES = [
        self::STATUS_PENDING,
        self::STATUS_PAID,
        self::STATUS_PROCESSING,
        self::STATUS_SHIPPING,
        self::STATUS_DELIVERED,
        self::STATUS_CANCELED,
    ];

    const STATUS_TRANSITIONS = [
        self::STATUS_PENDING => [self::STATUS_PAID, self::STATUS_CANCELED],
        self::STATUS_PAID => [self::STATUS_PROCESSING, self::STATUS_CANCELED],
        self::STATUS_PROCESSING => [self::STATUS_SHIPPING],
        self::STATUS_SHIPPING => [self::STATUS_DELIVERED],
        self::STATUS_DELIVERED => [],
        self::STATUS_CANCELED => [],
    ];

    const REVENUE_STATUSES = [
        self::STATUS_PAID,
        self::STATUS_PROCESSING,
        self::STATUS_SHIPPING,
        self::STATUS_DELIVERED,
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function orderItems()
    {
        return $this->hasMany(OrderItem::class);
    }

    public function items()
    {
        return $this->hasMany(OrderItem::class);
    }

    public function canTransitionTo(string $newStatus): bool
    {
        if (!in_array($newStatus, self::VALID_STATUSES)) {
            return false;
        }

        $allowedTransitions = self::STATUS_TRANSITIONS[$this->status] ?? [];

        return in_array($newStatus, $allowedTransitions);
    }

    public function transitionTo(string $newStatus): bool
    {
        if (!$this->canTransitionTo($newStatus)) {
            return false;
        }

        $this->status = $newStatus;
        return $this->save();
    }

    public function isRevenueCountable(): bool
    {
        return in_array($this->status, self::REVENUE_STATUSES);
    }

    public function scopeRevenueCountable($query)
    {
        return $query->whereIn('status', self::REVENUE_STATUSES);
    }
}
