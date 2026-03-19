<?php

namespace App\Filament\Resources\UniformItems\Pages;

use App\Filament\Resources\UniformItems\UniformItemsResource;
use Filament\Actions\DeleteAction;
use Filament\Actions\ForceDeleteAction;
use Filament\Actions\RestoreAction;
use Filament\Resources\Pages\EditRecord;

class EditUniformItems extends EditRecord
{
    protected static string $resource = UniformItemsResource::class;

    protected function getHeaderActions(): array
    {
        return [
            DeleteAction::make(),
            ForceDeleteAction::make(),
            RestoreAction::make(),
        ];
    }
}
