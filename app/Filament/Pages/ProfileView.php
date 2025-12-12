<?php

namespace App\Filament\Pages;

use Filament\Pages\Page;
use Filament\Infolists\Components\TextEntry;
use Illuminate\Support\Facades\Auth;
use Filament\Infolists\Concerns\InteractsWithInfolists;
use Filament\Schemas\Schema;
use Filament\Actions\EditAction;

class ProfileView extends Page
{
    use InteractsWithInfolists;

    protected static ?string $title = 'Profil';
    protected string $view = 'filament.pages.profile-view';

    // kita tidak ingin halaman ini muncul di sidebar
    public static function shouldRegisterNavigation(): bool
    {
        return false;
    }

    public function getUser()
    {
        return Auth::user();
    }

    public function profileInfolist(Schema $schema): Schema
    {
        return $schema
            // 4. TAMBAHKAN BARIS INI untuk menghubungkan data user
            ->record(Auth::user()) 
            ->schema([
                TextEntry::make('name')->label('Nama'),
                TextEntry::make('email')->label('Email'),
                TextEntry::make('role')->label('Role'),
                TextEntry::make('created_at')->label('Dibuat')->dateTime(),
            ]);
    }

}

