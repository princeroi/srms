<?php

namespace App\Filament\Resources\Billings\Schemas;

use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Schemas\Schema;
use Illuminate\Support\Carbon;

class BillingForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('billing_title')
                    ->required(),
                Select::make('client_id')
                    ->relationship('client', 'client_name')
                    ->required(),
                DatePicker::make('billing_start_period')
                    ->required()
                    ->default(now()),
                DatePicker::make('billing_end_period')
                    ->required()
                    ->default(now()->addMonth()),
                DatePicker::make('billing_date')
                    ->required()
                    ->default(now()->addMonth()),
                DatePicker::make('due_date')
                    ->required()
                    ->default(now()->addMonth()),
                Select::make('status')
                    ->options([
                        'pending'         => 'Pending',
                        'partially_paid'  => 'Partially paid',
                        'paid'            => 'Paid',
                        'overdue'         => 'Overdue',
                    ])
                    ->default('pending')
                    ->hidden()
                    ->required(),
                Textarea::make('notes')
                    ->columnSpanFull(),
            ]);
    }
}