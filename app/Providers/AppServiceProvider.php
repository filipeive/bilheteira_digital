<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\URL;

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
        if (config('app.env') !== 'local') {
            URL::forceScheme('https');
        }

        \Livewire\Livewire::setUpdateRoute(function ($handle) {
            return \Illuminate\Support\Facades\Route::post('/lw-update', $handle)->middleware(['web']);
        });

        \Livewire\Livewire::setScriptRoute(function ($handle) {
            return \Illuminate\Support\Facades\Route::get('/lw-assets', $handle);
        });

        $this->app->booted(function () {
            $this->app->booted(function () {
                $routes = \Illuminate\Support\Facades\Route::getRoutes();
                if ($uploadRoute = $routes->getByName('livewire.upload-file')) {
                    $uploadRoute->setUri('lw-upload');
                }
                if ($previewRoute = $routes->getByName('livewire.preview-file')) {
                    $previewRoute->setUri('lw-preview/{filename}');
                }
                $routes->refreshNameLookups();
                $routes->refreshActionLookups();
            });
        });
    }
}
