<?php

namespace App\Policies;

use App\Models\Company;
use App\Models\User;
use App\Models\Worker;

class WorkerPolicy
{
    public function viewAny(User $user, ?Company $company = null): bool
    {
        if ($user->hasPortalManagementAccess()) {
            return true;
        }

        if ($user->isGestorRH() && $company && $user->company_id === $company->id) {
            return true;
        }

        return false;
    }

    public function view(User $user, Worker $worker): bool
    {
        if ($user->hasPortalManagementAccess()) {
            return true;
        }

        if ($user->isGestorRH() && $user->company_id === $worker->company_id) {
            return true;
        }

        return false;
    }

    public function create(User $user, ?Company $company = null): bool
    {
        if ($user->hasPortalManagementAccess()) {
            return true;
        }

        if ($user->isGestorRH() && $company && $user->company_id === $company->id) {
            return true;
        }

        return false;
    }

    public function update(User $user, Worker $worker): bool
    {
        if ($user->hasPortalManagementAccess()) {
            return true;
        }

        if ($user->isGestorRH() && $user->company_id === $worker->company_id) {
            return true;
        }

        return false;
    }


    public function delete(User $user, Worker $worker): bool
    {
        return $user->isSuperAdmin();
    }

    public function manageDocuments(User $user, Worker $worker): bool
    {
        if ($user->isSuperAdmin()) {
            return true;
        }

        if ($user->isGestorRH() && $user->company_id === $worker->company_id) {
            return true;
        }

        return false;
    }

    public function accessViaToken(User $user, Worker $worker): bool
    {
        return $user->isGestorRH() && $user->company_id === $worker->company_id;
    }
}
