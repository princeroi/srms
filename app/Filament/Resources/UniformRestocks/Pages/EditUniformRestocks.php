<?php

namespace App\Filament\Resources\UniformRestocks\Pages;

use App\Filament\Resources\UniformRestocks\UniformRestocksResource;
use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;

class EditUniformRestocks extends EditRecord
{
    protected static string $resource = UniformRestocksResource::class;

    protected function getHeaderActions(): array
    {
        return [
            DeleteAction::make(),
        ];
    }
}
