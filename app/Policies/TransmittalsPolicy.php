<?php

declare(strict_types=1);

namespace App\Policies;

use Illuminate\Foundation\Auth\User as AuthUser;
use App\Models\Transmittals;
use Illuminate\Auth\Access\HandlesAuthorization;

class TransmittalsPolicy
{
    use HandlesAuthorization;
    
    public function viewAny(AuthUser $authUser): bool
    {
        return $authUser->can('ViewAny:Transmittals');
    }

    public function view(AuthUser $authUser, Transmittals $transmittals): bool
    {
        return $authUser->can('View:Transmittals');
    }

    public function create(AuthUser $authUser): bool
    {
        return $authUser->can('Create:Transmittals');
    }

    public function update(AuthUser $authUser, Transmittals $transmittals): bool
    {
        return $authUser->can('Update:Transmittals');
    }

    public function delete(AuthUser $authUser, Transmittals $transmittals): bool
    {
        return $authUser->can('Delete:Transmittals');
    }

    public function deleteAny(AuthUser $authUser): bool
    {
        return $authUser->can('DeleteAny:Transmittals');
    }

    public function restore(AuthUser $authUser, Transmittals $transmittals): bool
    {
        return $authUser->can('Restore:Transmittals');
    }

    public function forceDelete(AuthUser $authUser, Transmittals $transmittals): bool
    {
        return $authUser->can('ForceDelete:Transmittals');
    }

    public function forceDeleteAny(AuthUser $authUser): bool
    {
        return $authUser->can('ForceDeleteAny:Transmittals');
    }

    public function restoreAny(AuthUser $authUser): bool
    {
        return $authUser->can('RestoreAny:Transmittals');
    }

    public function replicate(AuthUser $authUser, Transmittals $transmittals): bool
    {
        return $authUser->can('Replicate:Transmittals');
    }

    public function reorder(AuthUser $authUser): bool
    {
        return $authUser->can('Reorder:Transmittals');
    }

}