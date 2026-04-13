<?php

namespace App\Policies;

use App\Models\ApprovalFlow;
use App\Models\User;

class ApprovalFlowPolicy
{
    public function viewAny(User $user): bool
    {
        return true; // Filtering should be handled by the resource/approval relationship
    }

    public function view(User $user, ApprovalFlow $approvalFlow): bool
    {
        if ($user->role === 'Super Admin') {
            return true;
        }

        $approval = $approvalFlow->approval;
        return $user->id === $approval->owner_id || $user->id === $approval->assigned_to;
    }

    public function create(User $user): bool
    {
        return in_array($user->role, ['Super Admin', 'Advogado']);
    }

    public function update(User $user, ApprovalFlow $approvalFlow): bool
    {
        return in_array($user->role, ['Super Admin', 'Advogado']);
    }

    public function delete(User $user, ApprovalFlow $approvalFlow): bool
    {
        return in_array($user->role, ['Super Admin', 'Advogado']);
    }
}
