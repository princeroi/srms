<?php

namespace App\Filament\Resources\Sites\Pages;

use App\Filament\Resources\Sites\SitesResource;
use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;

class EditSites extends EditRecord
{
    protected static string $resource = SitesResource::class;

    protected function getHeaderActions(): array
    {
        return [
            DeleteAction::make(),
        ];
    }
}
