<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Services extends Model
{
    protected $fillable = [
        'name',
        'description',
        'harga_layanan',
        'is_active',
    ];

    public function packages()
    {
        return $this->belongsToMany(
            Package::class,
            'packages_services',
            'service_id',
            'package_id'
        )
        ->withPivot(['value_price', 'is_required'])
        ->withTimestamps();
    }
}
