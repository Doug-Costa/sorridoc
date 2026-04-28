<?php

namespace App\Domain\Services;

use App\Models\Approval;
use App\Models\ApprovalFlow;
use App\Models\ApprovalAssignee;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\RateLimiter;

class ApprovalService
{
    /**
     * @throws \InvalidArgumentException|\RuntimeException
     */
    public function approve(Approval $record, string $pin, ?string $comment): void
    {
        $user = Auth::user();

        if ($record->flow_type === 'Múltipla') {
            $this->approveMultiple($record, $user, $pin, $comment);
        } else {
            $this->approveTraditional($record, $user, $pin, $comment);
        }
    }

    private function approveMultiple(Approval $record, $user, string $pin, ?string $comment): void
    {
        // Verificar se usuário está na lista de aprovadores
        $assignee = $record->assignees()->where('user_id', $user->id)->first();
        if (!$assignee) {
            throw new \RuntimeException('Você não está na lista de aprovadores para este documento.');
        }

        // Verificar se já aprovou
        if ($assignee->status === 'Aprovado') {
            throw new \RuntimeException('Você já aprovou este documento.');
        }

        // Verificar se já rejeitou
        if ($assignee->status === 'Rejeitado') {
            throw new \RuntimeException('Você já rejeitou este documento.');
        }

        // Validar PIN
        $this->validatePin($user, $pin);

        // Atualizar status do aprovador
        $assignee->update([
            'status' => 'Aprovado',
            'approved_at' => now(),
            'comment' => $comment,
            'signature_hash' => $this->generateSignatureHash($record, $pin),
        ]);

        // Verificar se todos aprovaram
        $this->updateMultipleApprovalStatus($record);
    }

    private function approveTraditional(Approval $record, $user, string $pin, ?string $comment): void
    {
        // 1. Assignment & Flow Controls
        $approvalCount = $record->approvalFlows()->where('status', 'Aprovado')->count();

        if ($approvalCount === 0) {
            // 1st Signature (or Simple Flow): Must be the assigned user or Super Admin
            if ($user->id !== $record->assigned_to && $user->role !== 'Super Admin') {
                throw new \RuntimeException('Apenas o usuário atribuído ou um Super Admin pode realizar esta assinatura.');
            }
        } else {
            // 2nd Signature (Double Flow): Must be Advogado or Super Admin
            if ($record->flow_type === 'Dupla') {
                if ($user->role !== 'Advogado' && $user->role !== 'Super Admin') {
                    throw new \RuntimeException('A assinatura final de um fluxo duplo deve ser realizada por uma Advogada.');
                }

                if ($record->approvalFlows()->where('assigned_to', $user->id)->exists()) {
                    throw new \RuntimeException('Você já assinou este documento. Um fluxo duplo exige dois assinantes diferentes.');
                }
            } else {
                throw new \RuntimeException('Este documento já foi totalmente aprovado.');
            }
        }

        // 2. PIN Validation with Rate Limiting (Phase 3)
        $this->validatePin($user, $pin);

        // 3. Determine Target Status
        $targetStatus = 'Aprovado';
        if ($record->flow_type === 'Dupla' && $approvalCount === 0) {
            $targetStatus = 'Em Aprovação';
        }

        // 4. Create Flow Record
        ApprovalFlow::create([
            'approval_id' => $record->id,
            'step_name' => ($approvalCount === 0 && $record->flow_type === 'Dupla') ? '1ª Aprovação (Diretor)' : 'Aprovação Final',
            'status' => 'Aprovado',
            'comment' => $comment,
            'action_type' => 'Aprovação',
            'assigned_to' => $user->id,
            'approved_at' => now(),
            'signature_hash' => $this->generateSignatureHash($record, $pin),
        ]);

        // 5. Update Record
        $record->update(['status' => $targetStatus]);
    }

    private function updateMultipleApprovalStatus(Approval $record): void
    {
        $progress = $record->getApprovalProgress();
        
        if ($progress['rejected'] > 0) {
            $record->update(['status' => 'Rejeitado']);
        } elseif ($progress['approved'] === $progress['total'] && $progress['total'] > 0) {
            $record->update(['status' => 'Aprovado']);
        } elseif ($progress['approved'] > 0) {
            $record->update(['status' => 'Em Aprovação']);
        }
    }

    /**
     * @throws \InvalidArgumentException
     */
    public function reject(Approval $record, string $pin, string $comment): void
    {
        $user = Auth::user();

        if ($record->flow_type === 'Múltipla') {
            $this->rejectMultiple($record, $user, $pin, $comment);
        } else {
            $this->rejectTraditional($record, $user, $pin, $comment);
        }
    }

    private function rejectMultiple(Approval $record, $user, string $pin, string $comment): void
    {
        // Verificar se usuário está na lista de aprovadores
        $assignee = $record->assignees()->where('user_id', $user->id)->first();
        if (!$assignee) {
            throw new \RuntimeException('Você não está na lista de aprovadores para este documento.');
        }

        // Verificar se já rejeitou
        if ($assignee->status === 'Rejeitado') {
            throw new \RuntimeException('Você já rejeitou este documento.');
        }

        // Validar PIN
        $this->validatePin($user, $pin);

        // Atualizar status do aprovador
        $assignee->update([
            'status' => 'Rejeitado',
            'approved_at' => now(),
            'comment' => $comment,
            'signature_hash' => $this->generateSignatureHash($record, $pin, true),
        ]);

        // Rejeição de um aprovador rejeita todo o documento
        $record->update(['status' => 'Rejeitado']);
    }

    private function rejectTraditional(Approval $record, $user, string $pin, string $comment): void
    {
        // 1. PIN Validation
        $this->validatePin($user, $pin);

        // 2. Create Rejection Record
        ApprovalFlow::create([
            'approval_id' => $record->id,
            'step_name' => 'Rejeição',
            'status' => 'Rejeitado',
            'comment' => $comment,
            'action_type' => 'Rejeição',
            'assigned_to' => $user->id,
            'approved_at' => now(),
            'signature_hash' => $this->generateSignatureHash($record, $pin, true),
        ]);

        // 3. Update Record
        $record->update(['status' => 'Rejeitado']);
    }

    /**
     * @throws \InvalidArgumentException
     */
    protected function validatePin($user, string $pin): void
    {
        $key = 'pin-attempt:' . $user->id;

        if (RateLimiter::tooManyAttempts($key, 5)) {
            $seconds = RateLimiter::availableIn($key);
            throw new \RuntimeException("Muitas tentativas. Tente novamente em {$seconds} segundos.");
        }

        if (!Hash::check($pin, $user->pin_code)) {
            RateLimiter::hit($key, 60);
            throw new \InvalidArgumentException('O PIN informado é inválido. A ação não pôde ser concluída.');
        }

        RateLimiter::clear($key);
    }

    protected function generateSignatureHash(Approval $record, string $pin, bool $isRejection = false): string
    {
        if ($isRejection) {
            return hash('sha256', 'REJETED' . $pin . ($record->hash_sha256 ?? 'no-file') . now());
        }

        return hash('sha256', $pin . ($record->hash_sha256 ?? 'no-file') . request()->ip() . now());
    }
}
