<?php

namespace App\Filament\Resources\Services\Schemas;

use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Schema;

class ServicesForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('name')
                    ->required()
                    ->unique(ignoreRecord: true, table: 'services', column: 'name')
                    ->validationMessages([
                        'required' => 'Nama wajib diisi.',
                        'unique' => 'Nama Layanan ini sudah ada sebelumnya.',
                    ])
                    ->label('Nama Layanan'),
                // Textarea::make('description')
                //     ->label('Deskripsi Paket')
                //     ->nullable()
                //     ->rows(4) 
                //     ->maxLength(65535)
                //     ->columnSpanFull(),
                TextInput::make('harga_layanan')
                    ->label('Harga Layanan')
                    ->numeric()
                    ->prefix('Rp')
                    ->required(),  
                Toggle::make('is_active')
                    ->label('Aktif/Tidak Aktif')
                    ->required(),
            ]);
    }
}
