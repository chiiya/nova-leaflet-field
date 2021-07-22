<?php

namespace Chiiya\NovaLeafletField;

use Illuminate\Support\ServiceProvider;
use Laravel\Nova\Events\ServingNova;
use Laravel\Nova\Nova;

class FieldServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        $this->publishes([
            __DIR__ . '/../images' => public_path('vendor/nova-leaflet-field'),
        ], 'assets');

        Nova::serving(function (ServingNova $event) {
            Nova::script('leaflet-field', __DIR__.'/../dist/js/field.js');
            Nova::style('leaflet-field', __DIR__.'/../dist/css/field.css');
        });
    }

    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }
}
