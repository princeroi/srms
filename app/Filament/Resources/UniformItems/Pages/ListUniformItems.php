<?php

namespace App\Filament\Resources\UniformItems\Pages;

use App\Filament\Resources\UniformItems\UniformItemsResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListUniformItems extends ListRecords
{
    protected static string $resource = UniformItemsResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}
