<?php

namespace App\Filament\Pages\Widgets;

use App\Models\Order;
use Filament\Widgets\StatsOverviewWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;
use Carbon\Carbon;

class RingkasanPendapatanStats extends StatsOverviewWidget
{
    public ?string $startDate = null;
    public ?string $endDate = null;

    protected $listeners = [
        'periodeUpdated' => 'updatePeriode',
    ];

    public function updatePeriode(string $start, string $end): void
    {
        $this->startDate = $start;
        $this->endDate = $end;
    }

    protected function getStats(): array
    {
        if (! $this->startDate || ! $this->endDate) {
            return [];
        }

        $query = Order::query()
            ->whereBetween('event_date', [
                Carbon::parse($this->startDate)->startOfDay(),
                Carbon::parse($this->endDate)->endOfDay(),
            ]);

        return [
            Stat::make('Total Order', (clone $query)->count())
                ->icon('heroicon-o-document-text')
                ->color('info'),

            Stat::make(
                'Total Pendapatan',
                'Rp ' . number_format((clone $query)->sum('total_price'), 0, ',', '.')
            )
                ->icon('heroicon-o-banknotes')
                ->color('success'),

            Stat::make(
                'Pendapatan Diterima',
                'Rp ' . number_format(
                    (clone $query)
                        ->whereIn('status', ['paid completed', 'completed'])
                        ->sum('total_price'),
                    0,
                    ',',
                    '.'
                )
            )
                ->icon('heroicon-o-check-circle')
                ->color('success'),

            Stat::make(
                'Belum Lunas',
                'Rp ' . number_format(
                    (clone $query)
                        ->where('status', 'paid in progress')
                        ->sum('total_price'),
                    0,
                    ',',
                    '.'
                )
            )
                ->icon('heroicon-o-clock')
                ->color('warning'),
        ];
    }
}