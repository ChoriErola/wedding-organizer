<?php

namespace App\Filament\Resources\AboutUs\Schemas;

use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Schemas\Schema;

class AboutUsForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('title')
                    ->label('Judul')
                    ->maxLength(255)
                    ->required(),
                Textarea::make('description')
                    ->label('Deskripsi')
                    ->rows(6)
                    ->required()
                    ->columnSpanFull(),
                FileUpload::make('image')
                    ->label('Gambar')
                    ->disk('public')
                    ->directory('about-us')
                    ->image()
                    ->imageEditor()
                    ->maxSize(2048)
                    ->required(),
            ]);
    }
}
