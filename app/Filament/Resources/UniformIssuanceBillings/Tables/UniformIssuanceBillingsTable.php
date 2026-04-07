<?php

namespace App\Filament\Resources\UniformIssuanceBillings\Tables;

use App\Models\Billing;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Filament\Actions\Action;
use Filament\Forms\Components\Select;
use Filament\Notifications\Notification;
use Illuminate\Support\Facades\DB;

class UniformIssuanceBillingsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('uniform_issuance_id')
                    ->numeric()
                    ->sortable(),
                TextColumn::make('billed_to')
                    ->searchable(),
                TextColumn::make('billing_type')
                    ->badge(),
                TextColumn::make('total_price')
                    ->money('PHP')
                    ->sortable(),
                TextColumn::make('status')
                    ->badge(),
                TextColumn::make('billed_at')
                    ->date()
                    ->sortable(),
                TextColumn::make('created_by')
                    ->numeric()
                    ->sortable(),
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

                Action::make('addToBilling')
                    ->label('Add to Billing')
                    ->icon('heroicon-o-plus-circle')
                    ->color('primary')
                    ->visible(
                        fn ($record): bool =>
                            $record->status === 'pending' &&
                            $record->billing_type === 'client'
                    )
                    ->form([
                        Select::make('billing_id')
                            ->label('Select Pending Billing')
                            ->options(function () {
                                return Billing::where('status', 'pending')
                                    ->with('client')
                                    ->get()
                                    ->mapWithKeys(fn ($billing) => [
                                        $billing->id => implode(' | ', array_filter([
                                            $billing->billing_title ?? 'No Title',
                                            $billing->client?->name,
                                            $billing->invoice_number ? "Inv #{$billing->invoice_number}" : null,
                                            $billing->billing_date?->format('M d, Y'),
                                        ])),
                                    ]);
                            })
                            ->native(true)
                            ->required()
                            ->placeholder('Choose a pending billing record'),
                    ])
                    ->modalHeading('Add to Client Billing')
                    ->modalDescription('Select a pending billing record to attach this issuance billing to.')
                    ->modalSubmitActionLabel('Add to Billing')
                    ->action(function ($record, array $data): void {
                        $billing = Billing::findOrFail($data['billing_id']);

                        DB::table('billings')
                            ->where('id', $billing->id)
                            ->update([
                                'total_amount' => DB::raw('COALESCE(total_amount, 0) + ' . (float) $record->total_price),
                            ]);

                        $record->update([
                            'status'    => 'billed',
                            'billed_at' => now(),
                        ]);

                        Notification::make()
                            ->title('Added to Billing')
                            ->body('Successfully added ₱' . number_format((float) $record->total_price, 2) . ' to "' . $billing->billing_title . '".')
                            ->success()
                            ->send();
                    }),

                // ── Bill to Employee: Mark as Billed ──────────────────────────
                Action::make('markBilled')
                    ->label('Mark as Billed')
                    ->icon('heroicon-o-check-circle')
                    ->color('success')
                    ->visible(
                        fn ($record): bool =>
                            $record->status === 'pending' &&
                            $record->billing_type === 'salary_deduct'
                    )
                    ->requiresConfirmation()
                    ->modalHeading('Mark as Billed')
                    ->modalDescription('Are you sure you want to mark this billing as Billed?')
                    ->modalSubmitActionLabel('Yes, Mark as Billed')
                    ->action(function ($record): void {
                        $record->update([
                            'status'    => 'billed',
                            'billed_at' => now(),
                        ]);

                        Notification::make()
                            ->title('Marked as Billed')
                            ->success()
                            ->send();
                    }),

                // ── ATD viewer ────────────────────────────────────────────────
                Action::make('view_atd')
                    ->label('ATD')
                    ->icon('heroicon-o-photo')
                    ->color('info')
                    ->modalHeading('ATD Images')
                    ->modalContent(function ($record) {
                        $items = $record->billingAtds;

                        if ($items->isEmpty()) {
                            return new \Illuminate\Support\HtmlString('<p class="text-center text-gray-400 py-4">No ATD records found.</p>');
                        }

                        $html = '<div class="space-y-4 p-2">';
                        foreach ($items as $item) {
                            $html .= '<div class="rounded-lg border border-gray-200 dark:border-gray-700 p-3 space-y-2">';
                            $html .= '<div class="flex items-center justify-between text-sm">';
                            $html .= '<span class="font-medium text-gray-800 dark:text-gray-200">' . e($item->employee_name) . '</span>';
                            if ($item->date_signed) {
                                $html .= '<span class="text-gray-500">Signed: ' . \Carbon\Carbon::parse($item->date_signed)->format('M d, Y') . '</span>';
                            }
                            $html .= '</div>';
                            if ($item->remarks) {
                                $html .= '<p class="text-xs text-gray-500">' . e($item->remarks) . '</p>';
                            }
                            if ($item->atd_image) {
                                $url = route('private.image', [
                                    'disk'  => 'local',
                                    'path'  => base64_encode($item->atd_image),
                                ]);
                                $html .= '<a href="' . $url . '" target="_blank">';
                                $html .= '<img src="' . $url . '" class="w-full max-h-72 object-contain rounded border border-gray-200 dark:border-gray-600 cursor-pointer hover:opacity-90 transition" />';
                                $html .= '</a>';
                                $html .= '<p class="text-xs text-gray-400 text-center">Click image to open full size</p>';
                            } else {
                                $html .= '<p class="text-sm text-gray-400 italic">No image uploaded.</p>';
                            }
                            $html .= '</div>';
                        }
                        $html .= '</div>';

                        return new \Illuminate\Support\HtmlString($html);
                    })
                    ->modalSubmitAction(false)
                    ->modalCancelActionLabel('Close')
                    ->visible(fn ($record) => $record->billingAtds()->exists()),

                // ── DR viewer ─────────────────────────────────────────────────
                Action::make('view_dr')
                    ->label('DR')
                    ->icon('heroicon-o-photo')
                    ->color('warning')
                    ->modalHeading('DR Images')
                    ->modalContent(function ($record) {
                        $items = $record->billingDrs;

                        if ($items->isEmpty()) {
                            return new \Illuminate\Support\HtmlString('<p class="text-center text-gray-400 py-4">No DR records found.</p>');
                        }

                        $html = '<div class="space-y-4 p-2">';
                        foreach ($items as $item) {
                            $html .= '<div class="rounded-lg border border-gray-200 dark:border-gray-700 p-3 space-y-2">';
                            $html .= '<div class="flex items-center justify-between text-sm">';
                            $html .= '<span class="font-medium text-gray-800 dark:text-gray-200">' . e($item->employee_name) . '</span>';
                            if ($item->date_signed) {
                                $html .= '<span class="text-gray-500">Signed: ' . \Carbon\Carbon::parse($item->date_signed)->format('M d, Y') . '</span>';
                            }
                            $html .= '</div>';
                            if ($item->remarks) {
                                $html .= '<p class="text-xs text-gray-500">' . e($item->remarks) . '</p>';
                            }
                            if ($item->dr_image) {
                                $url = route('private.image', [
                                    'disk'  => 'local',
                                    'path'  => base64_encode($item->dr_image),
                                ]);
                                $html .= '<a href="' . $url . '" target="_blank">';
                                $html .= '<img src="' . $url . '" class="w-full max-h-72 object-contain rounded border border-gray-200 dark:border-gray-600 cursor-pointer hover:opacity-90 transition" />';
                                $html .= '</a>';
                                $html .= '<p class="text-xs text-gray-400 text-center">Click image to open full size</p>';
                            } else {
                                $html .= '<p class="text-sm text-gray-400 italic">No image uploaded.</p>';
                            }
                            $html .= '</div>';
                        }
                        $html .= '</div>';

                        return new \Illuminate\Support\HtmlString($html);
                    })
                    ->modalSubmitAction(false)
                    ->modalCancelActionLabel('Close')
                    ->visible(fn ($record) => $record->billingDrs()->exists()),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ]);
    }
}