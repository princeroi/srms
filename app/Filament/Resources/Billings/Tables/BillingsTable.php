<?php

namespace App\Filament\Resources\Billings\Tables;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Filament\Actions\Action;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\DatePicker;

class BillingsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('invoice_number')
                    ->searchable(),
                TextColumn::make('billing_title')
                    ->searchable(),
                TextColumn::make('client.client_name')
                    ->searchable(),
                TextColumn::make('billing_start_period')
                    ->date()
                    ->sortable(),
                TextColumn::make('billing_end_period')
                    ->date()
                    ->sortable(),
                TextColumn::make('billing_date')
                    ->date()
                    ->sortable(),
                TextColumn::make('due_date')
                    ->date()
                    ->sortable(),
                TextColumn::make('total_amount')
                    ->money('PHP')
                    ->sortable(),
                TextColumn::make('total_paid')
                    ->money('PHP'),
                TextColumn::make('remaining_balance')
                    ->money('PHP'),
                TextColumn::make('status')
                    ->colors([
                        'danger'        => 'overdue',
                        'warning'       => 'pending',
                        'warning'       => 'partially_paid',
                        'success'       => 'paid',
                    ])
                    ->badge(),
                TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('updated_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                //
            ])
            ->recordActions([
                EditAction::make(),

                Action::make('payment')
                    ->icon('heroicon-o-check-circle')
                    ->color('success')
                    ->form([
                        TextInput::make('collect_by')
                            ->required(),
                        TextInput::make('amount_paid')
                            ->numeric()
                            ->minValue(0.01)
                            ->rules(['gt:0'])
                            ->step(0.01)
                            ->prefix('₱')
                            ->required()
                            ->default(fn ($record) => $record->remaining_balance)
                            ->maxValue(fn ($record) => $record->remaining_balance),
                        DatePicker::make('payment_date')
                            ->default(now())
                            ->required(),
                        Select::make('payment_method')
                            ->options([
                                'cash'          => 'Cash',
                                'gcash'         => 'GCash',
                                'bank_transfer' => 'Bank Transfer',
                                'check'         => 'Check'
                            ])
                            ->required(),
                        TextInput::make('reference_number')
                    ])
                    ->action(function ($record, array $data) {
                        
                        $billing = $record;

                        $collection = $billing->collection()->create([
                            'collect_by'        => $data['collect_by'],
                            'amount_paid'       => $data['amount_paid'],
                            'payment_date'      => $data['payment_date'],
                            'payment_method'    => $data['payment_method'],
                            'reference_number'  => $data['reference_number'],
                        ]);

                        $totalPaid = $billing->collection()->sum('amount_paid');
                        $isOverdue = $billing->due_date < now()->toDateString();

                        $billing->update([
                            'status' => match(true) {
                                $totalPaid >= $billing->total_amount                        => 'paid',
                                $totalPaid > 0 && $totalPaid < $billing->total_amount      => 'partially_paid',
                                $isOverdue                                                  => 'overdue',
                                default                                                     => 'pending'
                            }
                        ]);
                    })
                    ->successNotificationTitle('Payment recorded successfully'),
                Action::make('collection_logs')
                    ->icon('heroicon-o-clipboard-document-list')
                    ->color('info')
                    ->modalHeading(fn ($record) => "Collection Logs - {$record->invoice_number}")
                    ->modalDescription(fn ($record) => "Client: {$record->client->client_name}")
                    ->modalWIdth('5xl')
                    ->modalContent(fn ($record) => view(
                        'filament.billings.collection-logs-modal',
                        ['record' => $record->load('collection')]
                    ))
                    ->modalSubmitAction(False)
                    ->modalCancelActionLabel('Close'),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ]);
    }
}
