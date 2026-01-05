<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;

class AboutUs extends Model
{
    protected $table = 'about_us';
    
    protected $fillable = [
        'title',
        'description',
        'image',
    ];

    // jika multiple images
    // protected $casts = [
    //     'image' => 'array', 
    // ];

    protected static function booted()
    {
        static::updating(function ($about) {
            if ($about->isDirty('image')) {
                Storage::disk('public')->delete($about->getOriginal('image'));
            }
        });

        static::deleting(function ($about) {
            Storage::disk('public')->delete($about->image);
        });
    }
}
