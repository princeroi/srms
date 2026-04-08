<?php

namespace App\Filament\Resources\UniformRestocks;

use App\Filament\Resources\UniformRestocks\Pages\CreateUniformRestocks;
use App\Filament\Resources\UniformRestocks\Pages\EditUniformRestocks;
use App\Filament\Resources\UniformRestocks\Pages\ListUniformRestocks;
use App\Filament\Resources\UniformRestocks\Schemas\UniformRestocksForm;
use App\Filament\Resources\UniformRestocks\Tables\UniformRestocksTable;
use App\Models\UniformRestocks;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;

class UniformRestocksResource extends Resource
{
    protected static ?string $model = UniformRestocks::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedArrowLeftEndOnRectangle;

    public static function getNavigationGroup(): ?string
    {
        return 'Stock & Inventory';
    }

    public static function form(Schema $schema): Schema
    {
        return UniformRestocksForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return UniformRestocksTable::configure($table);
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
            'index' => ListUniformRestocks::route('/'),
        ];
    }
}
