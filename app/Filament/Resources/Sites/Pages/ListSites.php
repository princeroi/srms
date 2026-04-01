<?php

namespace App\Filament\Resources\Sites\Pages;

use App\Filament\Resources\Sites\SitesResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListSites extends ListRecords
{
    protected static string $resource = SitesResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make()
                ->extraAttributes([
                    'style' => 'color: #ffffff;' // dark text
                ]),
        ];
    }
}
