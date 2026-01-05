<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class OrderService extends Model
{
    protected $table = 'order_services';

    protected $fillable = [
        'order_id',
        'service_id',
        'package_id',
        'service_name',
        'price',
        'notes',
        'is_required',
        'is_custom',
    ];

    protected $casts = [
        'price' => 'decimal:2',
        'is_required' => 'boolean',
        'is_custom' => 'boolean',
    ];

    public function order()
    {
        return $this->belongsTo(Order::class);
    }

    public function package()
    {
        return $this->belongsTo(Package::class, 'package_id');
    }

    public function service()
    {
        return $this->belongsTo(Services::class);
    }

}
