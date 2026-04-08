<?php

namespace App\Filament\Resources\UniformIssuances;

use App\Filament\Resources\UniformIssuances\Pages\CreateUniformIssuances;
use App\Filament\Resources\UniformIssuances\Pages\EditUniformIssuances;
use App\Filament\Resources\UniformIssuances\Pages\ListUniformIssuances;
use App\Filament\Resources\UniformIssuances\Schemas\UniformIssuancesForm;
use App\Filament\Resources\UniformIssuances\Tables\UniformIssuancesTable;
use App\Models\UniformIssuances;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;

class UniformIssuancesResource extends Resource
{
    protected static ?string $model = UniformIssuances::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedArrowRightStartOnRectangle;

    public static function getNavigationGroup(): ?string
    {
        return 'Distributions';
    }

    public static function form(Schema $schema): Schema
    {
        return UniformIssuancesForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return UniformIssuancesTable::configure($table);
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
            'index' => ListUniformIssuances::route('/'),
        ];
    }

    public static function syncQuantities($record): void {
        $status = $record->uniform_issuance_status;

        foreach ($record->uniformIssuanceRecipient as $recipient) {
            foreach ($recipient->uniformIssuanceItem as $item) {
                $qty = (int) $item->quantity;

                if($status === 'issued') {
                    $item->update([
                        'released_quantity'     => $qty,
                        'remaining_quantity'    => 0,
                    ]);
                } else {
                    $item->update([
                        'released_quantity'     => 0,
                        'remaining_quantity'    =>$qty,
                    ]);
                }
            }
        }
    }
}
