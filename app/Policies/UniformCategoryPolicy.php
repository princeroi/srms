<?php

declare(strict_types=1);

namespace App\Policies;

use Illuminate\Foundation\Auth\User as AuthUser;
use App\Models\UniformCategory;
use Illuminate\Auth\Access\HandlesAuthorization;

class UniformCategoryPolicy
{
    use HandlesAuthorization;
    
    public function viewAny(AuthUser $authUser): bool
    {
        return $authUser->can('ViewAny:UniformCategory');
    }

    public function view(AuthUser $authUser, UniformCategory $uniformCategory): bool
    {
        return $authUser->can('View:UniformCategory');
    }

    public function create(AuthUser $authUser): bool
    {
        return $authUser->can('Create:UniformCategory');
    }

    public function update(AuthUser $authUser, UniformCategory $uniformCategory): bool
    {
        return $authUser->can('Update:UniformCategory');
    }

    public function delete(AuthUser $authUser, UniformCategory $uniformCategory): bool
    {
        return $authUser->can('Delete:UniformCategory');
    }

    public function deleteAny(AuthUser $authUser): bool
    {
        return $authUser->can('DeleteAny:UniformCategory');
    }

    public function restore(AuthUser $authUser, UniformCategory $uniformCategory): bool
    {
        return $authUser->can('Restore:UniformCategory');
    }

    public function forceDelete(AuthUser $authUser, UniformCategory $uniformCategory): bool
    {
        return $authUser->can('ForceDelete:UniformCategory');
    }

    public function forceDeleteAny(AuthUser $authUser): bool
    {
        return $authUser->can('ForceDeleteAny:UniformCategory');
    }

    public function restoreAny(AuthUser $authUser): bool
    {
        return $authUser->can('RestoreAny:UniformCategory');
    }

    public function replicate(AuthUser $authUser, UniformCategory $uniformCategory): bool
    {
        return $authUser->can('Replicate:UniformCategory');
    }

    public function reorder(AuthUser $authUser): bool
    {
        return $authUser->can('Reorder:UniformCategory');
    }

}