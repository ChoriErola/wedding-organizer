<?php

namespace App\Filament\Pages;

use App\Filament\Pages\Widgets\DashboardStats;
use App\Filament\Pages\Widgets\RecentOrdersWidget;
use App\Filament\Widgets\EventCalendarWidget;
use Filament\Pages\Dashboard as BaseDashboard;
use Filament\Widgets\Widget;

class Dashboard extends BaseDashboard
{
    protected static string $routePath = 'dashboard';
    
    protected static ?string $title = 'Dashboard';

    /**
     * @return array<class-string<Widget>>
     */
    public function getWidgets(): array
    {
        return [
            EventCalendarWidget::class,
            DashboardStats::class,
            RecentOrdersWidget::class,
        ];
    }
}
