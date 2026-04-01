<?php

namespace App\Filament\Resources\ForDeliveryReceipts;

use App\Filament\Resources\ForDeliveryReceipts\Pages\CreateForDeliveryReceipt;
use App\Filament\Resources\ForDeliveryReceipts\Pages\EditForDeliveryReceipt;
use App\Filament\Resources\ForDeliveryReceipts\Pages\ListForDeliveryReceipts;
use App\Filament\Resources\ForDeliveryReceipts\Schemas\ForDeliveryReceiptForm;
use App\Filament\Resources\ForDeliveryReceipts\Tables\ForDeliveryReceiptsTable;
use App\Models\ForDeliveryReceipt;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;

class ForDeliveryReceiptResource extends Resource
{
    protected static ?string $model = ForDeliveryReceipt::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedRectangleStack;

    public static function form(Schema $schema): Schema
    {
        return ForDeliveryReceiptForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return ForDeliveryReceiptsTable::configure($table);
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
            'index' => ListForDeliveryReceipts::route('/'),
            'create' => CreateForDeliveryReceipt::route('/create'),
            'edit' => EditForDeliveryReceipt::route('/{record}/edit'),
        ];
    }
}
