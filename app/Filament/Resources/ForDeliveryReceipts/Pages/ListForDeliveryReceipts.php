<?php

namespace App\Filament\Resources\ForDeliveryReceipts\Pages;

use App\Filament\Resources\ForDeliveryReceipts\ForDeliveryReceiptResource;
use App\Models\ForDeliveryReceipt;
use App\Models\IssuanceDr;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListForDeliveryReceipts extends ListRecords
{
    protected static string $resource = ForDeliveryReceiptResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make()
                ->using(function (array $data): ForDeliveryReceipt {
                    $drNumber = $data['dr_number'] ?? null;

                    // Auto-set status to done if DR number is provided
                    if (! empty($drNumber)) {
                        $data['status']    = 'done';
                        $data['done_date'] = $data['done_date'] ?? now();
                    }

                    // Remove virtual field before creating the record
                    unset($data['dr_number']);

                    $record = ForDeliveryReceipt::create($data);

                    // Save DR number to issuance_dr table
                    if (! empty($drNumber)) {
                        IssuanceDr::firstOrCreate([
                            'for_delivery_receipt_id' => $record->id,
                            'dr_number'               => $drNumber,
                        ]);
                    }

                    return $record;
                })
                ->extraAttributes([
                    'style' => 'color: #ffffff;',
                ]),
        ];
    }
}