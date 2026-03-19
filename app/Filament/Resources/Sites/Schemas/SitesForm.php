<?php

namespace App\Filament\Resources\Sites\Schemas;

use Filament\Forms\Components\TextInput;
use Filament\Schemas\Schema;
use Filament\Forms\Components\Select;

class SitesForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('site_name')
                    ->required(),
                TextInput::make('site_location')
                    ->required(),
                Select::make('client_id')
                    ->required()
                    ->relationship('client', 'client_name')
                    ->searchable()
                    ->preload(),
            ]);
    }
}
