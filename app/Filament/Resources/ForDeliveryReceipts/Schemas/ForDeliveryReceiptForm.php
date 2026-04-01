<?php

namespace App\Filament\Resources\ForDeliveryReceipts\Schemas;

use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Schema;

class ForDeliveryReceiptForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Select::make('uniform_issuance_id')
                    ->relationship('uniformIssuance', 'id')
                    ->required(),
                TextInput::make('endorse_by')
                    ->required(),
                DatePicker::make('endorse_date'),
                TextInput::make('item_summary')
                    ->required(),
                Select::make('status')
                    ->options(['pending' => 'Pending', 'done' => 'Done', 'cancelled' => 'Cancelled'])
                    ->default('pending')
                    ->required(),
                DatePicker::make('done_date'),
                DatePicker::make('cancel_date'),
                TextInput::make('remarks'),
            ]);
    }
}
