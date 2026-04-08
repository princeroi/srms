<?php

declare(strict_types=1);

namespace App\Policies;

use Illuminate\Foundation\Auth\User as AuthUser;
use App\Models\UniformIssuanceBilling;
use Illuminate\Auth\Access\HandlesAuthorization;

class UniformIssuanceBillingPolicy
{
    use HandlesAuthorization;
    
    public function viewAny(AuthUser $authUser): bool
    {
        return $authUser->can('ViewAny:UniformIssuanceBilling');
    }

    public function view(AuthUser $authUser, UniformIssuanceBilling $uniformIssuanceBilling): bool
    {
        return $authUser->can('View:UniformIssuanceBilling');
    }

    public function create(AuthUser $authUser): bool
    {
        return $authUser->can('Create:UniformIssuanceBilling');
    }

    public function update(AuthUser $authUser, UniformIssuanceBilling $uniformIssuanceBilling): bool
    {
        return $authUser->can('Update:UniformIssuanceBilling');
    }

    public function delete(AuthUser $authUser, UniformIssuanceBilling $uniformIssuanceBilling): bool
    {
        return $authUser->can('Delete:UniformIssuanceBilling');
    }

    public function deleteAny(AuthUser $authUser): bool
    {
        return $authUser->can('DeleteAny:UniformIssuanceBilling');
    }

    public function restore(AuthUser $authUser, UniformIssuanceBilling $uniformIssuanceBilling): bool
    {
        return $authUser->can('Restore:UniformIssuanceBilling');
    }

    public function forceDelete(AuthUser $authUser, UniformIssuanceBilling $uniformIssuanceBilling): bool
    {
        return $authUser->can('ForceDelete:UniformIssuanceBilling');
    }

    public function forceDeleteAny(AuthUser $authUser): bool
    {
        return $authUser->can('ForceDeleteAny:UniformIssuanceBilling');
    }

    public function restoreAny(AuthUser $authUser): bool
    {
        return $authUser->can('RestoreAny:UniformIssuanceBilling');
    }

    public function replicate(AuthUser $authUser, UniformIssuanceBilling $uniformIssuanceBilling): bool
    {
        return $authUser->can('Replicate:UniformIssuanceBilling');
    }

    public function reorder(AuthUser $authUser): bool
    {
        return $authUser->can('Reorder:UniformIssuanceBilling');
    }

}