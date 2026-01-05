<?php

namespace App\Filament\Resources\OrderHistories\Schemas;

use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Schemas\Components\Grid;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class OrderHistoriesForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Grid::make(1)
                ->columnSpanFull()
                ->schema([
                    Section::make('Detail Riwayat Order')
                        ->schema([
                            TextInput::make('order.order_code')
                                ->required(),
                            TextInput::make('order.customer.name')
                                ->required(),
                            TextInput::make('old_status'),
                            TextInput::make('new_status'),
                            Textarea::make('note')
                                ->columnSpanFull(),
                            TextInput::make('changed_by')
                                ->numeric(),
                    ]),
                ])
            ]);
    }
}
