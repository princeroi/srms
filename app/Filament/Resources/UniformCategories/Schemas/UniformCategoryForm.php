<?php

namespace App\Filament\Resources\UniformCategories\Schemas;

use Filament\Forms\Components\TextInput;
use Filament\Schemas\Schema;

class UniformCategoryForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('uniform_category_name')
                    ->required(),
            ]);
    }
}
