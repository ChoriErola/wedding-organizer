<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class OrderHistories extends Model
{
    protected $table = 'order_histories';

    protected $fillable = [
        'order_id',
        'old_status',
        'new_status',
        'note',
        'changed_by',
    ];  

    public function order()
    {
        return $this->belongsTo(Order::class, 'order_id');
    }   

    public function changer()
    {
        return $this->belongsTo(User::class, 'changed_by');
    }
}
