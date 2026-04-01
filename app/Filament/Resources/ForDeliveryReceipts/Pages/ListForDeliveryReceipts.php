<?php

namespace App\Filament\Resources\ForDeliveryReceipts\Pages;

use App\Filament\Resources\ForDeliveryReceipts\ForDeliveryReceiptResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListForDeliveryReceipts extends ListRecords
{
    protected static string $resource = ForDeliveryReceiptResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make()
                ->extraAttributes([
                        'style' => 'color: #ffffff;' // dark text
                    ]),
        ];
    }
}
