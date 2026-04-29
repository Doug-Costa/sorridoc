<?php

namespace App\Filament\Resources\ApprovalResource\Pages;

use App\Filament\Resources\ApprovalResource;
use Filament\Resources\Pages\CreateRecord;

class CreateApproval extends CreateRecord
{
    protected static string $resource = ApprovalResource::class;

    protected function afterCreate(): void
    {
        $record = $this->getRecord();
        $data = $this->form->getState();

        if ($record->flow_type === 'Dupla') {
            if ($record->assigned_to) {
                \App\Models\ApprovalAssignee::create([
                    'approval_id' => $record->id,
                    'user_id' => $record->assigned_to,
                    'status' => 'Pendente',
                ]);
            }

            if (!empty($data['advogado_id'])) {
                \App\Models\ApprovalAssignee::create([
                    'approval_id' => $record->id,
                    'user_id' => $data['advogado_id'],
                    'status' => 'Pendente',
                ]);
            }
        }
    }
}
