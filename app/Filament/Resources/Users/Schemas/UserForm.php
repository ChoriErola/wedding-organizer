<?php

namespace App\Filament\Resources\Users\Schemas;

use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Schemas\Schema;

class UserForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('name')
                    ->label('Nama')
                    ->required()
                    ->minLength(3),
                TextInput::make('email')
                    ->label('Email Address')
                    ->email()
                    ->required()
                    ->unique(ignoreRecord: true, table: 'users', column: 'email')
                    // ->requiredWithout('no_hp')
                    ->validationMessages([
                        'unique' => 'Email ini sudah terdaftar.',
                        'email' => 'Format email tidak valid.',
                    ]),
                TextInput::make('no_hp')
                    // ->requiredWithout('email')
                    ->required()
                    ->unique(ignoreRecord: true, table: 'users', column: 'no_hp')
                    ->label('Nomor Handphone')
                    ->validationMessages([
                        'unique' => 'Nomor handphone ini sudah terdaftar.',
                    ]),
                Textarea::make('alamat')
                    ->columnSpanFull(),
                TextInput::make('password')
                    ->password()
                    ->dehydrated(fn ($state) => filled($state)), // Only save password if it's filled               
                TextInput::make('role')
                    ->required()
                    ->default('pelanggan'),
            ]);
    }
}
