<?php

namespace App\Filament\Resources\UniformRestocks\Pages;

use App\Filament\Resources\UniformRestocks\UniformRestocksResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListUniformRestocks extends ListRecords
{
    protected static string $resource = UniformRestocksResource::class;

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
