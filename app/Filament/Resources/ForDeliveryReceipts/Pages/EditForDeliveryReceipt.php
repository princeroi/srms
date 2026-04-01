<?php

namespace App\Filament\Resources\ForDeliveryReceipts\Pages;

use App\Filament\Resources\ForDeliveryReceipts\ForDeliveryReceiptResource;
use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;

class EditForDeliveryReceipt extends EditRecord
{
    protected static string $resource = ForDeliveryReceiptResource::class;

    protected function getHeaderActions(): array
    {
        return [
            DeleteAction::make(),
        ];
    }
}
