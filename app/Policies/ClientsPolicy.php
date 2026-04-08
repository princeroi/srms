<?php

declare(strict_types=1);

namespace App\Policies;

use Illuminate\Foundation\Auth\User as AuthUser;
use App\Models\Clients;
use Illuminate\Auth\Access\HandlesAuthorization;

class ClientsPolicy
{
    use HandlesAuthorization;
    
    public function viewAny(AuthUser $authUser): bool
    {
        return $authUser->can('ViewAny:Clients');
    }

    public function view(AuthUser $authUser, Clients $clients): bool
    {
        return $authUser->can('View:Clients');
    }

    public function create(AuthUser $authUser): bool
    {
        return $authUser->can('Create:Clients');
    }

    public function update(AuthUser $authUser, Clients $clients): bool
    {
        return $authUser->can('Update:Clients');
    }

    public function delete(AuthUser $authUser, Clients $clients): bool
    {
        return $authUser->can('Delete:Clients');
    }

    public function deleteAny(AuthUser $authUser): bool
    {
        return $authUser->can('DeleteAny:Clients');
    }

    public function restore(AuthUser $authUser, Clients $clients): bool
    {
        return $authUser->can('Restore:Clients');
    }

    public function forceDelete(AuthUser $authUser, Clients $clients): bool
    {
        return $authUser->can('ForceDelete:Clients');
    }

    public function forceDeleteAny(AuthUser $authUser): bool
    {
        return $authUser->can('ForceDeleteAny:Clients');
    }

    public function restoreAny(AuthUser $authUser): bool
    {
        return $authUser->can('RestoreAny:Clients');
    }

    public function replicate(AuthUser $authUser, Clients $clients): bool
    {
        return $authUser->can('Replicate:Clients');
    }

    public function reorder(AuthUser $authUser): bool
    {
        return $authUser->can('Reorder:Clients');
    }

}