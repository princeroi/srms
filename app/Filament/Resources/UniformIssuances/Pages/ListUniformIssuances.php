<?php

namespace App\Filament\Resources\UniformIssuances\Pages;

use App\Filament\Resources\UniformIssuances\UniformIssuancesResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;
use App\Models\UniformIssuanceLog;
use Illuminate\Support\Facades\Auth;

class ListUniformIssuances extends ListRecords
{
    protected static string $resource = UniformIssuancesResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make()
                ->after(function ($record) {
                    UniformIssuancesResource::syncQuantities($record);

                    UniformIssuanceLog::create([
                        'uniform_issuance_id' => $record->id,
                        'user_id'             => Auth::id(),
                        'action'              => 'created',
                        'status_from'         => null,
                        'status_to'           => $record->uniform_issuance_status,
                        'note'                => 'Issuance was created.',
                    ]);
                })
                ->extraAttributes([
                    'style' => 'color: #ffffff;' // dark text
                ]),
        ];
    }
}
