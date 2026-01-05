<?php

namespace App\Filament\Widgets;

use App\Models\Order;
use Filament\Widgets\Widget;
use Illuminate\Support\Facades\Auth;

class EventCalendarWidget extends Widget
{
    protected string $view = 'filament.widgets.event-calendar-widget';

    protected int | string | array $columnSpan = 'full';

    protected function getViewData(): array
    {
            $events = Order::whereNotNull('event_date')
            ->get()
            ->map(function ($order) {
                return [
                    'title' => $order->acara ?? $order->order_code,
                    'start' => $order->event_date->toDateString(),
                    'backgroundColor' => '#2563eb', 
                    'borderColor'     => '#2563eb',
                    'textColor'       => '#ffffff',

                    // 🔗 redirect hanya untuk admin / pemilik
                    'url' => Auth::user()?->role === 'pelanggan'
                        ? null
                        : route('filament.panel.resources.orders.edit', $order),
                    ];
            })
            ->values()
            ->toArray();

        return [
            'events' => $events,
        ];
    }
}
