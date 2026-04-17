<?php

namespace App\Providers;

use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        //
    }

    public function boot(): void
    {
        if (! extension_loaded('intl')) {
            if (app()->runningInConsole()) {
                Log::warning("A extensão PHP 'intl' não está carregada. Algumas funcionalidades do Filament podem falhar.");

                return;
            }

            throw new \RuntimeException(
                "A extensão PHP 'intl' é obrigatória para o funcionamento do sistema SorriDoc. ".
                "Por favor, habilite 'extension=intl' no seu arquivo php.ini e reinicie o servidor."
            );
        }

        $this->loadRoutes();
    }

    protected function loadRoutes(): void
    {
        Route::middleware('web')
            ->group(base_path('routes/portals.php'));
    }
}
