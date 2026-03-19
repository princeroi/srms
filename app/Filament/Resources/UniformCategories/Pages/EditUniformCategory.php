<?php

namespace App\Filament\Resources\UniformCategories\Pages;

use App\Filament\Resources\UniformCategories\UniformCategoryResource;
use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;

class EditUniformCategory extends EditRecord
{
    protected static string $resource = UniformCategoryResource::class;

    protected function getHeaderActions(): array
    {
        return [
            DeleteAction::make(),
        ];
    }
}
