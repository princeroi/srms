<?php

namespace App\Filament\Resources\Transmittals\Schemas;

use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Schema;

class TransmittalsForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('uniform_issuance_id')
                    ->numeric(),
                TextInput::make('transmittal_number')
                    ->required(),
                TextInput::make('transmitted_by')
                    ->required(),
                TextInput::make('transmitted_to')
                    ->required(),
                Repeater::make('items_summary')
                    ->label('Items Summary')
                    ->schema([
                        TextInput::make('item')
                            ->label('Item')
                            ->required()
                            ->placeholder('Enter item description'),
                        TextInput::make('quantity')
                            ->label('Quantity')
                            ->numeric()
                            ->required()
                            ->placeholder('e.g. 1'),
                        TextInput::make('remarks')
                            ->label('Remarks')
                            ->placeholder('Optional remarks'),
                    ])
                    ->columns(3)
                    ->addActionLabel('Add Item')
                    ->required()
                    ->minItems(1)
                    ->defaultItems(1)
                    ->collapsible()
                    ->cloneable(),
                TextInput::make('purpose'),
                TextInput::make('instructions'),
                DatePicker::make('transmitted_at')
                    ->required(),
                Select::make('status')
                    ->options(['pending' => 'Pending', 'received' => 'Received'])
                    ->default('pending')
                    ->required(),
            ]);
    }
}