<?php

namespace App\Filament\Resources\Transmittals;

use App\Filament\Resources\Transmittals\Pages\CreateTransmittals;
use App\Filament\Resources\Transmittals\Pages\EditTransmittals;
use App\Filament\Resources\Transmittals\Pages\ListTransmittals;
use App\Filament\Resources\Transmittals\Schemas\TransmittalsForm;
use App\Filament\Resources\Transmittals\Tables\TransmittalsTable;
use App\Models\Transmittals;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;

class TransmittalsResource extends Resource
{
    protected static ?string $model = Transmittals::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedRectangleStack;

    public static function form(Schema $schema): Schema
    {
        return TransmittalsForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return TransmittalsTable::configure($table);
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
            'index' => ListTransmittals::route('/'),
        ];
    }
}
