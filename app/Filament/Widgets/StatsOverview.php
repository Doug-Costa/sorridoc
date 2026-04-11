<?php

namespace App\Filament\Widgets;

use Filament\Widgets\StatsOverviewWidget as BaseStatsOverview;
use App\Models\Approval;
use Filament\Widgets\StatsOverviewWidget\Stat;

class StatsOverview extends BaseStatsOverview
{
    protected function getStats(): array
    {
        return [
            Stat::make('Urgentes', Approval::where('status', 'Pendente')->count() . ' aprovações')
                ->description('Aproximação de prazo')
                ->descriptionIcon('heroicon-m-clock')
                ->color('warning'),
            Stat::make('Docs sigilosos ativos', Approval::where('sensitivity_level', 'Sigiloso')->count())
                ->description('Sob proteção LGPD')
                ->descriptionIcon('heroicon-m-shield-check')
                ->color('danger'),
            Stat::make('Tempo médio aprovação', $this->getAverageTime())
                ->description('Meta: 4h')
                ->descriptionIcon('heroicon-m-arrow-trending-up')
                ->color('success'),
        ];
    }

    protected function getAverageTime(): string
    {
        $avgHours = Approval::where('status', 'Aprovado')
            ->get()
            ->map(fn ($a) => $a->updated_at->diffInHours($a->created_at))
            ->average() ?? 0;

        return number_format($avgHours, 1) . 'h';
    }
}
