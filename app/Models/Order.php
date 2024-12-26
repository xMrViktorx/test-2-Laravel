<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Order extends Model
{
    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = ['order_date', 'channel', 'sku', 'item_description', 'origin', 'so_num', 'cost', 'shipping_cost', 'total_price'];

    /**
     * Get all audit logs for the order.
     */
    public function auditLogs()
    {
        return $this->morphMany(AuditLog::class, 'auditable');
    }
}
