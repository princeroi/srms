<?php

namespace App\Filament\Resources\UniformCategories;

use App\Filament\Resources\UniformCategories\Pages\CreateUniformCategory;
use App\Filament\Resources\UniformCategories\Pages\EditUniformCategory;
use App\Filament\Resources\UniformCategories\Pages\ListUniformCategories;
use App\Filament\Resources\UniformCategories\Schemas\UniformCategoryForm;
use App\Filament\Resources\UniformCategories\Tables\UniformCategoriesTable;
use App\Models\UniformCategory;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;

class UniformCategoryResource extends Resource
{
    protected static ?string $model = UniformCategory::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedRectangleStack;

    public static function form(Schema $schema): Schema
    {
        return UniformCategoryForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return UniformCategoriesTable::configure($table);
    }

    public static function getRelations(): array
    {
        return [
            //
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => ListUniformCategories::route('/'),
        ];
    }
}
