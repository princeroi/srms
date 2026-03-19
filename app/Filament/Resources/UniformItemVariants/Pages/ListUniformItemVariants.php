<?php

namespace App\Filament\Resources\UniformItemVariants\Pages;

use App\Filament\Resources\UniformItemVariants\UniformItemVariantsResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListUniformItemVariants extends ListRecords
{
    protected static string $resource = UniformItemVariantsResource::class;

    protected function getHeaderActions(): array
    {
        return [];
    }
}
