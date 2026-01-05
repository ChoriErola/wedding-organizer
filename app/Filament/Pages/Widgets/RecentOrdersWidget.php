<?php

namespace App\Filament\Pages\Widgets;

use App\Models\Order;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Widgets\TableWidget as BaseWidget;

class RecentOrdersWidget extends BaseWidget
{
    protected static ?string $title = 'Order Terbaru';

    protected int | string | array $columnSpan = 'full';

    public function table(Table $table): Table
    {
        return $table
            ->query(Order::query()->latest()->limit(10))
            ->columns([
                Tables\Columns\TextColumn::make('order_code')
                    ->label('Order Code')
                    ->searchable()
                    ->copyable(),
                Tables\Columns\TextColumn::make('customer.name')
                    ->label('Customer')
                    ->searchable(),
                Tables\Columns\TextColumn::make('package.name')
                    ->label('Paket'),
                Tables\Columns\TextColumn::make('event_date')
                    ->label('Tanggal Acara')
                    ->dateTime('d M Y'),
                Tables\Columns\BadgeColumn::make('status')
                    ->label('Status')
                    ->color(fn ($state) => match ($state) {
                        'confirmed' => 'warning',
                        'paid in progress' => 'info',
                        'paid completed' => 'success',
                        'completed' => 'success',
                        'cancelled' => 'danger',
                        default => 'secondary',
                    }),
                Tables\Columns\TextColumn::make('total_price')
                    ->label('Total Harga')
                    ->formatStateUsing(fn ($state) =>
                        'Rp ' . number_format($state, 0, ',', '.')
                    )
                    ->alignEnd(),
            ])
            ->filters([
                \Filament\Tables\Filters\SelectFilter::make('status')
                    ->options([
                        'confirmed' => 'Confirmed',
                        'on progress' => 'Paid / Proses',
                        'paid' => 'Paid / Selesai',
                        'completed' => 'Completed',
                        'cancelled' => 'Cancelled',
                    ])
                    ->label('Filter by Status'),
                \Filament\Tables\Filters\SelectFilter::make('event_date')
                    ->label('Filter by Event Date')
                    ->options(function () {
                        $dates = \App\Models\Order::query()
                            ->selectRaw('DATE(event_date) as date')
                            ->distinct()
                            ->orderBy('date', 'desc')
                            ->pluck('date')
                            ->toArray();
                        $formattedDates = [];
                        foreach ($dates as $date) {
                            $formattedDates[$date] = date('d M Y', strtotime($date));
                        }
                        return $formattedDates;
                    }),
                \Filament\Tables\Filters\SelectFilter::make('customer_id')
                    ->label('Filter by Customer')
                    ->relationship('customer', 'name'),
            ])
            ->paginated(false);
    }
}
