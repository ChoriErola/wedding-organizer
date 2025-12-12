<?php

namespace App\Filament\Resources\Packages\Tables;

// use Filament\Actions\BulkActionGroup;
// use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\ViewAction;
use Filament\Actions\DeleteAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Columns\ImageColumn;
use Filament\Tables\Table;

class PackagesTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('name')
                    ->searchable()
                    ->label('Nama Paket'),
                // TextColumn::make('description')
                //     ->label('Deskripsi')
                //     ->searchable()
                //     ->wrap()
                //     ->limit(50) 
                //     ->tooltip(fn ($state): string => $state), 
                TextColumn::make('services.name')
                    ->label('Layanan')
                    ->columnSpanFull()
                    ->badge()
                    ->separator(',')
                    ->limitList(3)
                    ->tooltip(fn ($record) => 
                        $record->services->pluck('name')->join(', ')
                    ),
                ImageColumn::make('image')
                    ->label('Foto')
                    ->disk('public'),
                TextColumn::make('price')
                    ->searchable()
                    ->label('Harga')
                    ->formatStateUsing(function ($state) {
                        return number_format($state, 0, ',', '.');
                    }),
                TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('updated_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                //
            ])
            ->recordActions([
                ViewAction::make()->iconButton(),
                EditAction::make()->iconButton(),
                DeleteAction::make()->iconButton(),
            ])
            ->toolbarActions([
                // BulkActionGroup::make([
                //     DeleteBulkAction::make(),
                // ]),
            ]);
    }
}
