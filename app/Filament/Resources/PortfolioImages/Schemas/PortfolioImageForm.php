<?php

namespace App\Filament\Resources\PortfolioImages\Schemas;

use Filament\Forms\Components\FileUpload;
use Filament\Schemas\Schema;

class PortfolioImageForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                FileUpload::make('images')
                    ->label('Portofolio Images')
                    ->multiple()
                    ->image()
                    ->disk('public')
                    ->directory('portfolios')
                    ->reorderable()
                    ->imagePreviewHeight('150')
                    ->panelLayout('grid')
                    ->required(),
                ]);
    }
}
