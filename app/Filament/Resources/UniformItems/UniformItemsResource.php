<?php

namespace App\Filament\Resources\UniformItems;

use App\Filament\Resources\UniformItems\Pages\CreateUniformItems;
use App\Filament\Resources\UniformItems\Pages\EditUniformItems;
use App\Filament\Resources\UniformItems\Pages\ListUniformItems;
use App\Filament\Resources\UniformItems\Schemas\UniformItemsForm;
use App\Filament\Resources\UniformItems\Tables\UniformItemsTable;
use App\Models\UniformItems;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class UniformItemsResource extends Resource
{
    protected static ?string $model = UniformItems::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedSquare3Stack3d;

    public static function getNavigationGroup(): ?string
    {
        return 'Uniform Setup';
    }
    
    public static function form(Schema $schema): Schema
    {
        return UniformItemsForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return UniformItemsTable::configure($table);
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
            'index' => ListUniformItems::route('/'),
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
