<?php

namespace App\Filament\Pages;

use Filament\Pages\Dashboard as BaseDashboard;
use Filament\Actions\Action;
use App\Filament\Widgets\StatsOverview;
use App\Filament\Widgets\PendingApprovals;

class Dashboard extends BaseDashboard
{
    protected static ?string $navigationIcon = 'heroicon-o-squares-2x2';

    protected function getHeaderActions(): array
    {
        return [
            Action::make('nova_aprovacao')
                ->label('+ Nova Aprovação')
                ->url('/admin/approvals/create')
                ->button()
                ->color('white')
                ->extraAttributes(['class' => 'bg-white text-gray-900 border-gray-300 shadow-sm']),
            Action::make('gerir_aprovacoes')
                ->label('Gerir Todas')
                ->url('/admin/approvals')
                ->button()
                ->color('white')
                ->extraAttributes(['class' => 'bg-white text-gray-900 border-gray-300 shadow-sm']),
        ];
    }

    public function getWidgets(): array
    {
        return [
            StatsOverview::class,
            PendingApprovals::class,
        ];
    }
}
