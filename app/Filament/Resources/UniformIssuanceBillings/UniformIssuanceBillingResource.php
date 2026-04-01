<?php

namespace App\Filament\Resources\UniformIssuanceBillings;

use App\Filament\Resources\UniformIssuanceBillings\Pages\CreateUniformIssuanceBilling;
use App\Filament\Resources\UniformIssuanceBillings\Pages\EditUniformIssuanceBilling;
use App\Filament\Resources\UniformIssuanceBillings\Pages\ListUniformIssuanceBillings;
use App\Filament\Resources\UniformIssuanceBillings\Schemas\UniformIssuanceBillingForm;
use App\Filament\Resources\UniformIssuanceBillings\Tables\UniformIssuanceBillingsTable;
use App\Models\UniformIssuanceBilling;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;

class UniformIssuanceBillingResource extends Resource
{
    protected static ?string $model = UniformIssuanceBilling::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedRectangleStack;

    public static function form(Schema $schema): Schema
    {
        return UniformIssuanceBillingForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return UniformIssuanceBillingsTable::configure($table);
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
            'index' => ListUniformIssuanceBillings::route('/'),
            'create' => CreateUniformIssuanceBilling::route('/create'),
            'edit' => EditUniformIssuanceBilling::route('/{record}/edit'),
        ];
    }
}
