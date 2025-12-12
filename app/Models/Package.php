<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\Services;

class Package extends Model
{
    protected $fillable = [
        'name',
        'image',
        'price',
    ];

    public function services()
    {
        return $this->belongsToMany(
            Services::class,
            'packages_services', // ✅ NAMA TABEL PIVOT
            'package_id',        // ✅ FK di pivot ke packages
            'service_id'         // ✅ FK di pivot ke services
        )
        ->withPivot(['value_price', 'is_required'])
        ->withTimestamps();
    }

    public function orderServices()
    {
        return $this->hasMany(OrderService::class);
    }
}