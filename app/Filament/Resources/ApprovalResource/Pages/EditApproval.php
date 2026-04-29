<?php

namespace App\Filament\Resources\ApprovalResource\Pages;

use App\Filament\Resources\ApprovalResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditApproval extends EditRecord
{
    protected static string $resource = ApprovalResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\ViewAction::make(),
            Actions\DeleteAction::make(),
        ];
    }

    protected function afterSave(): void
    {
        $record = $this->getRecord();
        $data = $this->form->getState();

        if ($record->flow_type === 'Dupla') {
            // Atualiza Diretor
            if ($record->assigned_to) {
                $diretor = $record->assignees()
                    ->whereHas('user', fn($q) => $q->where('role', 'Diretor'))
                    ->first();
                
                if ($diretor) {
                    $diretor->update(['user_id' => $record->assigned_to]);
                } else {
                    \App\Models\ApprovalAssignee::create([
                        'approval_id' => $record->id,
                        'user_id' => $record->assigned_to,
                        'status' => 'Pendente',
                    ]);
                }
            }

            // Atualiza Advogado
            if (!empty($data['advogado_id'])) {
                $advogado = $record->assignees()
                    ->whereHas('user', fn($q) => $q->where('role', 'Advogado'))
                    ->first();
                
                if ($advogado) {
                    $advogado->update(['user_id' => $data['advogado_id']]);
                } else {
                    \App\Models\ApprovalAssignee::create([
                        'approval_id' => $record->id,
                        'user_id' => $data['advogado_id'],
                        'status' => 'Pendente',
                    ]);
                }
            }
        }
    }
}
