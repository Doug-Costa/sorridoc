<?php

namespace App\Filament\Pages;

use Filament\Pages\Page;

class Fluxos extends Page
{
    protected static ?string $navigationIcon = 'heroicon-o-share';
    protected static ?string $navigationGroup = 'PRINCIPAL';
    protected static ?int $navigationSort = 4;
    protected static ?string $navigationLabel = 'Fluxos';
    protected static string $view = 'filament.pages.fluxos';
}
