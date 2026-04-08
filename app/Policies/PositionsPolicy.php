<?php

declare(strict_types=1);

namespace App\Policies;

use Illuminate\Foundation\Auth\User as AuthUser;
use App\Models\Positions;
use Illuminate\Auth\Access\HandlesAuthorization;

class PositionsPolicy
{
    use HandlesAuthorization;
    
    public function viewAny(AuthUser $authUser): bool
    {
        return $authUser->can('ViewAny:Positions');
    }

    public function view(AuthUser $authUser, Positions $positions): bool
    {
        return $authUser->can('View:Positions');
    }

    public function create(AuthUser $authUser): bool
    {
        return $authUser->can('Create:Positions');
    }

    public function update(AuthUser $authUser, Positions $positions): bool
    {
        return $authUser->can('Update:Positions');
    }

    public function delete(AuthUser $authUser, Positions $positions): bool
    {
        return $authUser->can('Delete:Positions');
    }

    public function deleteAny(AuthUser $authUser): bool
    {
        return $authUser->can('DeleteAny:Positions');
    }

    public function restore(AuthUser $authUser, Positions $positions): bool
    {
        return $authUser->can('Restore:Positions');
    }

    public function forceDelete(AuthUser $authUser, Positions $positions): bool
    {
        return $authUser->can('ForceDelete:Positions');
    }

    public function forceDeleteAny(AuthUser $authUser): bool
    {
        return $authUser->can('ForceDeleteAny:Positions');
    }

    public function restoreAny(AuthUser $authUser): bool
    {
        return $authUser->can('RestoreAny:Positions');
    }

    public function replicate(AuthUser $authUser, Positions $positions): bool
    {
        return $authUser->can('Replicate:Positions');
    }

    public function reorder(AuthUser $authUser): bool
    {
        return $authUser->can('Reorder:Positions');
    }

}