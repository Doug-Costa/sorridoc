<?php

namespace App\Filament\Portal\WorkerDocumentResource\Pages;

use App\Filament\Portal\WorkerDocumentResource\WorkerDocumentResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListWorkerDocuments extends ListRecords
{
    protected static string $resource = WorkerDocumentResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
