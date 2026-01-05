<?php

namespace App\Models;

use Illuminate\Support\Facades\Storage;
use Illuminate\Database\Eloquent\Model;

class PortfolioImage extends Model
{
    protected $fillable = [
        'images',
    ];
    protected $casts = [
        'images' => 'array',
    ];

    protected static function booted()
    {
        static::deleting(function ($record) {
            if (is_array($record->images)) {
                foreach ($record->images as $image) {
                    Storage::disk('public')->delete($image);
                }
            } elseif ($record->image) {
                Storage::disk('public')->delete($record->image);
            }
        });
    }
}
