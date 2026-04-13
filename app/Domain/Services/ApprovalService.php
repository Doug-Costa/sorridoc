<?php

namespace App\Domain\Services;

use App\Models\Approval;
use App\Models\ApprovalFlow;
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

    /**
     * @throws \InvalidArgumentException
     */
    public function reject(Approval $record, string $pin, string $comment): void
    {
        $user = Auth::user();

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
