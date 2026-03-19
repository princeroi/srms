<?php

namespace App\Filament\Resources\UniformItemVariants\Pages;

use App\Filament\Resources\UniformItemVariants\UniformItemVariantsResource;
use Filament\Actions\DeleteAction;
use Filament\Actions\ForceDeleteAction;
use Filament\Actions\RestoreAction;
use Filament\Resources\Pages\EditRecord;

class EditUniformItemVariants extends EditRecord
{
    protected static string $resource = UniformItemVariantsResource::class;

    protected function getHeaderActions(): array
    {
        return [
            DeleteAction::make(),
            ForceDeleteAction::make(),
            RestoreAction::make(),
        ];
    }
}
