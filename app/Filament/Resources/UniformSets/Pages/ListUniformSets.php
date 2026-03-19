<?php

namespace App\Filament\Resources\UniformSets\Pages;

use App\Filament\Resources\UniformSets\UniformSetsResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListUniformSets extends ListRecords
{
    protected static string $resource = UniformSetsResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}
