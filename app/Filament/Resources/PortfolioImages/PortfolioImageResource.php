<?php

namespace App\Filament\Resources\PortfolioImages;

use App\Filament\Resources\PortfolioImages\Pages\CreatePortfolioImage;
use App\Filament\Resources\PortfolioImages\Pages\EditPortfolioImage;
use App\Filament\Resources\PortfolioImages\Pages\ListPortfolioImages;
use App\Filament\Resources\PortfolioImages\Schemas\PortfolioImageForm;
use App\Filament\Resources\PortfolioImages\Tables\PortfolioImagesTable;
use App\Models\PortfolioImage;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;
use UnitEnum;

class PortfolioImageResource extends Resource
{
    protected static ?string $model = PortfolioImage::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedPhoto;
    
    protected static ?string $recordTitleAttribute = 'image';
    protected static string|UnitEnum|null $navigationGroup = 'Content';
    protected static ?string $navigationLabel = 'Portfolio';

    public static function form(Schema $schema): Schema
    {
        return PortfolioImageForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return PortfolioImagesTable::configure($table);
    }
    
    public static function getRelations(): array
    {
        return [
            //
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => ListPortfolioImages::route('/'),
            'create' => CreatePortfolioImage::route('/create'),
            'edit' => EditPortfolioImage::route('/{record}/edit'),
        ];
    }
}
