<?php

namespace App\Providers;

use Illuminate\Support\Facades\Validator;
use Illuminate\Support\ServiceProvider;

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
    }
}
