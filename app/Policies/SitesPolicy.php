<?php

declare(strict_types=1);

namespace App\Policies;

use Illuminate\Foundation\Auth\User as AuthUser;
use App\Models\Sites;
use Illuminate\Auth\Access\HandlesAuthorization;

class SitesPolicy
{
    use HandlesAuthorization;
    
    public function viewAny(AuthUser $authUser): bool
    {
        return $authUser->can('ViewAny:Sites');
    }

    public function view(AuthUser $authUser, Sites $sites): bool
    {
        return $authUser->can('View:Sites');
    }

    public function create(AuthUser $authUser): bool
    {
        return $authUser->can('Create:Sites');
    }

    public function update(AuthUser $authUser, Sites $sites): bool
    {
        return $authUser->can('Update:Sites');
    }

    public function delete(AuthUser $authUser, Sites $sites): bool
    {
        return $authUser->can('Delete:Sites');
    }

    public function deleteAny(AuthUser $authUser): bool
    {
        return $authUser->can('DeleteAny:Sites');
    }

    public function restore(AuthUser $authUser, Sites $sites): bool
    {
        return $authUser->can('Restore:Sites');
    }

    public function forceDelete(AuthUser $authUser, Sites $sites): bool
    {
        return $authUser->can('ForceDelete:Sites');
    }

    public function forceDeleteAny(AuthUser $authUser): bool
    {
        return $authUser->can('ForceDeleteAny:Sites');
    }

    public function restoreAny(AuthUser $authUser): bool
    {
        return $authUser->can('RestoreAny:Sites');
    }

    public function replicate(AuthUser $authUser, Sites $sites): bool
    {
        return $authUser->can('Replicate:Sites');
    }

    public function reorder(AuthUser $authUser): bool
    {
        return $authUser->can('Reorder:Sites');
    }

}