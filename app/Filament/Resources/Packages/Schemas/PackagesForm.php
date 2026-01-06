<?php

namespace App\Filament\Resources\Packages\Schemas;

use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Textarea;
use Filament\Schemas\Schema;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\CheckboxList;

class PackagesForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('name')
                    ->label('Nama Paket')
                    ->required()
                    ->unique(ignoreRecord: true, table: 'packages', column: 'name')
                    ->validationMessages([
                        'required' => 'Nama wajib diisi.'
                    ])
                    ->maxLength(255),
                // Textarea::make('description')
                //     ->label('Deskripsi Paket')
                //     ->nullable()
                //     ->rows(4) 
                //     ->maxLength(65535)
                //     ->columnSpanFull(),
                FileUpload::make('image')
                    ->label('Foto Paket')
                    ->disk('public')
                    ->directory('packages')
                    ->image()
                    ->imageEditor()
                    ->required(),
                TextInput::make('price')
                    ->label('Harga Paket')
                    ->numeric()
                    ->prefix('Rp')
                    ->required()
                    ->columnSpan(1),  
                CheckboxList::make('services')
                    ->relationship(
                        name: 'services',
                        titleAttribute: 'name'
                    )
                    ->label('Pilih Layanan')
                    ->required()
                    ->columnSpanFull()
                    ->columns(3)
                    ->searchable(),         
            ]);
    }
}
