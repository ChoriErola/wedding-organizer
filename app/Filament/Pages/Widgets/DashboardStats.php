<?php

namespace App\Filament\Pages\Widgets;

use App\Models\Order;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class DashboardStats extends BaseWidget
{
    protected function getStats(): array
    {
        $totalOrders = Order::count();
        $totalRevenue = Order::sum('total_price');
        $paidOrders = Order::whereIn('status', ['paid completed', 'completed'])->count();
        $pendingOrders = Order::whereIn('status', ['confirmed', 'paid in progress'])->count();

        return [
            Stat::make('Total Order', $totalOrders)
                ->description('Semua order yang telah dibuat')
                ->icon('heroicon-o-document-text')
                ->color('info'),
            
            Stat::make('Total Pendapatan', 'Rp ' . number_format($totalRevenue, 0, ',', '.'))
                ->description('Total harga dari semua order')
                ->icon('heroicon-o-banknotes')
                ->color('success'),
            
            Stat::make('Order Terbayar', $paidOrders)
                ->description('Order dengan status paid completed / completed')
                ->icon('heroicon-o-check-circle')
                ->color('success'),
            
            Stat::make('Order Tertunda', $pendingOrders)
                ->description('Order dengan status confirmed / paid in progress')
                ->icon('heroicon-o-clock')
                ->color('warning'),
        ];
    }
}
