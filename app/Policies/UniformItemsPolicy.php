<?php

declare(strict_types=1);

namespace App\Policies;

use Illuminate\Foundation\Auth\User as AuthUser;
use App\Models\UniformItems;
use Illuminate\Auth\Access\HandlesAuthorization;

class UniformItemsPolicy
{
    use HandlesAuthorization;
    
    public function viewAny(AuthUser $authUser): bool
    {
        return $authUser->can('ViewAny:UniformItems');
    }

    public function view(AuthUser $authUser, UniformItems $uniformItems): bool
    {
        return $authUser->can('View:UniformItems');
    }

    public function create(AuthUser $authUser): bool
    {
        return $authUser->can('Create:UniformItems');
    }

    public function update(AuthUser $authUser, UniformItems $uniformItems): bool
    {
        return $authUser->can('Update:UniformItems');
    }

    public function delete(AuthUser $authUser, UniformItems $uniformItems): bool
    {
        return $authUser->can('Delete:UniformItems');
    }

    public function deleteAny(AuthUser $authUser): bool
    {
        return $authUser->can('DeleteAny:UniformItems');
    }

    public function restore(AuthUser $authUser, UniformItems $uniformItems): bool
    {
        return $authUser->can('Restore:UniformItems');
    }

    public function forceDelete(AuthUser $authUser, UniformItems $uniformItems): bool
    {
        return $authUser->can('ForceDelete:UniformItems');
    }

    public function forceDeleteAny(AuthUser $authUser): bool
    {
        return $authUser->can('ForceDeleteAny:UniformItems');
    }

    public function restoreAny(AuthUser $authUser): bool
    {
        return $authUser->can('RestoreAny:UniformItems');
    }

    public function replicate(AuthUser $authUser, UniformItems $uniformItems): bool
    {
        return $authUser->can('Replicate:UniformItems');
    }

    public function reorder(AuthUser $authUser): bool
    {
        return $authUser->can('Reorder:UniformItems');
    }

}