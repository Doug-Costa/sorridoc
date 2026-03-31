<?php

namespace App\Filament\Resources\ApprovalResource\Pages;

use App\Filament\Resources\ApprovalResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;
use Filament\Resources\Components\Tab;
use Illuminate\Database\Eloquent\Builder;

class ListApprovals extends ListRecords
{
    protected static string $resource = ApprovalResource::class;

    public ?array $data = [];

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make()->label('+ Nova Aprovação'),
        ];
    }

    public function getTabs(): array
    {
        return [
            'todos' => Tab::make('Todas'),
            'acordos' => Tab::make('Acordos/Ações')
                ->modifyQueryUsing(fn (Builder $query) => $query->where('category', 'Acordo')),
            'contratos' => Tab::make('Contratos')
                ->modifyQueryUsing(fn (Builder $query) => $query->where('category', 'Contrato')),
            'pendentes' => Tab::make('Pendentes')
                ->modifyQueryUsing(fn (Builder $query) => $query->where('status', 'Pendente')),
        ];
    }
}
