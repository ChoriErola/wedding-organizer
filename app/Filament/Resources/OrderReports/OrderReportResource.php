<?php

namespace App\Filament\Resources\OrderReports;

use App\Filament\Resources\OrderReports\Pages\ManageOrderReports;
use App\Models\Order;
use App\Models\OrderReport;
use BackedEnum;
use Filament\Actions\Action;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Forms\Components\TextInput;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Columns\BadgeColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use UnitEnum;

class OrderReportResource extends Resource
{
    protected static ?string $model = Order::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedDocumentChartBar;

    protected static ?string $recordTitleAttribute = 'order_code';
    protected static string|UnitEnum|null $navigationGroup = 'Orders';
    protected static ?string $navigationLabel = 'Laporan Order';

    public static function canCreate(): bool { return false; }
    public static function canEdit($record): bool { return false; }
    public static function canDelete($record): bool { return false; }

    public static function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('order_code')
                    ->required()
                    ->maxLength(255),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('order_code')
            ->query(Order::query()->with('customer', 'package', 'services'))
            ->columns([
                TextColumn::make('customer.name')
                    ->label('Customer')
                    ->searchable(),
                TextColumn::make('order_code')
                    ->searchable(),
                TextColumn::make('event_date')
                    ->label('Tanggal Order')
                    ->date('d M Y')
                    ->sortable(),
                TextColumn::make('package.name')
                    ->label('Paket')
                    ->searchable(),
                TextColumn::make('total_price')
                    ->label('Total Harga')
                    ->money('IDR')
                    ->formatStateUsing(fn ($state) =>
                        'Rp ' . number_format($state, 0, ',', '.')
                    )
                    ->sortable(),
                // belum selesai logika status pembayaran
                BadgeColumn::make('status')
                    ->label('Status Pembayaran')
                    ->colors([
                        'danger'  => 'confirmed',
                        'warning' => 'paid in progress',
                        'success' => 'paid completed',
                    ])
                    ->formatStateUsing(fn ($state) => match ($state) {
                        'confirmed' => 'Unpaid',
                        'paid in progress' => 'Paid In Progress',
                        'paid completed' => 'Paid Completed',
                        'completed' => 'Completed',
                        'cancelled' => 'Cancelled',
                        default => ucfirst($state),
                    }),

            ])
            ->filters([
                SelectFilter::make('status')
                    ->label('Status Order')
                    ->options([
                        'confirmed' => 'Confirmed',
                        'paid in progress' => 'Paid in Progress',
                        'paid completed' => 'Paid Completed',
                        'completed' => 'Completed',
                        'cancelled' => 'Cancelled',
                    ]),

                // TextColumn::make('services_count')
                //     ->label('Detail Layanan')
                //     ->formatStateUsing(function (Order $record) {
                //         if ($record->services->isEmpty()) {
                //             return 'Tidak ada Layanan';
                //         }
                //         return $record->services
                //             ->map(function ($service) {
                //                 $label = $service->service->name;
                //                 $price = number_format($service->price, 0, ',', '.');
                                
                //                 if ($service->is_custom) {
                //                     return "{$label} (Rp {$price})";
                //                 }

                //                 return "{$label} (Rp {$price})";
                //             })
                //             ->implode("\n");
                //     })
                //     ->listWithLineBreaks()
                //     ->wrap(),
            ])
            ->actions([
                Action::make('viewInvoice')
                    ->icon('heroicon-o-clipboard-document')
                    ->iconButton()
                    ->color('info')
                    ->tooltip('Lihat Invoice')
                    ->url(fn (Order $record) => route('order_invoice', $record))
                    ->openUrlInNewTab(),
            ])
            ->toolbarActions([
                // BulkActionGroup::make([
                //     DeleteBulkAction::make(),
                // ]),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => ManageOrderReports::route('/'),
        ];
    }
}
