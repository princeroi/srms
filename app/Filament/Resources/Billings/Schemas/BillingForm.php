<?php

namespace App\Filament\Resources\Billings\Schemas;

use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Schemas\Schema;

class BillingForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('invoice_number')
                    ->required(),
                Select::make('client_id')
                    ->relationship('client', 'client_name')
                    ->required(),
                Textarea::make('notes')
                    ->columnSpanFull(),
                DatePicker::make('billing_start_period')
                    ->required(),
                DatePicker::make('billing_end_period')
                    ->required(),
                DatePicker::make('billing_date')
                    ->required(),
                DatePicker::make('due_date')
                    ->required(),
                TextInput::make('total_amount')
                    ->required()
                    ->numeric(),
                Select::make('status')
                    ->options([
            'pending' => 'Pending',
            'partially_paid' => 'Partially paid',
            'paid' => 'Paid',
            'overdue' => 'Overdue',
        ])
                    ->required(),
            ]);
    }
}
