<?php

namespace App\Filament\Pages;

use Filament\Pages\Page;

class LGPD extends Page
{
    protected static ?string $navigationIcon = 'heroicon-o-shield-check';
    protected static ?string $navigationGroup = 'CONFIGURAÇÕES';
    protected static ?int $navigationSort = 2;
    protected static ?string $navigationLabel = 'LGPD & Segurança';
    protected static string $view = 'filament.pages.lgpd';
}
