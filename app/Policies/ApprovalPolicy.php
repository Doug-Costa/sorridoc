<?php

namespace App\Policies;

use App\Models\Approval;
use App\Models\User;

class ApprovalPolicy
{
    /**
     * Determine whether the user can view any models.
     */
    public function viewAny(User $user): bool
    {
        return true; // Todos podem ver a listagem por enquanto
    }

    /**
     * Determine whether the user can view the model.
     */
    public function view(User $user, Approval $approval): bool
    {
        // Administradores, Advogados e Diretores veem tudo. 
        // Operacional vê apenas o que solicitou.
        if (in_array($user->role, ['Super Admin', 'Advogado', 'Diretor'])) {
            return true;
        }

        return $user->id === $approval->owner_id;
    }

    /**
     * Determine whether the user can create models.
     */
    public function create(User $user): bool
    {
        return true; // Todos podem solicitar aprovações
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user, Approval $approval): bool
    {
        // Apenas o dono pode editar se estiver pendente, ou Super Admin
        if ($user->role === 'Super Admin') {
            return true;
        }

        return $user->id === $approval->owner_id && $approval->status === 'Pendente';
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, Approval $approval): bool
    {
        return $user->role === 'Super Admin';
    }

    /**
     * Determine whether the user can restore the model.
     */
    public function restore(User $user, Approval $approval): bool
    {
        return $user->role === 'Super Admin';
    }

    /**
     * Determine whether the user can permanently delete the model.
     */
    public function forceDelete(User $user, Approval $approval): bool
    {
        return $user->role === 'Super Admin';
    }
}
