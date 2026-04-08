<?php

declare(strict_types=1);

namespace App\Policies;

use Illuminate\Foundation\Auth\User as AuthUser;
use App\Models\UniformIssuances;
use Illuminate\Auth\Access\HandlesAuthorization;

class UniformIssuancesPolicy
{
    use HandlesAuthorization;
    
    public function viewAny(AuthUser $authUser): bool
    {
        return $authUser->can('ViewAny:UniformIssuances');
    }

    public function view(AuthUser $authUser, UniformIssuances $uniformIssuances): bool
    {
        return $authUser->can('View:UniformIssuances');
    }

    public function create(AuthUser $authUser): bool
    {
        return $authUser->can('Create:UniformIssuances');
    }

    public function update(AuthUser $authUser, UniformIssuances $uniformIssuances): bool
    {
        return $authUser->can('Update:UniformIssuances');
    }

    public function delete(AuthUser $authUser, UniformIssuances $uniformIssuances): bool
    {
        return $authUser->can('Delete:UniformIssuances');
    }

    public function deleteAny(AuthUser $authUser): bool
    {
        return $authUser->can('DeleteAny:UniformIssuances');
    }

    public function restore(AuthUser $authUser, UniformIssuances $uniformIssuances): bool
    {
        return $authUser->can('Restore:UniformIssuances');
    }

    public function forceDelete(AuthUser $authUser, UniformIssuances $uniformIssuances): bool
    {
        return $authUser->can('ForceDelete:UniformIssuances');
    }

    public function forceDeleteAny(AuthUser $authUser): bool
    {
        return $authUser->can('ForceDeleteAny:UniformIssuances');
    }

    public function restoreAny(AuthUser $authUser): bool
    {
        return $authUser->can('RestoreAny:UniformIssuances');
    }

    public function replicate(AuthUser $authUser, UniformIssuances $uniformIssuances): bool
    {
        return $authUser->can('Replicate:UniformIssuances');
    }

    public function reorder(AuthUser $authUser): bool
    {
        return $authUser->can('Reorder:UniformIssuances');
    }

}