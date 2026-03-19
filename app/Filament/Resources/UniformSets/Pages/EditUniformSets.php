<?php

namespace App\Filament\Resources\UniformSets\Pages;

use App\Filament\Resources\UniformSets\UniformSetsResource;
use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;

class EditUniformSets extends EditRecord
{
    protected static string $resource = UniformSetsResource::class;

    protected function getHeaderActions(): array
    {
        return [
            DeleteAction::make(),
        ];
    }
}
