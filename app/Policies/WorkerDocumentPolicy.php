<?php

namespace App\Policies;

use App\Models\User;
use App\Models\Worker;
use App\Models\WorkerDocument;

class WorkerDocumentPolicy
{
    public function viewAny(User $user, ?Worker $worker = null): bool
    {
        if ($user->isSuperAdmin()) {
            return true;
        }

        if ($worker && $user->isGestorRH() && $user->company_id === $worker->company_id) {
            return true;
        }

        return false;
    }

    public function view(User $user, WorkerDocument $document): bool
    {
        if ($user->isSuperAdmin()) {
            return true;
        }

        if ($user->isGestorRH() && $user->company_id === $document->worker->company_id) {
            return true;
        }

        return false;
    }

    public function create(User $user, ?Worker $worker = null): bool
    {
        if ($user->isSuperAdmin()) {
            return true;
        }

        if ($user->isGestorRH() && $worker && $user->company_id === $worker->company_id) {
            return true;
        }

        return false;
    }

    public function update(User $user, WorkerDocument $document): bool
    {
        if ($user->isSuperAdmin()) {
            return true;
        }

        if ($user->isGestorRH() && $user->company_id === $document->worker->company_id) {
            return true;
        }

        return false;
    }

    public function delete(User $user, WorkerDocument $document): bool
    {
        return $user->isSuperAdmin();
    }

    public function download(User $user, WorkerDocument $document): bool
    {
        if ($user->isSuperAdmin()) {
            return true;
        }

        if ($user->isGestorRH() && $user->company_id === $document->worker->company_id) {
            return true;
        }

        return false;
    }

    public function upload(User $user): bool
    {
        return $user->isSuperAdmin() || $user->isGestorRH();
    }
}
