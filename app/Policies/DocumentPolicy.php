<?php

namespace App\Policies;

use App\Models\Document;
use App\Models\User;

class DocumentPolicy
{
    public function viewAny(User $user): bool
    {
        return true; // Everyone can see the list, but we filter out sensitive ones in the query if needed, or Operacional sees all non-sigilosos
    }

    public function view(User $user, Document $document): bool
    {
        if ($user->role === 'Super Admin' || $user->role === 'Advogado') {
            return true;
        }
        if ($user->role === 'Diretor') {
            return true;
        }
        // Operacional cannot see Sigiloso/LGPD docs
        if ($document->sensitivity_level !== 'Normal') {
            return false;
        }
        // Only same unit
        return $user->unit === $document->owner->unit;
    }

    public function create(User $user): bool
    {
        return in_array($user->role, ['Super Admin', 'Advogado', 'Operacional']);
    }

    public function update(User $user, Document $document): bool
    {
        return in_array($user->role, ['Super Admin', 'Advogado']) || $user->id === $document->owner_id;
    }

    public function delete(User $user, Document $document): bool
    {
        return in_array($user->role, ['Super Admin', 'Advogado']);
    }
}
