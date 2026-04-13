<?php

namespace App\Providers;

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
        if (! extension_loaded('intl')) {
            if (app()->runningInConsole()) {
                \Illuminate\Support\Facades\Log::warning("A extensão PHP 'intl' não está carregada. Algumas funcionalidades do Filament podem falhar.");
                return;
            }

            throw new \RuntimeException(
                "A extensão PHP 'intl' é obrigatória para o funcionamento do sistema SorriDoc. " .
                "Por favor, habilite 'extension=intl' no seu arquivo php.ini e reinicie o servidor."
            );
        }
    }
}
