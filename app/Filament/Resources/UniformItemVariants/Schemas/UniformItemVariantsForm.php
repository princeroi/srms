<?php

namespace App\Filament\Resources\UniformItemVariants\Schemas;

use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Schema;

class UniformItemVariantsForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Select::make('uniform_item_id')
                    ->relationship('uniformItem', 'id')
                    ->required(),
                TextInput::make('uniform_item_size')
                    ->required(),
                TextInput::make('uniform_item_quantity')
                    ->required()
                    ->numeric(),
            ]);
    }
}
