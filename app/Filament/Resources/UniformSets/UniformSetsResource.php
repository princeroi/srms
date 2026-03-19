<?php

namespace App\Filament\Resources\UniformSets;

use App\Filament\Resources\UniformSets\Pages\CreateUniformSets;
use App\Filament\Resources\UniformSets\Pages\EditUniformSets;
use App\Filament\Resources\UniformSets\Pages\ListUniformSets;
use App\Filament\Resources\UniformSets\Schemas\UniformSetsForm;
use App\Filament\Resources\UniformSets\Tables\UniformSetsTable;
use App\Models\UniformSets;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;

class UniformSetsResource extends Resource
{
    protected static ?string $model = UniformSets::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedRectangleStack;

    protected static ?string $recordTitleAttribute = '\\';

    public static function form(Schema $schema): Schema
    {
        return UniformSetsForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return UniformSetsTable::configure($table);
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
            'index' => ListUniformSets::route('/'),
        ];
    }
}
