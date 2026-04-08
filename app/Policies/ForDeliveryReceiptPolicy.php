<?php

declare(strict_types=1);

namespace App\Policies;

use Illuminate\Foundation\Auth\User as AuthUser;
use App\Models\ForDeliveryReceipt;
use Illuminate\Auth\Access\HandlesAuthorization;

class ForDeliveryReceiptPolicy
{
    use HandlesAuthorization;
    
    public function viewAny(AuthUser $authUser): bool
    {
        return $authUser->can('ViewAny:ForDeliveryReceipt');
    }

    public function view(AuthUser $authUser, ForDeliveryReceipt $forDeliveryReceipt): bool
    {
        return $authUser->can('View:ForDeliveryReceipt');
    }

    public function create(AuthUser $authUser): bool
    {
        return $authUser->can('Create:ForDeliveryReceipt');
    }

    public function update(AuthUser $authUser, ForDeliveryReceipt $forDeliveryReceipt): bool
    {
        return $authUser->can('Update:ForDeliveryReceipt');
    }

    public function delete(AuthUser $authUser, ForDeliveryReceipt $forDeliveryReceipt): bool
    {
        return $authUser->can('Delete:ForDeliveryReceipt');
    }

    public function deleteAny(AuthUser $authUser): bool
    {
        return $authUser->can('DeleteAny:ForDeliveryReceipt');
    }

    public function restore(AuthUser $authUser, ForDeliveryReceipt $forDeliveryReceipt): bool
    {
        return $authUser->can('Restore:ForDeliveryReceipt');
    }

    public function forceDelete(AuthUser $authUser, ForDeliveryReceipt $forDeliveryReceipt): bool
    {
        return $authUser->can('ForceDelete:ForDeliveryReceipt');
    }

    public function forceDeleteAny(AuthUser $authUser): bool
    {
        return $authUser->can('ForceDeleteAny:ForDeliveryReceipt');
    }

    public function restoreAny(AuthUser $authUser): bool
    {
        return $authUser->can('RestoreAny:ForDeliveryReceipt');
    }

    public function replicate(AuthUser $authUser, ForDeliveryReceipt $forDeliveryReceipt): bool
    {
        return $authUser->can('Replicate:ForDeliveryReceipt');
    }

    public function reorder(AuthUser $authUser): bool
    {
        return $authUser->can('Reorder:ForDeliveryReceipt');
    }

}