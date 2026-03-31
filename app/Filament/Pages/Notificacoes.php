<?php

namespace App\Filament\Pages;

use Filament\Pages\Page;

class Notificacoes extends Page
{
    protected static ?string $navigationIcon = 'heroicon-o-bell';
    protected static ?string $navigationGroup = 'CONFIGURAÇÕES';
    protected static ?int $navigationSort = 3;
    protected static ?string $navigationLabel = 'Notificações';
    protected static string $view = 'filament.pages.notificacoes';

    public static function getNavigationBadge(): ?string
    {
        return '3'; // Hardcoded as per Image 1 for UI prototype
    }
}
