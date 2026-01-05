<?php

namespace App\Providers;

use Illuminate\Support\Facades\Validator;
use Illuminate\Support\ServiceProvider;
use Filament\Support\Facades\FilamentView;
use Illuminate\Support\HtmlString;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        //Order::observe(OrderObserver::class);

        Validator::resolver(function (
        $translator,
        $data,
        $rules,
        $messages,
        $attributes
        ) {
            $translator->setLocale('id');
            return new \Illuminate\Validation\Validator(
                $translator,
                $data,
                $rules,
                $messages,
                $attributes
            );
        });

        FilamentView::registerRenderHook(
        'panels::head.end',
        fn () => new HtmlString(
            '<script src="https://cdn.jsdelivr.net/npm/fullcalendar@6.1.11/index.global.min.js"></script>'
        )
    );
    }
}
