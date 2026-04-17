<?php

namespace App\Filament\Portal\WorkerResource\Pages;

use App\Filament\Portal\WorkerResource\WorkerResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListWorkers extends ListRecords
{
    protected static string $resource = WorkerResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
