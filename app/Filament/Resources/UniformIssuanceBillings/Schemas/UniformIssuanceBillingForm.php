<?php

namespace App\Filament\Resources\UniformIssuanceBillings\Schemas;

use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Schema;

class UniformIssuanceBillingForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('uniform_issuance_id')
                    ->required()
                    ->numeric(),
                TextInput::make('billed_to')
                    ->required(),
                Select::make('billing_type')
                    ->options(['client' => 'Client', 'salary_deduct' => 'Salary deduct', 'other' => 'Other'])
                    ->default('client')
                    ->required(),
                TextInput::make('billing_items')
                    ->required(),
                TextInput::make('total_price')
                    ->required()
                    ->numeric()
                    ->default(0.0)
                    ->prefix('$'),
                Select::make('status')
                    ->options(['pending' => 'Pending', 'billed' => 'Billed'])
                    ->default('pending')
                    ->required(),
                DatePicker::make('billed_at'),
                TextInput::make('created_by')
                    ->numeric(),
            ]);
    }
}
