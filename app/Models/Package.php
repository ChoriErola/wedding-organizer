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
            'packages_services', 
            'package_id',        
            'service_id'        
        )
        ->withPivot(['value_price', 'is_required'])
        ->withTimestamps();
    }

    public function orderServices()
    {
        return $this->hasMany(OrderService::class);
    }
}