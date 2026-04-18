<?php

namespace App\Policies;

use App\Models\User;

class UserPolicy
{
    public function viewAny(User $user): bool
    {
        return $user->hasPortalManagementAccess();
    }

    public function view(User $user, User $model): bool
    {
        if ($user->hasPortalManagementAccess()) {
            // Se não for super admin, só pode ver usuários do portal
            if (!$user->isSuperAdmin() && !in_array($model->role, ['Empresa', 'Funcionario'])) {
                return $user->id === $model->id;
            }
            return true;
        }
        return $user->id === $model->id;
    }

    public function create(User $user): bool
    {
        return $user->hasPortalManagementAccess();
    }

    public function update(User $user, User $model): bool
    {
        if ($user->hasPortalManagementAccess()) {
             // Se não for super admin, só pode editar usuários do portal
             if (!$user->isSuperAdmin() && !in_array($model->role, ['Empresa', 'Funcionario'])) {
                return $user->id === $model->id;
            }
            return true;
        }
        return false;
    }

    public function delete(User $user, User $model): bool
    {
        if ($user->isSuperAdmin()) return true;
        
        // Membros com permissão podem deletar apenas usuários do portal
        return $user->hasPortalManagementAccess() && in_array($model->role, ['Empresa', 'Funcionario']);
    }

}
