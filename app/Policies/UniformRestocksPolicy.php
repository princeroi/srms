<?php

declare(strict_types=1);

namespace App\Policies;

use Illuminate\Foundation\Auth\User as AuthUser;
use App\Models\UniformRestocks;
use Illuminate\Auth\Access\HandlesAuthorization;

class UniformRestocksPolicy
{
    use HandlesAuthorization;
    
    public function viewAny(AuthUser $authUser): bool
    {
        return $authUser->can('ViewAny:UniformRestocks');
    }

    public function view(AuthUser $authUser, UniformRestocks $uniformRestocks): bool
    {
        return $authUser->can('View:UniformRestocks');
    }

    public function create(AuthUser $authUser): bool
    {
        return $authUser->can('Create:UniformRestocks');
    }

    public function update(AuthUser $authUser, UniformRestocks $uniformRestocks): bool
    {
        return $authUser->can('Update:UniformRestocks');
    }

    public function delete(AuthUser $authUser, UniformRestocks $uniformRestocks): bool
    {
        return $authUser->can('Delete:UniformRestocks');
    }

    public function deleteAny(AuthUser $authUser): bool
    {
        return $authUser->can('DeleteAny:UniformRestocks');
    }

    public function restore(AuthUser $authUser, UniformRestocks $uniformRestocks): bool
    {
        return $authUser->can('Restore:UniformRestocks');
    }

    public function forceDelete(AuthUser $authUser, UniformRestocks $uniformRestocks): bool
    {
        return $authUser->can('ForceDelete:UniformRestocks');
    }

    public function forceDeleteAny(AuthUser $authUser): bool
    {
        return $authUser->can('ForceDeleteAny:UniformRestocks');
    }

    public function restoreAny(AuthUser $authUser): bool
    {
        return $authUser->can('RestoreAny:UniformRestocks');
    }

    public function replicate(AuthUser $authUser, UniformRestocks $uniformRestocks): bool
    {
        return $authUser->can('Replicate:UniformRestocks');
    }

    public function reorder(AuthUser $authUser): bool
    {
        return $authUser->can('Reorder:UniformRestocks');
    }

}