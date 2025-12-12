<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PackagesServices extends Model
{
    protected $table = 'packages_services';

    protected $fillable = [
        'package_id',
        'service_id',
        'value_price',
        'is_required',
    ];
}
