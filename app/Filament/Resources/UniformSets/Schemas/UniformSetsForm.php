<?php

namespace App\Filament\Resources\UniformSets\Schemas;

use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Schemas\Schema;
use Filament\Forms\Components\Repeater;

class UniformSetsForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('uniform_set_name')
                    ->required(),
                Select::make('position_id')
                    ->relationship('position', 'position_name')
                    ->preload()
                    ->searchable()
                    ->required(),
                Select::make('site_id')
                    ->relationship('site', 'site_name')
                    ->preload()
                    ->searchable()
                    ->required(),
                Select::make('employee_status')
                    ->options(['all' => 'All', 'reliever' => 'Reliever', 'posted' => 'Posted'])
                    ->required(),
                Textarea::make('uniform_set_description')
                    ->columnSpanFull(),
                Repeater::make('uniformSetItem')
                    ->relationship('uniformSetItem')
                    ->schema([
                        Select::make('uniform_item_id')
                            ->relationship('uniformItem', 'uniform_item_name')
                            ->preload()
                            ->required()
                            ->searchable(),
                        TextInput::make('quantity')
                            ->numeric()
                            ->required(),
                    ])
                    ->columns(2)
                    ->columnSpan('full'),
            ]);
    }
}
