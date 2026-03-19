<?php

namespace App\Filament\Resources\UniformItemVariants;

use App\Filament\Resources\UniformItemVariants\Pages\CreateUniformItemVariants;
use App\Filament\Resources\UniformItemVariants\Pages\EditUniformItemVariants;
use App\Filament\Resources\UniformItemVariants\Pages\ListUniformItemVariants;
use App\Filament\Resources\UniformItemVariants\Schemas\UniformItemVariantsForm;
use App\Filament\Resources\UniformItemVariants\Tables\UniformItemVariantsTable;
use App\Models\UniformItemVariants;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class UniformItemVariantsResource extends Resource
{
    protected static ?string $model = UniformItemVariants::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedRectangleStack;

    public static function form(Schema $schema): Schema
    {
        return UniformItemVariantsForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return UniformItemVariantsTable::configure($table);
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
            'index' => ListUniformItemVariants::route('/'),
        ];
    }

    public static function getRecordRouteBindingEloquentQuery(): Builder
    {
        return parent::getRecordRouteBindingEloquentQuery()
            ->withoutGlobalScopes([
                SoftDeletingScope::class,
            ]);
    }
}
