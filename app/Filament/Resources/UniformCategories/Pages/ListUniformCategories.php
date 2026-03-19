<?php

namespace App\Filament\Resources\UniformCategories\Pages;

use App\Filament\Resources\UniformCategories\UniformCategoryResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListUniformCategories extends ListRecords
{
    protected static string $resource = UniformCategoryResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}
