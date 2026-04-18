<?php

namespace App\Policies;

use App\Models\Company;
use App\Models\User;

class CompanyPolicy
{
    public function viewAny(User $user): bool
    {
        return $user->hasPortalManagementAccess();
    }

    public function view(User $user, Company $company): bool
    {
        if ($user->hasPortalManagementAccess()) {
            return true;
        }

        return $user->isGestorRH() && $user->company_id === $company->id;
    }

    public function create(User $user): bool
    {
        return $user->hasPortalManagementAccess();
    }

    public function update(User $user, Company $company): bool
    {
        return $user->hasPortalManagementAccess();
    }

    public function delete(User $user, Company $company): bool
    {
        return $user->isSuperAdmin();
    }

    public function manageWorkers(User $user, Company $company): bool
    {
        if ($user->hasPortalManagementAccess()) {
            return true;
        }

        return $user->isGestorRH() && $user->company_id === $company->id;
    }

    public function generateToken(User $user, Company $company): bool
    {
        return $user->isSuperAdmin();
    }

}
