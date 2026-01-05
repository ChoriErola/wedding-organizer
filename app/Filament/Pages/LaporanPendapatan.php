<?php

namespace App\Filament\Pages;

use Filament\Pages\Page;
use Filament\Forms;
use Filament\Forms\Concerns\InteractsWithForms;
use App\Models\Order;
use Barryvdh\DomPDF\Facade\Pdf;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Concerns\InteractsWithTable;
use Filament\Tables\Contracts\HasTable;
use Carbon\Carbon;
use BackedEnum;
use UnitEnum;

class LaporanPendapatan extends Page implements HasTable
{
    use InteractsWithForms;
    use InteractsWithTable;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedDocumentChartBar;
    protected static ?string $navigationLabel = 'Laporan Pendapatan';
    protected static string|UnitEnum|null $navigationGroup = 'Orders';
    protected static ?string $title = 'Laporan Pendapatan';
    protected string $view = 'filament.pages.laporan-pendapatan';

    public ?array $data = [];
    
    protected function getFormStatePath(): string
    {
        return 'data';
    }

    public array $result = [];
    
    public $orders = [];

    public function mount(): void
    {
        $this->form->fill([
            'start_date' => now()->startOfMonth()->toDateString(),
            'end_date'   => now()->toDateString(),
        ]);

        $this->generate();
    }

    protected function getFormSchema(): array
    {
        return [
            Forms\Components\DatePicker::make('start_date')
                ->label('Periode Mulai')
                ->required(),

            Forms\Components\DatePicker::make('end_date')
                ->label('Periode Selesai')
                ->required(),
        ];
    }

    public function generate(): void
    {
        $this->resetTable();
        $state = $this->data;

        if (empty($state['start_date']) || empty($state['end_date'])) {
            return;
        }

        $query = Order::query()
            ->whereBetween('event_date', [
                Carbon::parse($state['start_date'])->startOfDay(),
                Carbon::parse($state['end_date'])->endOfDay(),
            ]);

        $this->result = [
            'total_orders' => (clone $query)->count(),

            'total_paid' => (clone $query)
                ->where('status', 'paid completed')
                ->sum('total_price'),

            'total_unpaid' => (clone $query)
                ->where('status', 'paid in progress')
                ->sum('total_price'),
        ];

        $this->orders = (clone $query)
            ->with('customer')
            ->orderBy('event_date')
            ->get();
        $this->dispatch('periodeUpdated', 
            $this->data['start_date'], 
            $this->data['end_date']
        );
    }

    public function exportPdf()
    {
        $state = $this->data;

        if (empty($state['start_date']) || empty($state['end_date'])) {
            // Optional: tangani jika tanggal belum dipilih
            return redirect()->back()->with('error', 'Periode belum dipilih.');
        }
        $start = Carbon::parse($state['start_date'])->startOfDay();
        $end   = Carbon::parse($state['end_date'])->endOfDay();

        $orders = Order::with('customer')
            ->whereBetween('event_date', [$start, $end])
            ->orderBy('event_date')
            ->get();
            
        $summary = [
            'total_orders' => $orders->count(),
            'total_paid' => $orders
                ->whereIn('status', ['paid completed', 'completed'])
                ->sum('total_price'),
            'total_unpaid' => $orders
                ->where('status', 'paid in progress')
                ->sum('total_price'),
        ];

        $pdf = Pdf::loadView('pdf.laporan-pendapatan', [
            'start'   => $start->toDateString(),
            'end'     => $end->toDateString(),
            'summary' => $summary,
            'orders'  => $orders,
        ]);

        // Kembalikan stream download agar browser membukanya sebagai PDF
        return response()->streamDownload(
            fn () => print($pdf->output()),
            "laporan-pendapatan-{$start->toDateString()}.pdf"
        );
    }

    protected function getTableQuery()
    {
        if (
            empty($this->data['start_date']) ||
            empty($this->data['end_date'])
        ) {
            return Order::query()->whereRaw('1=0');
        }

        return Order::query()
            ->with('customer')
            ->whereBetween('event_date', [
                Carbon::parse($this->data['start_date'])->startOfDay(),
                Carbon::parse($this->data['end_date'])->endOfDay(),
            ]);
    }

    protected function getTableColumns(): array
    {
        return [
            TextColumn::make('order_code')
                ->label('Kode Order')
                ->searchable(),

            TextColumn::make('customer.name')
                ->label('Pelanggan')
                ->searchable(),

            TextColumn::make('event_date')
                ->label('Tanggal Acara')
                ->date('d M Y')
                ->sortable(),

            TextColumn::make('status')
                ->badge()
                ->colors([
                    'warning' => 'confirmed',
                    'success' => ['paid completed', 'completed'],
                    'danger'  => 'paid in progress',
                ]),

            TextColumn::make('total_price')
                ->label('Total')
                ->money('IDR', true)
                ->formatStateUsing(fn ($state) =>
                        'Rp ' . number_format($state, 0, ',', '.')
                    )
                ->sortable(),
        ];
    }

    protected function getTableDefaultSortColumn(): ?string
    {
        return 'event_date';
    }

    protected function getHeaderWidgets(): array
    {
        return [
            \App\Filament\Pages\Widgets\RingkasanPendapatanStats::make([
                'startDate' => $this->data['start_date'] ?? null,
                'endDate'   => $this->data['end_date'] ?? null,
            ]),
        ];
    }

}
