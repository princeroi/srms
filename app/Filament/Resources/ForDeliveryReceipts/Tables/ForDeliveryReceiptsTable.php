<?php

namespace App\Filament\Resources\ForDeliveryReceipts\Tables;

use App\Models\IssuanceDr;
use Filament\Actions\Action;
use Filament\Actions\EditAction;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Forms\Components\TextInput;
use Filament\Notifications\Notification;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Support\Carbon;

class ForDeliveryReceiptsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('uniformIssuance.id')
                    ->label('Issuance ID')
                    ->placeholder('—')
                    ->searchable(),

                TextColumn::make('endorse_by')
                    ->searchable(),

                TextColumn::make('status_date')
                    ->label('Date')
                    ->getStateUsing(function ($record): ?string {
                        return match ($record->status) {
                            'pending'   => $record->endorse_date
                                ? Carbon::parse($record->endorse_date)->toFormattedDateString()
                                : null,
                            'done'      => $record->done_date
                                ? Carbon::parse($record->done_date)->toFormattedDateString()
                                : null,
                            'cancelled' => $record->cancel_date
                                ? Carbon::parse($record->cancel_date)->toFormattedDateString()
                                : null,
                            default     => null,
                        };
                    })
                    ->description(fn ($record): string => match ($record->status) {
                        'pending'   => 'Endorse Date',
                        'done'      => 'Done Date',
                        'cancelled' => 'Cancel Date',
                        default     => '',
                    })
                    ->sortable(query: function ($query, string $direction) {
                        return $query->orderByRaw("
                            CASE
                                WHEN status = 'pending'   THEN endorse_date
                                WHEN status = 'done'      THEN done_date
                                WHEN status = 'cancelled' THEN cancel_date
                            END {$direction}
                        ");
                    }),

                TextColumn::make('status')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'pending'   => 'warning',
                        'done'      => 'success',
                        'cancelled' => 'danger',
                        default     => 'gray',
                    }),

                TextColumn::make('dr_numbers_display')
                    ->label('DR Number(s)')
                    ->getStateUsing(function ($record): string {
                        $numbers = IssuanceDr::where('for_delivery_receipt_id', $record->id)
                            ->pluck('dr_number');

                        return $numbers->isNotEmpty()
                            ? $numbers->join(', ')
                            : '—';
                    }),

                TextColumn::make('remarks')
                    ->searchable(),

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
                Action::make('markDone')
                    ->label('Mark as Done')
                    ->icon('heroicon-o-check-circle')
                    ->color('success')
                    ->visible(fn ($record): bool => $record->status !== 'done')
                    ->form([
                        TextInput::make('dr_number')
                            ->label('DR Number')
                            ->required()
                            ->maxLength(255)
                            ->placeholder('Enter DR Number'),
                    ])
                    ->action(function (array $data, $record): void {
                        IssuanceDr::create([
                            'for_delivery_receipt_id' => $record->id,
                            'dr_number'               => $data['dr_number'],
                        ]);

                        $record->update([
                            'status'    => 'done',
                            'done_date' => now(),
                        ]);

                        Notification::make()
                            ->title('Marked as Done')
                            ->body("DR Number {$data['dr_number']} has been recorded.")
                            ->success()
                            ->send();
                    })
                    ->modalHeading('Mark as Done & Enter DR Number')
                    ->modalSubmitActionLabel('Confirm'),

                EditAction::make()
                    ->visible(fn ($record): bool => $record->uniform_issuance_id === null)
                    ->fillForm(function ($record): array {
                        $data = $record->toArray();

                        $latest = IssuanceDr::where('for_delivery_receipt_id', $record->id)
                            ->latest()
                            ->first();

                        $data['dr_number'] = $latest?->dr_number;

                        return $data;
                    })
                    ->using(function ($record, array $data): mixed {
                        $drNumber = $data['dr_number'] ?? null;

                        if (! empty($drNumber)) {
                            $data['status']    = 'done';
                            $data['done_date'] = $data['done_date'] ?? now();
                        }

                        unset($data['dr_number']);

                        $record->update($data);

                        if ($record->status === 'done' && ! empty($drNumber)) {
                            IssuanceDr::firstOrCreate([
                                'for_delivery_receipt_id' => $record->id,
                                'dr_number'               => $drNumber,
                            ]);
                        }

                        return $record;
                    }),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ]);
    }
}