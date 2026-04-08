<?php

declare(strict_types=1);

namespace App\Policies;

use Illuminate\Foundation\Auth\User as AuthUser;
use App\Models\UniformSets;
use Illuminate\Auth\Access\HandlesAuthorization;

class UniformSetsPolicy
{
    use HandlesAuthorization;
    
    public function viewAny(AuthUser $authUser): bool
    {
        return $authUser->can('ViewAny:UniformSets');
    }

    public function view(AuthUser $authUser, UniformSets $uniformSets): bool
    {
        return $authUser->can('View:UniformSets');
    }

    public function create(AuthUser $authUser): bool
    {
        return $authUser->can('Create:UniformSets');
    }

    public function update(AuthUser $authUser, UniformSets $uniformSets): bool
    {
        return $authUser->can('Update:UniformSets');
    }

    public function delete(AuthUser $authUser, UniformSets $uniformSets): bool
    {
        return $authUser->can('Delete:UniformSets');
    }

    public function deleteAny(AuthUser $authUser): bool
    {
        return $authUser->can('DeleteAny:UniformSets');
    }

    public function restore(AuthUser $authUser, UniformSets $uniformSets): bool
    {
        return $authUser->can('Restore:UniformSets');
    }

    public function forceDelete(AuthUser $authUser, UniformSets $uniformSets): bool
    {
        return $authUser->can('ForceDelete:UniformSets');
    }

    public function forceDeleteAny(AuthUser $authUser): bool
    {
        return $authUser->can('ForceDeleteAny:UniformSets');
    }

    public function restoreAny(AuthUser $authUser): bool
    {
        return $authUser->can('RestoreAny:UniformSets');
    }

    public function replicate(AuthUser $authUser, UniformSets $uniformSets): bool
    {
        return $authUser->can('Replicate:UniformSets');
    }

    public function reorder(AuthUser $authUser): bool
    {
        return $authUser->can('Reorder:UniformSets');
    }

}