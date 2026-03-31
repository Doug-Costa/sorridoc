<?php

namespace App\Policies;

use App\Models\ApprovalFlow;
use App\Models\User;

class ApprovalFlowPolicy
{
    public function viewAny(User $user): bool
    {
        return $user->role !== 'Operacional';
    }

    public function view(User $user, ApprovalFlow $approvalFlow): bool
    {
        return $user->role !== 'Operacional';
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
