<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Spatie\Permission\Traits\HasRoles;
use Filament\Panel;
use Illuminate\Support\Facades\Storage;
use Filament\Models\Contracts\HasAvatar;
use Filament\Models\Contracts\FilamentUser;

class User extends Authenticatable implements FilamentUser, HasAvatar
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasFactory, Notifiable, HasRoles;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */

    const ROLE_ADMIN = 'admin';
    const ROLE_PELANGGAN = 'pelanggan';
    
    protected $fillable = [
        'name',
        'email',
        'password',
        'no_hp',
        'alamat',
        'role',
        'avatar_url',
    ];

    // User (customer) → banyak order
    public function orders()
    {
        return $this->hasMany(Order::class, 'user_id');
    }

    public function isCustomer(): bool
    {
        return $this->role === 'pelanggan';
    }

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

    /**
     * Fungsi ini menentukan siapa yang boleh masuk ke panel Filament.
     */
    public function canAccessPanel(Panel $panel): bool
    {
        // Izinkan akses jika role user adalah 'admin' ATAU 'pemilik'
        return in_array($this->role, ['admin', 'pemilik']);
    }

    
    public function getFilamentAvatarUrl(): ?string
    {
        
        return $this->avatar_url
            ? Storage::url($this->avatar_url)
            : 'https://ui-avatars.com/api/?name=' . urlencode($this->name);
    }

    protected static function booted()
    {
        static::updating(function ($user) {
            // Cek apakah avatar_url diganti
            if ($user->isDirty('avatar_url')) {

                $oldAvatar = $user->getOriginal('avatar_url');

                // Hapus file lama jika ada dan bukan null
                if ($oldAvatar && Storage::disk('public')->exists($oldAvatar)) {
                    Storage::disk('public')->delete($oldAvatar);
                }
            }
        });
    }
}
