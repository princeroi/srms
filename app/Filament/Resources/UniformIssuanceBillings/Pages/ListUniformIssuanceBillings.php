<?php

namespace App\Filament\Resources\UniformIssuanceBillings\Pages;

use App\Filament\Resources\UniformIssuanceBillings\UniformIssuanceBillingResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListUniformIssuanceBillings extends ListRecords
{
    protected static string $resource = UniformIssuanceBillingResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}
