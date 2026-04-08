<?php

declare(strict_types=1);

namespace App\Policies;

use Illuminate\Foundation\Auth\User as AuthUser;
use App\Models\UniformItemVariants;
use Illuminate\Auth\Access\HandlesAuthorization;

class UniformItemVariantsPolicy
{
    use HandlesAuthorization;
    
    public function viewAny(AuthUser $authUser): bool
    {
        return $authUser->can('ViewAny:UniformItemVariants');
    }

    public function view(AuthUser $authUser, UniformItemVariants $uniformItemVariants): bool
    {
        return $authUser->can('View:UniformItemVariants');
    }

    public function create(AuthUser $authUser): bool
    {
        return $authUser->can('Create:UniformItemVariants');
    }

    public function update(AuthUser $authUser, UniformItemVariants $uniformItemVariants): bool
    {
        return $authUser->can('Update:UniformItemVariants');
    }

    public function delete(AuthUser $authUser, UniformItemVariants $uniformItemVariants): bool
    {
        return $authUser->can('Delete:UniformItemVariants');
    }

    public function deleteAny(AuthUser $authUser): bool
    {
        return $authUser->can('DeleteAny:UniformItemVariants');
    }

    public function restore(AuthUser $authUser, UniformItemVariants $uniformItemVariants): bool
    {
        return $authUser->can('Restore:UniformItemVariants');
    }

    public function forceDelete(AuthUser $authUser, UniformItemVariants $uniformItemVariants): bool
    {
        return $authUser->can('ForceDelete:UniformItemVariants');
    }

    public function forceDeleteAny(AuthUser $authUser): bool
    {
        return $authUser->can('ForceDeleteAny:UniformItemVariants');
    }

    public function restoreAny(AuthUser $authUser): bool
    {
        return $authUser->can('RestoreAny:UniformItemVariants');
    }

    public function replicate(AuthUser $authUser, UniformItemVariants $uniformItemVariants): bool
    {
        return $authUser->can('Replicate:UniformItemVariants');
    }

    public function reorder(AuthUser $authUser): bool
    {
        return $authUser->can('Reorder:UniformItemVariants');
    }

}