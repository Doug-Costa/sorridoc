<?php

namespace App\Filament\Widgets;

use Filament\Widgets\StatsOverviewWidget as BaseStatsOverview;
use App\Models\Approval;
use Filament\Widgets\StatsOverviewWidget\Stat;
use Illuminate\Support\Facades\Auth;

class StatsOverview extends BaseStatsOverview
{
    protected function getStats(): array
    {
        $user = auth()->user();
        $isSuperAdmin = $user->role === 'Super Admin';

        $query = Approval::query()
            ->when(!$isSuperAdmin, function ($q) use ($user) {
                $q->where(function ($sub) use ($user) {
                    $sub->where('assigned_to', $user->id)
                        ->orWhere('owner_id', $user->id);
                });
            });

        return [
            Stat::make('Urgentes', (clone $query)->where('status', 'Pendente')->count() . ' aprovações')
                ->description($isSuperAdmin ? 'Total pendente no sistema' : 'Pendentes para você')
                ->descriptionIcon('heroicon-m-clock')
                ->color('warning'),
            Stat::make('Docs sigilosos ativos', (clone $query)->where('sensitivity_level', 'Sigiloso')->count())
                ->description('Sob proteção LGPD')
                ->descriptionIcon('heroicon-m-shield-check')
                ->color('danger'),
            Stat::make('Tempo médio aprovação', $this->getAverageTime($query))
                ->description('Meta: 4h')
                ->descriptionIcon('heroicon-m-arrow-trending-up')
                ->color('success'),
        ];
    }

    protected function getAverageTime($query): string
    {
        $avgHours = (clone $query)->where('status', 'Aprovado')
            ->get()
            ->map(fn ($a) => $a->updated_at->diffInHours($a->created_at))
            ->average() ?? 0;

        return number_format($avgHours, 1) . 'h';
    }
}
