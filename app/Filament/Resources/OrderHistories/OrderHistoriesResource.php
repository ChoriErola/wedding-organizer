<?php

namespace App\Filament\Resources\OrderHistories;

use App\Filament\Resources\OrderHistories\Pages\CreateOrderHistories;
use App\Filament\Resources\OrderHistories\Pages\EditOrderHistories;
use App\Filament\Resources\OrderHistories\Pages\ListOrderHistories;
use App\Filament\Resources\OrderHistories\Schemas\OrderHistoriesForm;
use App\Filament\Resources\OrderHistories\Tables\OrderHistoriesTable;
use App\Models\OrderHistories;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;
use UnitEnum;

class OrderHistoriesResource extends Resource
{
    protected static ?string $model = OrderHistories::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedRectangleStack;

    protected static ?string $recordTitleAttribute = 'id';
    protected static ?string $pluralLabel = 'Riwayat Order';
    protected static string|UnitEnum|null $navigationGroup = 'Orders';

    public static function form(Schema $schema): Schema
    {
        return OrderHistoriesForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return OrderHistoriesTable::configure($table);
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
            'index' => ListOrderHistories::route('/'),
            'create' => CreateOrderHistories::route('/create'),
            //'edit' => EditOrderHistories::route('/{record}/edit'),
        ];
    }
}
