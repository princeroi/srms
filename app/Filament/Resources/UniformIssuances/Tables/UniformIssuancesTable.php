<?php

namespace App\Filament\Resources\UniformIssuances\Tables;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use App\Filament\Resources\UniformIssuances\UniformIssuancesResource;
use Filament\Actions\Action;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Components\Section;
use Filament\Forms\Components\Placeholder;
use Illuminate\Support\HtmlString;
use App\Models\UniformItemVariants;
use App\Models\UniformItems;
use Filament\Notifications\Notification;
use Filament\Forms\Components\Fieldset;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Toggle;
use App\Models\UniformIssuanceLog;
use Illuminate\Support\Facades\Auth;
use Filament\Actions\BulkAction;
use Illuminate\Database\Eloquent\Collection;

class UniformIssuancesTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('site.site_name')
                    ->numeric()
                    ->sortable(),
                TextColumn::make('uniformIssuanceType.uniform_issuance_type_name')
                    ->numeric()
                    ->sortable(),
                TextColumn::make('uniform_issuance_status')
                    ->badge(),
                TextColumn::make('status_date')
                    ->label('Date')
                    ->date()
                    ->sortable(query: function ($query, $direction) {
                        return $query->orderByRaw("
                            CASE uniform_issuance_status
                                WHEN 'pending'   THEN pending_at
                                WHEN 'partial'   THEN partial_at
                                WHEN 'issued'    THEN issued_at
                                WHEN 'cancelled' THEN cancelled_at
                                ELSE NULL
                            END {$direction}
                        ");
                    })
                    ->getStateUsing(fn ($record) => match($record->uniform_issuance_status) {
                        'pending'   => $record->pending_at,
                        'partial'   => $record->partial_at,
                        'issued'    => $record->issued_at,
                        'cancelled' => $record->cancelled_at,
                        default     => null,
                    }),
                TextColumn::make('signed_receiving_copy')
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
                // ─── VIEW: show employees with their items ────────────────
                Action::make('view')
                    ->label('View')
                    ->color('gray')
                    ->icon('heroicon-o-eye')
                    ->modalWidth('3xl')
                    ->modalContent(function ($record) {
                        $record->loadMissing(
                            'uniformIssuanceRecipient.uniformIssuanceItem.uniformItem',
                            'uniformIssuanceRecipient.uniformIssuanceItem.uniformItemVariant',
                            'uniformIssuanceRecipient.position'
                        );

                        $html = '';

                        foreach ($record->uniformIssuanceRecipient as $recipient) {
                            $employee       = e($recipient->employee_name ?? '—');
                            $position       = e($recipient->position?->position_name ?? '—');
                            $employeeStatus = $recipient->employee_status ?? null;

                            $statusBadgeHtml = '';
                            if ($employeeStatus) {
                                $statusColor = match(strtolower($employeeStatus)) {
                                    'regular'   => '#16a34a',
                                    'reliever'  => '#d97706',
                                    'probationary' => '#2563eb',
                                    'contractual'  => '#7c3aed',
                                    default     => '#6b7280',
                                };
                                $statusLabel     = strtoupper(e($employeeStatus));
                                $statusBadgeHtml = "<span style='background:{$statusColor};color:#fff;font-size:9px;font-weight:700;padding:2px 8px;border-radius:999px;margin-left:6px;'>{$statusLabel}</span>";
                            }

                            $itemRows = '';
                            $totalQty       = 0;
                            $totalReleased  = 0;
                            $totalRemaining = 0;

                            foreach ($recipient->uniformIssuanceItem as $i => $item) {
                                $itemName  = e($item->uniformItem?->uniform_item_name ?? '—');
                                $size      = e($item->uniformItemVariant?->uniform_item_size ?? '—');
                                $qty       = (int) $item->quantity;
                                $released  = (int) $item->released_quantity;
                                $remaining = (int) $item->remaining_quantity;

                                $totalQty       += $qty;
                                $totalReleased  += $released;
                                $totalRemaining += $remaining;

                                $releasedColor  = $released  > 0 ? '#16a34a' : '#9ca3af';
                                $remainingColor = $remaining > 0 ? '#d97706' : '#9ca3af';
                                $bg = $i % 2 === 0 ? '#ffffff' : '#f8fafc';

                                $itemRows .= "
                                    <tr style='background:{$bg};'>
                                        <td style='padding:7px 10px;border-bottom:1px solid #e5e7eb;font-size:12px;color:#111827;font-weight:500;'>{$itemName}</td>
                                        <td style='padding:7px 10px;border-bottom:1px solid #e5e7eb;font-size:12px;color:#374151;text-align:center;'>{$size}</td>
                                        <td style='padding:7px 10px;border-bottom:1px solid #e5e7eb;font-size:12px;font-weight:700;text-align:center;color:#1d4ed8;'>{$qty}</td>
                                        <td style='padding:7px 10px;border-bottom:1px solid #e5e7eb;font-size:12px;font-weight:700;text-align:center;color:{$releasedColor};'>{$released}</td>
                                        <td style='padding:7px 10px;border-bottom:1px solid #e5e7eb;font-size:12px;font-weight:700;text-align:center;color:{$remainingColor};'>{$remaining}</td>
                                    </tr>";
                            }

                            // Totals footer row
                            $itemRows .= "
                                <tr style='background:#eff6ff;border-top:2px solid #93c5fd;'>
                                    <td colspan='2' style='padding:6px 10px;font-size:11px;font-weight:700;color:#374151;text-align:right;border-right:1px solid #cbd5e1;'>TOTAL</td>
                                    <td style='padding:6px 10px;font-size:13px;font-weight:900;color:#1d4ed8;text-align:center;border-right:1px solid #cbd5e1;'>{$totalQty}</td>
                                    <td style='padding:6px 10px;font-size:13px;font-weight:900;color:#16a34a;text-align:center;border-right:1px solid #cbd5e1;'>{$totalReleased}</td>
                                    <td style='padding:6px 10px;font-size:13px;font-weight:900;color:#d97706;text-align:center;'>{$totalRemaining}</td>
                                </tr>";

                            $html .= "
                                <div style='border:1px solid #e5e7eb;border-radius:8px;margin-bottom:16px;overflow:hidden;box-shadow:0 1px 3px rgba(0,0,0,.06);'>
                                    <div style='background:#1e3a5f;padding:10px 14px;display:flex;justify-content:space-between;align-items:center;'>
                                        <div>
                                            <div style='font-size:13px;font-weight:700;color:#fff;display:flex;align-items:center;gap:4px;'>
                                                {$employee}{$statusBadgeHtml}
                                            </div>
                                            <div style='font-size:11px;color:#93c5fd;margin-top:2px;'>{$position}</div>
                                        </div>
                                    </div>
                                    <table style='width:100%;border-collapse:collapse;'>
                                        <thead>
                                            <tr style='background:#f1f5f9;'>
                                                <th style='padding:7px 10px;text-align:left;font-size:10px;font-weight:700;color:#475569;text-transform:uppercase;letter-spacing:.05em;border-bottom:1px solid #e2e8f0;'>Item</th>
                                                <th style='padding:7px 10px;text-align:center;font-size:10px;font-weight:700;color:#475569;text-transform:uppercase;letter-spacing:.05em;border-bottom:1px solid #e2e8f0;width:60px;'>Size</th>
                                                <th style='padding:7px 10px;text-align:center;font-size:10px;font-weight:700;color:#1d4ed8;text-transform:uppercase;letter-spacing:.05em;border-bottom:1px solid #e2e8f0;width:60px;'>Qty</th>
                                                <th style='padding:7px 10px;text-align:center;font-size:10px;font-weight:700;color:#16a34a;text-transform:uppercase;letter-spacing:.05em;border-bottom:1px solid #e2e8f0;width:70px;'>Released</th>
                                                <th style='padding:7px 10px;text-align:center;font-size:10px;font-weight:700;color:#d97706;text-transform:uppercase;letter-spacing:.05em;border-bottom:1px solid #e2e8f0;width:75px;'>Remaining</th>
                                            </tr>
                                        </thead>
                                        <tbody>{$itemRows}</tbody>
                                    </table>
                                </div>";
                        }

                        return new \Illuminate\Support\HtmlString("
                            <div style='max-height:600px;overflow-y:auto;padding:2px;'>
                                {$html}
                            </div>
                        ");
                    })
                    ->modalSubmitAction(false)
                    ->modalCancelActionLabel('Close'),

                // ─── EDIT: only when pending ───────────────────────────────
                EditAction::make()
                    ->visible(fn ($record) => $record->uniform_issuance_status === 'pending')
                    ->after(function ($record) {
                        UniformIssuancesResource::syncQuantities($record);

                        UniformIssuanceLog::create([
                            'uniform_issuance_id' => $record->id,
                            'user_id'             => Auth::id(),
                            'action'              => 'edited',
                            'status_from'         => null,
                            'status_to'           => $record->uniform_issuance_status,
                            'note'                => 'Record was edited.',
                        ]);
                    }),

                // ─── ISSUED: pending or partial ────────────────────────────
                Action::make('issued')
                    ->color('success')
                    ->icon('heroicon-o-check-circle')
                    ->requiresConfirmation()
                    ->modalWidth('2xl')
                    ->visible(fn ($record) => in_array($record->uniform_issuance_status, ['pending', 'partial']))
                    ->form(function ($record) {
                        $fields = [];

                        foreach ($record->uniformIssuanceRecipient as $recipient) {
                            $items = [];

                            foreach ($recipient->uniformIssuanceItem as $item) {
                                $remaining = (int) $item->remaining_quantity;

                                if ($remaining <= 0) continue;

                                $items[] = TextInput::make("item_{$item->id}_released")
                                    ->label("{$item->uniformItem->uniform_item_name} : {$item->uniformItemVariant->uniform_item_size} (qty: {$remaining})")
                                    ->numeric()
                                    ->default($remaining)
                                    ->minValue(0)
                                    ->maxValue($remaining)
                                    ->required();
                            }

                            if (!empty($items)) {
                                $fields[] = Placeholder::make("recipient")
                                    ->label('')
                                    ->content(new HtmlString("<strong style='font-size:1rem;'>{$recipient->employee_name}</strong>"))
                                    ->columnSpanFull();

                                foreach ($items as $item) {
                                    $fields[] = $item;
                                }
                            }
                        }
                        return $fields;
                    })
                    ->action(function ($record, array $data, Action $action) {
                        foreach ($record->uniformIssuanceRecipient as $recipient) {
                            foreach ($recipient->uniformIssuanceItem as $item) {
                                $released = (int) ($data["item_{$item->id}_released"] ?? 0);

                                if ($released > 0) {
                                    $variant = UniformItemVariants::find($item->uniform_item_variant_id);

                                    if (!$variant) {
                                        Notification::make()->title('Variant Not Found')->danger()->send();
                                        $action->halt();
                                        return;
                                    }

                                    if ((int) $variant->uniform_item_quantity < $released) {
                                        Notification::make()
                                            ->title('Insufficient Stock')
                                            ->body("'{$item->uniformItem->uniform_item_name} - {$item->uniformItemVariant->uniform_item_size}' only has {$variant->uniform_item_quantity} in stock but you are trying to issue {$released}.")
                                            ->danger()
                                            ->send();
                                        $action->halt();
                                        return;
                                    }
                                }
                            }
                        }

                        $totalRemaining = 0;
                        $totalReleased  = 0;

                        foreach ($record->uniformIssuanceRecipient as $recipient) {
                            foreach ($recipient->uniformIssuanceItem as $item) {
                                $newlyReleased = (int) ($data["item_{$item->id}_released"] ?? 0);

                                if ($newlyReleased > 0) {
                                    $item->update([
                                        'released_quantity'  => (int) $item->released_quantity + $newlyReleased,
                                        'remaining_quantity' => (int) $item->remaining_quantity - $newlyReleased,
                                    ]);

                                    $variant = UniformItemVariants::find($item->uniform_item_variant_id);
                                    if ($variant) {
                                        $variant->decrement('uniform_item_quantity', $newlyReleased);
                                    }
                                }

                                $item->refresh();
                                $totalReleased  += (int) $item->released_quantity;
                                $totalRemaining += (int) $item->remaining_quantity;
                            }
                        }

                        if ($totalRemaining === 0) {
                            $status = 'issued';
                        } elseif ($totalReleased === 0) {
                            $status = 'pending';
                        } else {
                            $status = 'partial';
                        }

                        $record->update([
                            'uniform_issuance_status' => $status,
                            'issued_at'               => $status === 'issued' ? now()->toDateString() : null,
                            'partial_at'              => $status === 'partial' ? now()->toDateString() : null,
                        ]);

                        $note = [];
                        foreach ($record->uniformIssuanceRecipient as $recipient) {
                            foreach ($recipient->uniformIssuanceItem as $item) {
                                $newlyReleased = (int) ($data["item_{$item->id}_released"] ?? 0);
                                if ($newlyReleased > 0) {
                                    $note[] = [
                                        'label'    => "{$item->uniformItem->uniform_item_name} ({$item->uniformItemVariant->uniform_item_size}) — {$recipient->employee_name}",
                                        'released' => $newlyReleased,
                                    ];
                                }
                            }
                        }

                        UniformIssuanceLog::create([
                            'uniform_issuance_id' => $record->id,
                            'user_id'             => Auth::id(),
                            'action'              => $status,
                            'status_from'         => $record->getOriginal('uniform_issuance_status'),
                            'status_to'           => $status,
                            'note'                => json_encode($note),
                        ]);

                        if ($status === 'issued') {
                            Notification::make()->title('Issued')->body('All items have been fully issued.')->success()->send();
                        } elseif ($status === 'partial') {
                            Notification::make()->title('Partial Issued')->body('Some items have been issued. Remaining items are still pending.')->warning()->send();
                        }
                    }),

                // ─── CANCEL: only when pending ─────────────────────────────
                Action::make('cancel')
                    ->color('danger')
                    ->icon('heroicon-o-x-circle')
                    ->requiresConfirmation()
                    ->visible(fn ($record) => $record->uniform_issuance_status === 'pending')
                    ->action(function ($record) {
                        $record->update([
                            'uniform_issuance_status' => 'cancelled',
                            'cancelled_at'            => now()->toDateString(),
                        ]);

                        UniformIssuanceLog::create([
                            'uniform_issuance_id' => $record->id,
                            'user_id'             => Auth::id(),
                            'action'              => 'cancelled',
                            'status_from'         => $record->uniform_issuance_status,
                            'status_to'           => 'cancelled',
                            'note'                => 'Issuance was cancelled.',
                        ]);

                        Notification::make()->title('Cancelled')->body('Issuance has been cancelled.')->danger()->send();
                    }),

                // ─── CHANGE ITEM: issued or partial ───────────────────────
                // ─── CHANGE ITEM: issued or partial ───────────────────────
                Action::make('change_item')
                    ->color('info')
                    ->icon('heroicon-o-arrows-right-left')
                    ->modalWidth('3xl')
                    ->visible(function ($record) {
                        if (!in_array($record->uniform_issuance_status, ['issued', 'partial'])) {
                            return false;
                        }
                        if ($record->is_for_transmit) {
                            return false;
                        }
                        if (\App\Models\UniformIssuanceBilling::where('uniform_issuance_id', $record->id)->exists()) {
                            return false;
                        }
                        return true;
                    })
                    ->form(function ($record) {
                        $isPartial       = $record->uniform_issuance_status === 'partial';
                        $employeeOptions = [];
                        $itemsByEmployee = [];

                        foreach ($record->uniformIssuanceRecipient as $recipient) {
                            $name  = $recipient->employee_name;
                            $items = [];

                            foreach ($recipient->uniformIssuanceItem as $item) {
                                $released = (int) $item->released_quantity;
                                if ($isPartial && $released <= 0) continue;

                                $qty              = $isPartial ? $released : (int) $item->quantity;
                                $items[$item->id] = "{$item->uniformItem->uniform_item_name} ({$item->uniformItemVariant->uniform_item_size}) × {$qty}";
                            }

                            if (!empty($items)) {
                                $employeeOptions[$name] = $name;
                                $itemsByEmployee[$name] = $items;
                            }
                        }

                        $itemsByEmployeeJson = htmlspecialchars(json_encode($itemsByEmployee), ENT_QUOTES);

                        return [
                            Placeholder::make('_map')
                                ->label('')
                                ->content(new HtmlString(
                                    "<script>window._changeItemMap = {$itemsByEmployeeJson};</script>"
                                ))
                                ->columnSpanFull(),

                            \Filament\Forms\Components\Repeater::make('changes')
                                ->label('Items to Change')
                                ->addActionLabel('+ Add Another Change')
                                ->minItems(1)
                                ->defaultItems(1)
                                ->columnSpanFull()
                                ->schema([
                                    Select::make('employee')
                                        ->label('Employee')
                                        ->options($employeeOptions)
                                        ->required()
                                        ->live()
                                        ->afterStateUpdated(function (callable $set) {
                                            $set('from_item_id', null);
                                        }),

                                    Select::make('from_item_id')
                                        ->label('Item to Change')
                                        ->options(function (callable $get) use ($itemsByEmployee) {
                                            $emp = $get('employee');
                                            return $emp ? ($itemsByEmployee[$emp] ?? []) : [];
                                        })
                                        ->required()
                                        ->live()
                                        ->searchable(),

                                    TextInput::make('change_qty')
                                        ->label('Quantity to Change')
                                        ->helperText('How many of this item are being swapped out')
                                        ->numeric()
                                        ->minValue(1)
                                        ->required(),

                                    Select::make('to_item_id')
                                        ->label('Replacement Item')
                                        ->options(UniformItems::pluck('uniform_item_name', 'id'))
                                        ->required()
                                        ->live()
                                        ->searchable()
                                        ->afterStateUpdated(function (callable $set) {
                                            $set('to_variant_id', null);
                                        }),

                                    Select::make('to_variant_id')
                                        ->label('Replacement Size / Variant')
                                        ->options(function (callable $get) {
                                            $itemId = $get('to_item_id');
                                            if (!$itemId) return [];
                                            return UniformItemVariants::where('uniform_item_id', $itemId)
                                                ->pluck('uniform_item_size', 'id');
                                        })
                                        ->required()
                                        ->live()
                                        ->searchable(),

                                    TextInput::make('replacement_qty')
                                        ->label('Replacement Quantity')
                                        ->helperText('How many replacement items to issue')
                                        ->numeric()
                                        ->minValue(1)
                                        ->required(),
                                ]),
                        ];
                    })
                    ->action(function ($record, array $data, Action $action) {
                        $isPartial = $record->uniform_issuance_status === 'partial';
                        $changes   = $data['changes'] ?? [];

                        if (empty($changes)) {
                            Notification::make()->title('No Changes')->body('Add at least one change.')->warning()->send();
                            $action->halt();
                            return;
                        }

                        // ── Validate all rows first before making any changes ──
                        $resolved = [];
                        foreach ($changes as $idx => $row) {
                            $fromItemId     = (int) ($row['from_item_id'] ?? 0);
                            $changeQty      = (int) ($row['change_qty'] ?? 0);
                            $toItemId       = (int) ($row['to_item_id'] ?? 0);
                            $toVariantId    = (int) ($row['to_variant_id'] ?? 0);
                            $replacementQty = (int) ($row['replacement_qty'] ?? 0);

                            if (!$fromItemId || !$toVariantId || $changeQty <= 0 || $replacementQty <= 0) {
                                Notification::make()
                                    ->title('Row ' . ($idx + 1) . ' is incomplete')
                                    ->warning()
                                    ->send();
                                $action->halt();
                                return;
                            }

                            $issuanceItem    = null;
                            $recipientRecord = null;

                            foreach ($record->uniformIssuanceRecipient as $recipient) {
                                $found = $recipient->uniformIssuanceItem->firstWhere('id', $fromItemId);
                                if ($found) {
                                    $issuanceItem    = $found;
                                    $recipientRecord = $recipient;
                                    break;
                                }
                            }

                            if (!$issuanceItem) {
                                Notification::make()->title('Item not found')->danger()->send();
                                $action->halt();
                                return;
                            }

                            $currentReleased = (int) $issuanceItem->released_quantity;
                            if ($changeQty > $currentReleased) {
                                Notification::make()
                                    ->title('Change Qty Exceeds Released')
                                    ->body("You can only change up to {$currentReleased} of that item.")
                                    ->danger()
                                    ->send();
                                $action->halt();
                                return;
                            }

                            $toVariant = UniformItemVariants::find($toVariantId);
                            if (!$toVariant) {
                                Notification::make()->title('Replacement Variant Not Found')->danger()->send();
                                $action->halt();
                                return;
                            }

                            if ((int) $toVariant->uniform_item_quantity < $replacementQty) {
                                Notification::make()
                                    ->title('Insufficient Stock')
                                    ->body("'{$toVariant->uniformItem?->uniform_item_name} - {$toVariant->uniform_item_size}' only has {$toVariant->uniform_item_quantity} in stock but you need {$replacementQty}.")
                                    ->danger()
                                    ->send();
                                $action->halt();
                                return;
                            }

                            $resolved[] = [
                                'item'            => $issuanceItem,
                                'recipient'       => $recipientRecord,
                                'change_qty'      => $changeQty,
                                'to_item_id'      => $toItemId,
                                'to_variant_id'   => $toVariantId,
                                'to_variant'      => $toVariant,
                                'replacement_qty' => $replacementQty,
                            ];
                        }

                        // ── Apply all changes ──
                        $changeNote = [];

                        foreach ($resolved as $r) {
                            $item             = $r['item'];
                            $recipient        = $r['recipient'];
                            $changeQty        = $r['change_qty'];
                            $toVariantId      = $r['to_variant_id'];
                            $toItemId         = $r['to_item_id'];
                            $replacementQty   = $r['replacement_qty'];
                            $currentReleased  = (int) $item->released_quantity;
                            $currentRemaining = (int) $item->remaining_quantity;
                            $currentQty       = (int) $item->quantity;

                            $oldVariant   = UniformItemVariants::find($item->uniform_item_variant_id);
                            $newVariant   = UniformItemVariants::find($toVariantId);
                            $oldItemModel = \App\Models\UniformItems::find($item->uniform_item_id);
                            $newItemModel = \App\Models\UniformItems::find($toItemId);

                            // ── Adjust stock ──
                            if ($oldVariant && $changeQty > 0) {
                                $oldVariant->increment('uniform_item_quantity', $changeQty);
                            }
                            if ($newVariant) {
                                $newVariant->decrement('uniform_item_quantity', $replacementQty);
                            }

                            // ── Update or delete the original issuance item ──
                            $reducedReleased = $currentReleased - $changeQty;
                            $reducedQty      = $currentQty - $changeQty;

                            if ($reducedReleased <= 0 && $currentRemaining <= 0) {
                                $item->delete();
                            } else {
                                $item->update([
                                    'quantity'           => max(0, $reducedQty),
                                    'released_quantity'  => max(0, $reducedReleased),
                                    'remaining_quantity' => $isPartial
                                        ? $currentRemaining
                                        : max(0, $reducedQty - max(0, $reducedReleased)),
                                ]);
                            }

                            // ── Upsert replacement item ──
                            // If same item+variant already exists for this recipient, increment quantities
                            $existingIssuanceItem = \App\Models\UniformIssuanceItems::where([
                                'uniform_issuance_recipient_id' => $recipient->id,
                                'uniform_item_id'               => $toItemId,
                                'uniform_item_variant_id'       => $toVariantId,
                            ])->first();

                            if ($existingIssuanceItem) {
                                $existingIssuanceItem->increment('quantity',          $replacementQty);
                                $existingIssuanceItem->increment('released_quantity', $replacementQty);
                                // remaining_quantity stays as-is (already fully issued)
                            } else {
                                $newIssuanceItem = $item->replicate(['id', 'created_at', 'updated_at']);
                                $newIssuanceItem->uniform_item_id         = $toItemId;
                                $newIssuanceItem->uniform_item_variant_id = $toVariantId;
                                $newIssuanceItem->quantity                = $replacementQty;
                                $newIssuanceItem->released_quantity       = $replacementQty;
                                $newIssuanceItem->remaining_quantity      = 0;
                                $newIssuanceItem->save();
                            }

                            $changeNote[] = [
                                'label'           => $recipient->employee_name,
                                'released'        => $replacementQty,
                                '_from'           => "{$oldItemModel?->uniform_item_name} ({$oldVariant?->uniform_item_size}) × {$changeQty}",
                                '_to'             => "{$newItemModel?->uniform_item_name} ({$newVariant?->uniform_item_size}) × {$replacementQty}",
                                '_new_item_name'  => $newItemModel?->uniform_item_name ?? '—',
                                '_new_item_size'  => $newVariant?->uniform_item_size ?? '—',
                                '_employee'       => $recipient->employee_name,
                                '_old_item_id'    => $item->uniform_item_id,
                                '_old_variant_id' => $item->uniform_item_variant_id,
                                '_change_qty'     => $changeQty,
                                '_release_label'  => "{$newItemModel?->uniform_item_name} ({$newVariant?->uniform_item_size}) — {$recipient->employee_name}",
                            ];
                        }

                        // ── Log: item_changed ──
                        UniformIssuanceLog::create([
                            'uniform_issuance_id' => $record->id,
                            'user_id'             => Auth::id(),
                            'action'              => 'item_changed',
                            'status_from'         => $record->uniform_issuance_status,
                            'status_to'           => $record->uniform_issuance_status,
                            'note'                => json_encode($changeNote),
                        ]);

                        // ── Log: item_released ──
                        $releaseNote = array_map(fn ($c) => [
                            'label'    => $c['_release_label'],
                            'released' => $c['released'],
                        ], $changeNote);

                        UniformIssuanceLog::create([
                            'uniform_issuance_id' => $record->id,
                            'user_id'             => Auth::id(),
                            'action'              => 'item_released',
                            'status_from'         => $record->uniform_issuance_status,
                            'status_to'           => $record->uniform_issuance_status,
                            'note'                => json_encode($releaseNote),
                        ]);

                        // ── Sync transmittal items_summary if any exist ──
                        $existingTransmittals = \App\Models\Transmittals::where('uniform_issuance_id', $record->id)->get();

                        if ($existingTransmittals->count() > 0) {
                            $freshRecord = \App\Models\UniformIssuances::with([
                                'uniformIssuanceRecipient.uniformIssuanceItem.uniformItem',
                                'uniformIssuanceRecipient.uniformIssuanceItem.uniformItemVariant',
                            ])->find($record->id);

                            $summaryMap = [];
                            foreach ($freshRecord->uniformIssuanceRecipient as $recipient) {
                                foreach ($recipient->uniformIssuanceItem as $item) {
                                    $qty = (int) ($item->released_quantity ?: $item->quantity);
                                    if ($qty <= 0) continue;
                                    $itemName = $item->uniformItem?->uniform_item_name ?? '—';
                                    $size     = $item->uniformItemVariant?->uniform_item_size ?? '—';
                                    $key      = $itemName . '||' . $size;
                                    if (!isset($summaryMap[$key])) {
                                        $summaryMap[$key] = ['item_name' => $itemName, 'size' => $size, 'qty' => 0];
                                    }
                                    $summaryMap[$key]['qty'] += $qty;
                                }
                            }
                            $newSummary = array_values($summaryMap);

                            foreach ($existingTransmittals as $txn) {
                                $txn->update(['items_summary' => $newSummary]);
                            }
                        }

                        Notification::make()
                            ->title('Items Changed')
                            ->body(count($changeNote) . ' change(s) applied successfully.')
                            ->success()
                            ->send();
                    }),

                // ─── LOGS ──────────────────────────────────────────────────
                Action::make('view_logs')
                    ->label('Logs')
                    ->color('gray')
                    ->icon('heroicon-o-clock')
                    ->modalContent(function ($record) {
                        $logs = \App\Models\UniformIssuanceLog::where('uniform_issuance_id', $record->id)
                            ->with('user')
                            ->latest()
                            ->get();

                        $rows = $logs->map(function ($log) {
                            $user   = $log->user?->name ?? 'System';
                            $date   = \Carbon\Carbon::parse($log->created_at)->timezone('Asia/Manila')->format('M d, Y h:i A');
                            $from   = $log->status_from ?? '—';
                            $to     = $log->status_to ?? '—';
                            $action = strtoupper($log->action);

                            $badgeColor = match($log->action) {
                                'issued'        => '#16a34a',
                                'partial'       => '#d97706',
                                'cancelled'     => '#dc2626',
                                'item_changed'  => '#2563eb',
                                'item_released' => '#0d9488',
                                'created'       => '#7c3aed',
                                'edited'        => '#0891b2',
                                default         => '#6b7280',
                            };

                            $itemsHtml = '';
                            if (!empty($log->note)) {
                                $noteData = json_decode($log->note, true);
                                if (is_array($noteData)) {
                                    if ($log->action === 'item_changed') {
                                        $itemsHtml = "<div style='margin-top:6px;padding:6px 8px;background:#f8fafc;border-radius:6px;border:1px solid #e2e8f0;'>";
                                        $itemsHtml .= "<div style='font-size:10px;font-weight:700;color:#475569;margin-bottom:4px;'>ITEM CHANGES:</div>";
                                        foreach ($noteData as $row) {
                                            $employee = e($row['label'] ?? '—');
                                            $fromStr  = e($row['_from'] ?? '—');
                                            $toStr    = e($row['_to'] ?? '—');
                                            $itemsHtml .= "
                                                <div style='font-size:11px;color:#374151;padding:3px 0;border-bottom:1px dashed #e5e7eb;'>
                                                    <span style='font-weight:600;color:#1e3a5f;'>{$employee}</span><br>
                                                    <span style='color:#dc2626;'>FROM: {$fromStr}</span><br>
                                                    <span style='color:#16a34a;'>TO: &nbsp;&nbsp; {$toStr}</span>
                                                </div>";
                                        }
                                        $itemsHtml .= "</div>";
                                    } elseif (in_array($log->action, ['issued', 'partial', 'item_released'])) {
                                        $itemsHtml = "<div style='margin-top:6px;padding:6px 8px;background:#f0fdf4;border-radius:6px;border:1px solid #bbf7d0;'>";
                                        $itemsHtml .= "<div style='font-size:10px;font-weight:700;color:#166534;margin-bottom:4px;'>ITEMS RELEASED:</div>";
                                        foreach ($noteData as $row) {
                                            $label    = e($row['label'] ?? '—');
                                            $released = (int) ($row['released'] ?? 0);
                                            $itemsHtml .= "
                                                <div style='font-size:11px;color:#374151;padding:3px 0;border-bottom:1px dashed #d1fae5;display:flex;justify-content:space-between;'>
                                                    <span>{$label}</span>
                                                    <span style='font-weight:700;color:#16a34a;'>×{$released}</span>
                                                </div>";
                                        }
                                        $itemsHtml .= "</div>";
                                    } else {
                                        $itemsHtml = "<div style='margin-top:4px;font-size:11px;color:#6b7280;font-style:italic;'>" . e($log->note) . "</div>";
                                    }
                                } else {
                                    $itemsHtml = "<div style='margin-top:4px;font-size:11px;color:#6b7280;font-style:italic;'>" . e($log->note) . "</div>";
                                }
                            }

                            return "
                                <tr>
                                    <td style='padding:10px;border-bottom:1px solid #e5e7eb;vertical-align:top;'>
                                        <div style='font-size:11px;color:#374151;white-space:nowrap;'>{$date}</div>
                                        <div style='font-size:10px;color:#9ca3af;margin-top:2px;'>{$user}</div>
                                    </td>
                                    <td style='padding:10px;border-bottom:1px solid #e5e7eb;vertical-align:top;'>
                                        <span style='background:{$badgeColor};color:#fff;font-size:10px;font-weight:700;padding:2px 8px;border-radius:999px;'>{$action}</span>
                                    </td>
                                    <td style='padding:10px;border-bottom:1px solid #e5e7eb;vertical-align:top;'>
                                        <div style='font-size:11px;color:#374151;'>{$from} → {$to}</div>
                                        {$itemsHtml}
                                    </td>
                                </tr>";
                        })->implode('');

                        return new \Illuminate\Support\HtmlString("
                            <div style='max-height:500px;overflow-y:auto;'>
                                <table style='width:100%;border-collapse:collapse;'>
                                    <thead style='position:sticky;top:0;z-index:1;'>
                                        <tr style='background:#1e3a5f;'>
                                            <th style='padding:8px 10px;text-align:left;font-size:11px;color:#fff;white-space:nowrap;'>Date / By</th>
                                            <th style='padding:8px 10px;text-align:left;font-size:11px;color:#fff;'>Action</th>
                                            <th style='padding:8px 10px;text-align:left;font-size:11px;color:#fff;'>Details</th>
                                        </tr>
                                    </thead>
                                    <tbody>{$rows}</tbody>
                                </table>
                            </div>
                        ");
                    })
                    ->modalSubmitAction(false)
                    ->modalCancelActionLabel('Close'),

                // ─── RECEIVING COPY ────────────────────────────────────────
                Action::make('receiving_copy')
                    ->label('Receiving Copy')
                    ->color('gray')
                    ->icon('heroicon-o-document-text')
                    ->visible(fn ($record) => in_array($record->uniform_issuance_status, ['partial', 'issued']))
                    ->modalContent(function ($record) {
                        $siteName = e($record->site->site_name ?? '—');
                        $typeName = e($record->uniformIssuanceType->uniform_issuance_type_name ?? '—');
                        $printUrl = route('uniform-issuances.receiving-copy', $record->id);

                        $allLogs = \App\Models\UniformIssuanceLog::where('uniform_issuance_id', $record->id)
                            ->whereIn('action', ['issued', 'partial', 'item_changed', 'item_released'])
                            ->with('user')
                            ->latest()
                            ->get();

                        $buildCompleteCard = function () use ($record, $siteName, $typeName, $printUrl, $allLogs): string {
                            $rows = '';
                            foreach ($record->uniformIssuanceRecipient as $recipient) {
                                foreach ($recipient->uniformIssuanceItem as $item) {
                                    $released = (int) $item->released_quantity;
                                    if ($released <= 0) continue;
                                    $label = e("{$item->uniformItem->uniform_item_name} ({$item->uniformItemVariant->uniform_item_size}) — {$recipient->employee_name}");
                                    $rows .= "
                                        <tr>
                                            <td style='padding:6px 8px;border:1px solid #d1d5db;font-size:12px;'>{$label}</td>
                                            <td style='padding:6px 8px;border:1px solid #d1d5db;font-size:12px;text-align:center;font-weight:700;'>{$released}</td>
                                        </tr>";
                                }
                            }

                            $finalStatus     = $record->uniform_issuance_status === 'issued' ? 'FULLY ISSUED' : 'PARTIAL';
                            $finalBadgeColor = $record->uniform_issuance_status === 'issued' ? '#16a34a' : '#d97706';
                            $lastLog         = $allLogs->last();
                            $lastDate        = $lastLog
                                ? \Carbon\Carbon::parse($lastLog->created_at)->timezone('Asia/Manila')->format('M d, Y h:i A')
                                : now()->timezone('Asia/Manila')->format('M d, Y h:i A');
                            $lastUser = $lastLog?->user?->name ?? 'System';

                            return "
                                <div style='border:2px solid #1e3a5f;border-radius:8px;padding:16px;margin-bottom:16px;background:#f0f4ff;box-shadow:0 2px 6px rgba(0,0,0,.08);'>
                                    <div style='display:flex;justify-content:space-between;align-items:flex-start;margin-bottom:10px;'>
                                        <div>
                                            <div style='font-size:14px;font-weight:800;color:#1e3a5f;'>Complete Receiving Copy</div>
                                            <div style='font-size:11px;color:#6b7280;margin-top:2px;'>{$siteName} &bull; {$typeName}</div>
                                            <div style='font-size:10px;color:#9ca3af;margin-top:1px;'>{$lastDate} &bull; by {$lastUser}</div>
                                        </div>
                                        <span style='background:{$finalBadgeColor};color:#fff;font-size:10px;font-weight:700;padding:3px 10px;border-radius:999px;white-space:nowrap;'>{$finalStatus}</span>
                                    </div>
                                    <table style='width:100%;border-collapse:collapse;'>
                                        <thead>
                                            <tr style='background:#1e3a5f;'>
                                                <th style='padding:6px 8px;text-align:left;font-size:11px;color:#fff;border:1px solid #1e3a5f;'>Item / Recipient</th>
                                                <th style='padding:6px 8px;text-align:center;font-size:11px;color:#fff;border:1px solid #1e3a5f;width:80px;'>Total Qty</th>
                                            </tr>
                                        </thead>
                                        <tbody>{$rows}</tbody>
                                    </table>
                                    <div style='margin-top:10px;text-align:right;'>
                                        <a href='{$printUrl}' target='_blank' style='font-size:11px;color:#2563eb;text-decoration:underline;'>
                                            Open Printable Copy ↗
                                        </a>
                                    </div>
                                </div>";
                        };

                        $buildIssuanceCard = function (
                            string $title,
                            string $badgeColor,
                            string $badgeLabel,
                            string $date,
                            string $byUser,
                            array  $noteData,
                            string $cardPrintUrl
                        ) use ($siteName, $typeName): string {
                            $rows = '';
                            foreach ($noteData as $row) {
                                $label    = e($row['label'] ?? '—');
                                $released = (int) ($row['released'] ?? 0);
                                $rows .= "
                                    <tr>
                                        <td style='padding:6px 8px;border:1px solid #d1d5db;font-size:12px;'>{$label}</td>
                                        <td style='padding:6px 8px;border:1px solid #d1d5db;font-size:12px;text-align:center;font-weight:700;'>{$released}</td>
                                    </tr>";
                            }

                            return "
                                <div style='border:1px solid #e5e7eb;border-radius:8px;padding:16px;margin-bottom:16px;background:#fff;box-shadow:0 1px 3px rgba(0,0,0,.06);'>
                                    <div style='display:flex;justify-content:space-between;align-items:flex-start;margin-bottom:10px;'>
                                        <div>
                                            <div style='font-size:13px;font-weight:700;color:#1e3a5f;'>{$title}</div>
                                            <div style='font-size:11px;color:#6b7280;margin-top:2px;'>{$siteName} &bull; {$typeName}</div>
                                            <div style='font-size:10px;color:#9ca3af;margin-top:1px;'>{$date} &bull; by {$byUser}</div>
                                        </div>
                                        <span style='background:{$badgeColor};color:#fff;font-size:10px;font-weight:700;padding:3px 10px;border-radius:999px;white-space:nowrap;'>{$badgeLabel}</span>
                                    </div>
                                    <table style='width:100%;border-collapse:collapse;'>
                                        <thead>
                                            <tr style='background:#1e3a5f;'>
                                                <th style='padding:6px 8px;text-align:left;font-size:11px;color:#fff;border:1px solid #1e3a5f;'>Item / Recipient</th>
                                                <th style='padding:6px 8px;text-align:center;font-size:11px;color:#fff;border:1px solid #1e3a5f;width:80px;'>Qty</th>
                                            </tr>
                                        </thead>
                                        <tbody>{$rows}</tbody>
                                    </table>
                                    <div style='margin-top:10px;text-align:right;'>
                                        <a href='{$cardPrintUrl}' target='_blank' style='font-size:11px;color:#2563eb;text-decoration:underline;'>
                                            Open Printable Copy ↗
                                        </a>
                                    </div>
                                </div>";
                        };

                        $buildChangedCard = function (
                            string $title,
                            string $date,
                            string $byUser,
                            array  $noteData,
                            string $completePrintUrl
                        ) use ($siteName, $typeName, $record): string {

                            $completeRows = '';
                            foreach ($record->uniformIssuanceRecipient as $recipient) {
                                foreach ($recipient->uniformIssuanceItem as $item) {
                                    $released = (int) $item->released_quantity;
                                    if ($released <= 0) continue;
                                    $label = e("{$item->uniformItem->uniform_item_name} ({$item->uniformItemVariant->uniform_item_size}) — {$recipient->employee_name}");
                                    $completeRows .= "
                                        <tr>
                                            <td style='padding:5px 8px;border:1px solid #d1d5db;font-size:11px;'>{$label}</td>
                                            <td style='padding:5px 8px;border:1px solid #d1d5db;font-size:11px;text-align:center;font-weight:700;'>{$released}</td>
                                        </tr>";
                                }
                            }

                            $changeRows = '';
                            foreach ($noteData as $row) {
                                $employee = e($row['label'] ?? '—');
                                $from     = e($row['_from'] ?? '—');
                                $to       = e($row['_to'] ?? '—');
                                $changeRows .= "
                                    <tr>
                                        <td style='padding:6px 8px;border:1px solid #bfdbfe;font-size:12px;font-weight:600;color:#1e3a5f;'>{$employee}</td>
                                        <td style='padding:6px 8px;border:1px solid #bfdbfe;font-size:12px;'>
                                            <span style='color:#dc2626;'>FROM: {$from}</span><br>
                                            <span style='color:#16a34a;'>TO: &nbsp;&nbsp;{$to}</span>
                                        </td>
                                    </tr>";
                            }

                            return "
                                <div style='border:1px solid #bfdbfe;border-radius:8px;padding:16px;margin-bottom:16px;background:#eff6ff;box-shadow:0 1px 3px rgba(0,0,0,.06);'>
                                    <div style='display:flex;justify-content:space-between;align-items:flex-start;margin-bottom:10px;'>
                                        <div>
                                            <div style='font-size:13px;font-weight:700;color:#1e3a5f;'>{$title}</div>
                                            <div style='font-size:11px;color:#6b7280;margin-top:2px;'>{$siteName} &bull; {$typeName}</div>
                                            <div style='font-size:10px;color:#9ca3af;margin-top:1px;'>{$date} &bull; by {$byUser}</div>
                                        </div>
                                        <span style='background:#2563eb;color:#fff;font-size:10px;font-weight:700;padding:3px 10px;border-radius:999px;white-space:nowrap;'>ITEM CHANGED</span>
                                    </div>

                                    <div style='font-size:10px;font-weight:700;color:#475569;text-transform:uppercase;letter-spacing:.04em;margin-bottom:6px;'>
                                        Complete Receiving Copy (after change)
                                    </div>
                                    <table style='width:100%;border-collapse:collapse;margin-bottom:10px;'>
                                        <thead>
                                            <tr style='background:#1e3a5f;'>
                                                <th style='padding:5px 8px;text-align:left;font-size:10px;color:#fff;border:1px solid #1e3a5f;'>Item / Recipient</th>
                                                <th style='padding:5px 8px;text-align:center;font-size:10px;color:#fff;border:1px solid #1e3a5f;width:60px;'>Qty</th>
                                            </tr>
                                        </thead>
                                        <tbody>{$completeRows}</tbody>
                                    </table>
                                    <div style='margin-bottom:12px;text-align:right;'>
                                        <a href='{$completePrintUrl}' target='_blank' style='font-size:11px;color:#2563eb;text-decoration:underline;'>
                                            Print Complete Copy ↗
                                        </a>
                                    </div>

                                    <div style='border-top:1px dashed #bfdbfe;padding-top:10px;'>
                                        <div style='font-size:10px;font-weight:700;color:#475569;text-transform:uppercase;letter-spacing:.04em;margin-bottom:6px;'>
                                            What Changed
                                        </div>
                                        <table style='width:100%;border-collapse:collapse;'>
                                            <thead>
                                                <tr style='background:#1e40af;'>
                                                    <th style='padding:6px 8px;text-align:left;font-size:11px;color:#fff;border:1px solid #1e40af;width:35%;'>Recipient</th>
                                                    <th style='padding:6px 8px;text-align:left;font-size:11px;color:#fff;border:1px solid #1e40af;'>Change Detail</th>
                                                </tr>
                                            </thead>
                                            <tbody>{$changeRows}</tbody>
                                        </table>
                                        </div>
                                </div>";
                        };

                        $completeCard    = $buildCompleteCard();
                        $individualCards = '';

                        $totalBatches  = $allLogs->whereIn('action', ['issued', 'partial', 'item_released'])->count();
                        $totalChanges  = $allLogs->where('action', 'item_changed')->count();
                        $batchIndex    = $totalBatches;
                        $changeIndex   = $totalChanges;

                        foreach ($allLogs as $log) {
                            $noteData = json_decode($log->note ?? '[]', true);
                            if (!is_array($noteData)) $noteData = [];

                            $date   = \Carbon\Carbon::parse($log->created_at)->timezone('Asia/Manila')->format('M d, Y h:i A');
                            $byUser = $log->user?->name ?? 'System';

                            if ($log->action === 'item_changed') {
                                $title            = "Change #{$changeIndex} — Item Substitution";
                                $completePrintUrl = $printUrl;
                                $individualCards .= $buildChangedCard($title, $date, $byUser, $noteData, $completePrintUrl);
                                $changeIndex--;
                            } elseif ($log->action === 'item_released') {
                                $batchPrintUrl    = route('uniform-issuances.receiving-copy', ['issuance' => $record->id, 'log' => $log->id]);
                                $title            = "Batch #{$batchIndex} — Release (After Change)";
                                $individualCards .= $buildIssuanceCard($title, '#0d9488', 'CHANGE RELEASE', $date, $byUser, $noteData, $batchPrintUrl);
                                $batchIndex--;
                            } else {
                                $completedIssuance = $log->action === 'issued';
                                $badgeColor        = '#d97706';
                                $badgeLabel        = 'RELEASE';
                                $title             = "Batch #{$batchIndex} — Release"
                                                   . ($completedIssuance ? ' (Completed Issuance)' : '');
                                $batchPrintUrl     = route('uniform-issuances.receiving-copy', ['issuance' => $record->id, 'log' => $log->id]);
                                $individualCards  .= $buildIssuanceCard($title, $badgeColor, $badgeLabel, $date, $byUser, $noteData, $batchPrintUrl);
                                $batchIndex--;
                            }
                        }

                        $separatorHtml = $allLogs->count() > 0
                            ? "<div style='border-top:2px dashed #e5e7eb;margin:20px 0 16px;'>
                                   <div style='font-size:11px;font-weight:700;color:#6b7280;text-transform:uppercase;letter-spacing:.06em;margin-top:12px;margin-bottom:4px;'>
                                       Individual Transactions
                                   </div>
                               </div>"
                            : '';

                        return new \Illuminate\Support\HtmlString("
                            <div style='max-height:600px;overflow-y:auto;padding:4px;'>
                                {$completeCard}
                                {$separatorHtml}
                                {$individualCards}
                            </div>
                        ");
                    })
                    ->modalHeading('Receiving Copies')
                    ->modalSubmitAction(false)
                    ->modalCancelActionLabel('Close'),

                // ─── FOR DELIVERY RECEIPT ──────────────────────────────────────────────────
                Action::make('for_delivery_receipt')
                    ->label('For Delivery Receipt')
                    ->color('success')
                    ->icon('heroicon-o-document-check')
                    ->modalWidth('4xl')
                    ->visible(function ($record) {
                        if (!in_array($record->uniform_issuance_status, ['partial', 'issued'])) {
                            return false;
                        }
                
                        $typeName        = strtolower($record->uniformIssuanceType?->uniform_issuance_type_name ?? '');
                        $drEligibleTypes = ['new hire', 'additional', 'annual'];
                
                        $isDrType = false;
                        foreach ($drEligibleTypes as $t) {
                            if (str_contains($typeName, $t)) { $isDrType = true; break; }
                        }
                
                        if (!$isDrType) return false;
                
                        $record->loadMissing('uniformIssuanceRecipient.position');
                
                        return $record->uniformIssuanceRecipient->contains(function ($recipient) {
                            return strtolower($recipient->position?->position_name ?? '') !== 'reliever'
                                && strtolower($recipient->employee_status ?? '') !== 'reliever';
                        });
                    })
                    ->modalSubmitAction(function ($action, $record) {
                        if (\App\Models\ForDeliveryReceipt::where('uniform_issuance_id', $record->id)->exists()) {
                            return false;
                        }
                        return $action;
                    })
                    ->modalCancelActionLabel('Close')
                    ->modalContent(function ($record) {
                        $record->load(
                            'uniformIssuanceRecipient.uniformIssuanceItem.uniformItem',
                            'uniformIssuanceRecipient.uniformIssuanceItem.uniformItemVariant',
                            'uniformIssuanceRecipient.position',
                            'site',
                            'uniformIssuanceType'
                        );
                
                        $existingDR = \App\Models\ForDeliveryReceipt::where('uniform_issuance_id', $record->id)
                            ->latest()
                            ->first();
                
                        $existingHtml = '';
                
                        if ($existingDR) {
                            $statusColor = match ($existingDR->status) {
                                'done'      => '#16a34a',
                                'cancelled' => '#dc2626',
                                default     => '#d97706',
                            };
                            $statusLabel = strtoupper($existingDR->status);
                            $endorseBy   = e($existingDR->endorse_by);
                            $endorseDate = $existingDR->endorse_date
                                ? \Carbon\Carbon::parse($existingDR->endorse_date)->format('M d, Y')
                                : '—';
                            $createdAt = \Carbon\Carbon::parse($existingDR->created_at)->timezone('Asia/Manila')->format('M d, Y h:i A');
                            $remarks   = e($existingDR->remarks ?? '—');
                
                            $itemRows = '';
                            foreach ((array) $existingDR->item_summary as $row) {
                                $itemName = e($row['item_name'] ?? '—');
                                $size     = e($row['size'] ?? '—');
                                $emp      = e($row['employee'] ?? '—');
                                $qty      = (int) ($row['qty'] ?? 0);
                                $itemRows .= "
                                    <tr style='border-bottom:0.5px solid #f1f5f9;'>
                                        <td style='padding:7px 14px;font-size:12px;color:#111827;'>{$emp}</td>
                                        <td style='padding:7px 14px;font-size:12px;color:#374151;'>{$itemName}</td>
                                        <td style='padding:7px 14px;font-size:12px;color:#374151;text-align:center;'>{$size}</td>
                                        <td style='padding:7px 14px;font-size:12px;color:#185FA5;font-weight:600;text-align:center;'>{$qty}</td>
                                    </tr>";
                            }
                
                            $existingHtml = "
                                <div style='border:2px solid #bbf7d0;border-radius:8px;padding:16px;margin-bottom:16px;background:#f0fdf4;box-shadow:0 2px 6px rgba(0,0,0,.06);'>
                                    <div style='display:flex;justify-content:space-between;align-items:flex-start;margin-bottom:12px;'>
                                        <div>
                                            <div style='font-size:14px;font-weight:800;color:#1e3a5f;'>Delivery Receipt #DR{$existingDR->id}</div>
                                            <div style='font-size:11px;color:#6b7280;margin-top:3px;'>Endorsed by: <strong>{$endorseBy}</strong> &bull; {$endorseDate}</div>
                                            <div style='font-size:10px;color:#9ca3af;margin-top:1px;'>Created: {$createdAt}</div>
                                            <div style='font-size:11px;color:#374151;margin-top:4px;'>Remarks: {$remarks}</div>
                                        </div>
                                        <span style='background:{$statusColor};color:#fff;font-size:10px;font-weight:700;padding:3px 10px;border-radius:999px;white-space:nowrap;'>{$statusLabel}</span>
                                    </div>
                                    <table style='width:100%;border-collapse:collapse;'>
                                        <thead>
                                            <tr style='background:#1e3a5f;'>
                                                <th style='padding:6px 14px;text-align:left;font-size:10px;color:#fff;text-transform:uppercase;letter-spacing:.05em;'>Employee</th>
                                                <th style='padding:6px 14px;text-align:left;font-size:10px;color:#fff;text-transform:uppercase;letter-spacing:.05em;'>Item</th>
                                                <th style='padding:6px 14px;text-align:center;font-size:10px;color:#fff;text-transform:uppercase;letter-spacing:.05em;width:70px;'>Size</th>
                                                <th style='padding:6px 14px;text-align:center;font-size:10px;color:#fff;text-transform:uppercase;letter-spacing:.05em;width:60px;'>Qty</th>
                                            </tr>
                                        </thead>
                                        <tbody>{$itemRows}</tbody>
                                    </table>
                                </div>
                                <div style='padding:8px 12px;background:#fef9c3;border:1px solid #fde68a;border-radius:6px;font-size:11px;color:#92400e;text-align:center;'>
                                    A delivery receipt has already been created for this issuance. No further DR can be added.
                                </div>";
                
                            return new \Illuminate\Support\HtmlString("
                                <div style='max-height:600px;overflow-y:auto;padding:2px;'>
                                    {$existingHtml}
                                </div>
                            ");
                        }
                
                        $siteName = e($record->site?->site_name ?? '—');
                        $typeName = e($record->uniformIssuanceType?->uniform_issuance_type_name ?? '—');
                        $status   = strtoupper($record->uniform_issuance_status);
                
                        return new \Illuminate\Support\HtmlString("
                            <div style='padding:8px 12px;background:#fefce8;border:1px solid #fde68a;border-radius:6px;font-size:12px;color:#374151;margin-bottom:10px;'>
                                <strong style='color:#1e3a5f;'>{$siteName}</strong> &bull; {$typeName} &bull;
                                <span style='color:#16a34a;font-weight:700;'>{$status}</span>
                            </div>
                            <div style='font-size:11px;color:#6b7280;'>
                                The form below will pre-fill item summary based on issued quantities.
                                Only non-reliever employees are included.
                            </div>
                        ");
                    })
                    ->form(function ($record) {
                        if (\App\Models\ForDeliveryReceipt::where('uniform_issuance_id', $record->id)->exists()) {
                            return [];
                        }
                
                        $record->load(
                            'uniformIssuanceRecipient.uniformIssuanceItem.uniformItem',
                            'uniformIssuanceRecipient.uniformIssuanceItem.uniformItemVariant',
                            'uniformIssuanceRecipient.position',
                        );
                
                        $grouped = [];
                
                        foreach ($record->uniformIssuanceRecipient as $recipient) {
                            $isReliever = strtolower($recipient->position?->position_name ?? '') === 'reliever'
                                || strtolower($recipient->employee_status ?? '') === 'reliever';
                
                            if ($isReliever) continue;
                
                            $employeeName  = $recipient->employee_name ?? '—';
                            $employeeItems = [];
                
                            foreach ($recipient->uniformIssuanceItem as $item) {
                                $qty = (int) $item->released_quantity;
                                if ($qty <= 0) continue;
                
                                $key = $item->uniform_item_id . '_' . $item->uniform_item_variant_id;
                                if (!isset($employeeItems[$key])) {
                                    $employeeItems[$key] = [
                                        'employee'  => $employeeName,
                                        'item_name' => $item->uniformItem?->uniform_item_name ?? '—',
                                        'size'      => $item->uniformItemVariant?->uniform_item_size ?? '—',
                                        'qty'       => 0,
                                    ];
                                }
                                $employeeItems[$key]['qty'] += $qty;
                            }
                
                            if (!empty($employeeItems)) {
                                $grouped[$employeeName] = array_values($employeeItems);
                            }
                        }
                
                        $itemSummaryFlat = [];
                        foreach ($grouped as $empItems) {
                            foreach ($empItems as $row) {
                                $itemSummaryFlat[] = $row;
                            }
                        }
                
                        $itemSummaryJson = json_encode($itemSummaryFlat, JSON_UNESCAPED_UNICODE);
                        $grandQty        = array_sum(array_column($itemSummaryFlat, 'qty'));
                        $empCount        = count($grouped);
                
                        $fields = [];
                
                        $fields[] = \Filament\Forms\Components\TextInput::make('endorse_by')
                            ->label('Endorsed By')
                            ->default(fn () => \Illuminate\Support\Facades\Auth::user()?->name ?? '')
                            ->required()
                            ->columnSpanFull();
                
                        $fields[] = \Filament\Forms\Components\DatePicker::make('endorse_date')
                            ->label('Endorse Date')
                            ->default(now()->toDateString())
                            ->required();
                
                        $fields[] = \Filament\Forms\Components\TextInput::make('remarks')
                            ->label('Remarks')
                            ->placeholder('Optional notes or instructions');
                
                        $fields[] = \Filament\Forms\Components\Hidden::make('item_summary')
                            ->default($itemSummaryJson);
                
                        $fields[] = \Filament\Forms\Components\Placeholder::make('dr_summary_header')
                            ->label('')
                            ->content(new \Illuminate\Support\HtmlString("
                                <div style='display:flex;align-items:center;justify-content:space-between;
                                    padding:8px 0 6px;border-bottom:0.5px solid #e5e7eb;margin-bottom:4px;'>
                                    <span style='font-size:11px;font-weight:500;color:#6b7280;
                                        text-transform:uppercase;letter-spacing:.06em;'>
                                        {$empCount} employee" . ($empCount !== 1 ? 's' : '') . " &nbsp;&middot;&nbsp; items
                                    </span>
                                    <span style='font-size:15px;font-weight:500;color:#16a34a;'>
                                        Total qty: {$grandQty}
                                    </span>
                                </div>
                            "))
                            ->columnSpanFull();
                
                        $avatarStyles = [
                            ['bg' => '#E6F1FB', 'color' => '#0C447C'],
                            ['bg' => '#E1F5EE', 'color' => '#0F6E56'],
                            ['bg' => '#EEEDFE', 'color' => '#3C3489'],
                            ['bg' => '#FAEEDA', 'color' => '#633806'],
                            ['bg' => '#FAECE7', 'color' => '#712B13'],
                        ];
                
                        $empIndex = 0;
                        foreach ($grouped as $employeeName => $empItems) {
                            $empIndex++;
                            $empQty   = array_sum(array_column($empItems, 'qty'));
                            $words    = explode(' ', trim($employeeName));
                            $initials = strtoupper(
                                (isset($words[0]) ? substr($words[0], 0, 1) : '') .
                                (isset($words[1]) ? substr($words[1], 0, 1) : '')
                            );
                            $av = $avatarStyles[($empIndex - 1) % count($avatarStyles)];
                
                            $tableRows = '';
                            foreach ($empItems as $item) {
                                $tableRows .= "
                                    <tr style='border-bottom:0.5px solid #f1f5f9;'>
                                        <td style='padding:7px 14px;font-size:12px;color:#111827;'>" . e($item['item_name']) . "</td>
                                        <td style='padding:7px 14px;font-size:12px;color:#374151;text-align:center;'>" . e($item['size']) . "</td>
                                        <td style='padding:7px 14px;font-size:12px;color:#185FA5;font-weight:500;text-align:center;'>" . (int) $item['qty'] . "</td>
                                    </tr>";
                            }
                
                            $cardHtml = "
                                <div style='background:#fff;border:0.5px solid #e2e8f0;border-radius:12px;overflow:hidden;margin-bottom:4px;'>
                                    <div style='display:flex;align-items:center;gap:10px;padding:10px 14px;
                                        background:#f8fafc;border-bottom:0.5px solid #e2e8f0;'>
                                        <div style='width:34px;height:34px;border-radius:50%;background:{$av['bg']};
                                            display:flex;align-items:center;justify-content:center;
                                            font-size:12px;font-weight:600;color:{$av['color']};flex-shrink:0;'>
                                            {$initials}
                                        </div>
                                        <div>
                                            <div style='font-size:14px;font-weight:500;color:#111827;'>" . e($employeeName) . "</div>
                                            <div style='font-size:11px;color:#6b7280;margin-top:1px;'>Employee #{$empIndex}</div>
                                        </div>
                                        <span style='margin-left:auto;background:{$av['bg']};color:{$av['color']};
                                            font-size:12px;font-weight:600;padding:3px 12px;border-radius:999px;white-space:nowrap;'>
                                            {$empQty} pc" . ($empQty !== 1 ? 's' : '') . "
                                        </span>
                                    </div>
                                    <div style='overflow-x:auto;'>
                                        <table style='width:100%;border-collapse:collapse;min-width:320px;'>
                                            <thead>
                                                <tr style='background:#f8fafc;border-bottom:0.5px solid #e2e8f0;'>
                                                    <th style='padding:6px 14px;font-size:10px;font-weight:500;color:#6b7280;text-align:left;text-transform:uppercase;letter-spacing:.05em;'>Item</th>
                                                    <th style='padding:6px 14px;font-size:10px;font-weight:500;color:#6b7280;text-align:center;text-transform:uppercase;letter-spacing:.05em;width:70px;'>Size</th>
                                                    <th style='padding:6px 14px;font-size:10px;font-weight:500;color:#6b7280;text-align:center;text-transform:uppercase;letter-spacing:.05em;width:60px;'>Qty</th>
                                                </tr>
                                            </thead>
                                            <tbody>{$tableRows}</tbody>
                                            <tfoot>
                                                <tr style='background:#f0fdf4;border-top:0.5px solid #bbf7d0;'>
                                                    <td colspan='2' style='padding:6px 14px;font-size:11px;font-weight:500;color:#374151;text-align:right;'>Employee total</td>
                                                    <td style='padding:6px 14px;font-size:13px;font-weight:600;color:#16a34a;text-align:center;white-space:nowrap;'>{$empQty}</td>
                                                </tr>
                                            </tfoot>
                                        </table>
                                    </div>
                                </div>";
                
                            $fields[] = \Filament\Forms\Components\Placeholder::make('emp_card_' . $empIndex)
                                ->label('')
                                ->content(new \Illuminate\Support\HtmlString($cardHtml))
                                ->columnSpanFull();
                
                            if ($empIndex < $empCount) {
                                $fields[] = \Filament\Forms\Components\Placeholder::make('emp_divider_' . $empIndex)
                                    ->label('')
                                    ->content(new \Illuminate\Support\HtmlString(
                                        "<div style='border-top:1px dashed #e2e8f0;margin:8px 0 12px;'></div>"
                                    ))
                                    ->columnSpanFull();
                            }
                        }
                
                        return $fields;
                    })
                    ->action(function ($record, array $data, Action $action) {
                        if (\App\Models\ForDeliveryReceipt::where('uniform_issuance_id', $record->id)->exists()) {
                            return;
                        }
                
                        $itemSummary = json_decode($data['item_summary'] ?? '[]', true);
                
                        if (!is_array($itemSummary) || empty($itemSummary)) {
                            \Filament\Notifications\Notification::make()
                                ->title('No Items')
                                ->body('No issued items found to include in the delivery receipt.')
                                ->warning()
                                ->send();
                            $action->halt();
                            return;
                        }
                
                        \App\Models\ForDeliveryReceipt::create([
                            'uniform_issuance_id' => $record->id,
                            'endorse_by'          => $data['endorse_by'],
                            'endorse_date'        => $data['endorse_date'] ?? now()->toDateString(),
                            'item_summary'        => $itemSummary,
                            'status'              => 'pending',
                            'done_date'           => null,
                            'cancel_date'         => null,
                            'remarks'             => $data['remarks'] ?? null,
                        ]);
                
                        \Filament\Notifications\Notification::make()
                            ->title('Delivery Receipt Created')
                            ->body('A new delivery receipt has been queued successfully.')
                            ->success()
                            ->send();
                    }),
                
                
                // ─── BILLING ──────────────────────────────────────────────────────────────
                Action::make('billing')
                    ->label('Billing')
                    ->color('warning')
                    ->icon('heroicon-o-banknotes')
                    ->modalWidth('4xl')
                    ->visible(function ($record) {
                        if (!in_array($record->uniform_issuance_status, ['partial', 'issued'])) {
                            return false;
                        }

                        $typeName      = strtolower($record->uniformIssuanceType?->uniform_issuance_type_name ?? '');
                        $billableTypes = ['new hire', 'additional', 'annual', 'salary deduct'];

                        $isBillableType = false;
                        foreach ($billableTypes as $t) {
                            if (str_contains($typeName, $t)) { $isBillableType = true; break; }
                        }

                        if (!$isBillableType) return false;

                        $isSalaryDeduct = str_contains($typeName, 'salary deduct');

                        // Reliever-only check — applies to all types
                        $record->loadMissing('uniformIssuanceRecipient.position');
                        $recipients = $record->uniformIssuanceRecipient;

                        if ($recipients->isNotEmpty()) {
                            $allRelievers = $recipients->every(function ($recipient) {
                                return strtolower($recipient->position?->position_name ?? '') === 'reliever'
                                    || strtolower($recipient->employee_status ?? '') === 'reliever';
                            });

                            if ($allRelievers) return false;
                        }

                        // Salary deduct: always show (no DR required)
                        if ($isSalaryDeduct) return true;

                        // Client types (new hire / additional / annual): DR must exist first
                        return \App\Models\ForDeliveryReceipt::where('uniform_issuance_id', $record->id)->exists();
                    })
                    ->modalSubmitAction(function ($action, $record) {
                        if (\App\Models\UniformIssuanceBilling::where('uniform_issuance_id', $record->id)->exists()) {
                            return false;
                        }
                        return $action;
                    })
                    ->modalCancelActionLabel('Close')
                    ->modalContent(function ($record) {
                        $record->load(
                            'uniformIssuanceRecipient.uniformIssuanceItem.uniformItem',
                            'uniformIssuanceRecipient.uniformIssuanceItem.uniformItemVariant',
                            'uniformIssuanceRecipient.position',
                            'site',
                            'uniformIssuanceType'
                        );

                        $existingBillings = \App\Models\UniformIssuanceBilling::where('uniform_issuance_id', $record->id)
                            ->latest()
                            ->get();

                        $existingHtml = '';
                        if ($existingBillings->count() > 0) {
                            $rows = '';
                            foreach ($existingBillings as $billing) {
                                $bg          = '#f8fafc';
                                $statusColor = $billing->status === 'billed' ? '#16a34a' : '#d97706';
                                $statusLabel = strtoupper($billing->status);
                                $billedTo    = e($billing->billed_to);
                                $total       = number_format($billing->total_price, 2);
                                $date        = \Carbon\Carbon::parse($billing->created_at)->timezone('Asia/Manila')->format('M d, Y');
                                $typeLabel   = match ($billing->billing_type) {
                                    'client'        => 'CLIENT',
                                    'salary_deduct' => 'SALARY DEDUCT',
                                    default         => 'OTHER',
                                };
                                $typeBgColor = match ($billing->billing_type) {
                                    'client'        => '#1d4ed8',
                                    'salary_deduct' => '#7c3aed',
                                    default         => '#6b7280',
                                };

                                $rows .= "
                                    <tr style='background:{$bg};'>
                                        <td style='padding:6px 10px;border-bottom:1px solid #e5e7eb;font-size:11px;font-weight:700;color:#1e3a5f;'>
                                            {$billedTo}
                                        </td>
                                        <td style='padding:6px 10px;border-bottom:1px solid #e5e7eb;font-size:11px;text-align:center;'>
                                            <span style='background:{$typeBgColor};color:#fff;font-size:9px;font-weight:700;padding:2px 7px;border-radius:999px;'>{$typeLabel}</span>
                                        </td>
                                        <td style='padding:6px 10px;border-bottom:1px solid #e5e7eb;font-size:11px;color:#374151;text-align:right;font-weight:700;'>&#x20B1;{$total}</td>
                                        <td style='padding:6px 10px;border-bottom:1px solid #e5e7eb;font-size:11px;color:#6b7280;text-align:center;'>{$date}</td>
                                        <td style='padding:6px 10px;border-bottom:1px solid #e5e7eb;text-align:center;'>
                                            <span style='background:{$statusColor};color:#fff;font-size:9px;font-weight:700;padding:2px 7px;border-radius:999px;'>{$statusLabel}</span>
                                        </td>
                                    </tr>";
                            }

                            $grandTotal = number_format($existingBillings->sum('total_price'), 2);

                            $lockedNotice = "
                                <div style='padding:8px 12px;background:#fef9c3;border:1px solid #fde68a;border-radius:6px;font-size:11px;color:#92400e;text-align:center;margin-top:12px;'>
                                    A billing has already been created for this issuance. No further billing can be added.
                                </div>";

                            $existingHtml = "
                                <div style='margin-bottom:16px;'>
                                    <div style='font-size:11px;font-weight:700;color:#475569;text-transform:uppercase;letter-spacing:.05em;margin-bottom:8px;'>
                                        Existing Billings ({$existingBillings->count()})
                                    </div>
                                    <table style='width:100%;border-collapse:collapse;border:1px solid #e5e7eb;border-radius:6px;overflow:hidden;'>
                                        <thead>
                                            <tr style='background:#1e3a5f;'>
                                                <th style='padding:5px 10px;text-align:left;font-size:10px;color:#fff;'>Billed To</th>
                                                <th style='padding:5px 10px;text-align:center;font-size:10px;color:#fff;'>Type</th>
                                                <th style='padding:5px 10px;text-align:right;font-size:10px;color:#fff;'>Total</th>
                                                <th style='padding:5px 10px;text-align:center;font-size:10px;color:#fff;'>Date</th>
                                                <th style='padding:5px 10px;text-align:center;font-size:10px;color:#fff;'>Status</th>
                                            </tr>
                                        </thead>
                                        <tbody>{$rows}</tbody>
                                        <tfoot>
                                            <tr style='background:#f0f9ff;border-top:2px solid #93c5fd;'>
                                                <td colspan='2' style='padding:6px 10px;font-size:11px;font-weight:700;color:#374151;text-align:right;'>GRAND TOTAL</td>
                                                <td style='padding:6px 10px;font-size:13px;font-weight:900;color:#1d4ed8;text-align:right;'>&#x20B1;{$grandTotal}</td>
                                                <td colspan='2'></td>
                                            </tr>
                                        </tfoot>
                                    </table>
                                    {$lockedNotice}
                                </div>";

                            return new \Illuminate\Support\HtmlString("
                                <div style='max-height:600px;overflow-y:auto;padding:2px;'>
                                    {$existingHtml}
                                </div>
                            ");
                        }

                        $siteName = e($record->site?->site_name ?? '—');
                        $typeName = e($record->uniformIssuanceType?->uniform_issuance_type_name ?? '—');
                        $status   = strtoupper($record->uniform_issuance_status);

                        return new \Illuminate\Support\HtmlString("
                            <div style='padding:8px 12px;background:#fefce8;border:1px solid #fde68a;border-radius:6px;font-size:12px;color:#374151;margin-bottom:10px;'>
                                <strong style='color:#1e3a5f;'>{$siteName}</strong> &bull; {$typeName} &bull;
                                <span style='color:#1d4ed8;font-weight:700;'>{$status}</span>
                            </div>
                            <div style='font-size:11px;color:#6b7280;'>
                                The form below will pre-fill billing items based on issued quantities and item prices.
                                Total is computed automatically.
                            </div>
                        ");
                    })
                    ->form(function ($record) {
                        // View-only if billing already exists
                        if (\App\Models\UniformIssuanceBilling::where('uniform_issuance_id', $record->id)->exists()) {
                            return [];
                        }

                        $record->load(
                            'uniformIssuanceRecipient.uniformIssuanceItem.uniformItem',
                            'uniformIssuanceRecipient.uniformIssuanceItem.uniformItemVariant',
                            'uniformIssuanceRecipient.position',
                            'site',
                            'uniformIssuanceType'
                        );

                        $typeName        = strtolower($record->uniformIssuanceType?->uniform_issuance_type_name ?? '');
                        $isClientBilling = !str_contains($typeName, 'salary deduct');
                        $isSalaryDeduct  = str_contains($typeName, 'salary deduct');

                        $defaultBilledTo = '';
                        if ($isClientBilling) {
                            $defaultBilledTo = $record->site?->client?->client_name
                                ?? $record->site?->site_name
                                ?? '';
                        }

                        // ── Build per-employee grouped items ──
                        $grouped = [];

                        foreach ($record->uniformIssuanceRecipient as $recipient) {
                            $isReliever = strtolower($recipient->position?->position_name ?? '') === 'reliever'
                                || strtolower($recipient->employee_status ?? '') === 'reliever';

                            if ($isClientBilling && $isReliever) continue;

                            $employeeName  = $recipient->employee_name ?? '—';
                            $employeeItems = [];

                            foreach ($recipient->uniformIssuanceItem as $item) {
                                $qty = (int) $item->released_quantity;
                                if ($qty <= 0) continue;
                                $key = $item->uniform_item_id . '_' . $item->uniform_item_variant_id;
                                if (!isset($employeeItems[$key])) {
                                    $employeeItems[$key] = [
                                        'employee'   => $employeeName,
                                        'item_name'  => $item->uniformItem?->uniform_item_name ?? '—',
                                        'size'       => $item->uniformItemVariant?->uniform_item_size ?? '—',
                                        'quantity'   => 0,
                                        'unit_price' => (float) ($item->uniformItem?->uniform_item_price ?? 0),
                                    ];
                                }
                                $employeeItems[$key]['quantity'] += $qty;
                            }

                            if (!empty($employeeItems)) {
                                $grouped[$employeeName] = array_values($employeeItems);
                            }
                        }

                        // ── Flat list for hidden field ──
                        $billingItemsFlat = [];
                        foreach ($grouped as $empItems) {
                            foreach ($empItems as $row) {
                                $billingItemsFlat[] = $row;
                            }
                        }
                        $billingItemsJson = json_encode($billingItemsFlat, JSON_UNESCAPED_UNICODE);

                        $grandTotal = array_sum(array_map(
                            fn ($i) => (float) ($i['unit_price'] ?? 0) * (int) ($i['quantity'] ?? 0),
                            $billingItemsFlat
                        ));

                        $empCount = count($grouped);
                        $fields   = [];

                        // ── Billed To (client only) ──
                        if ($isClientBilling) {
                            $fields[] = \Filament\Forms\Components\TextInput::make('billed_to')
                                ->label('Billed To (Client)')
                                ->default($defaultBilledTo)
                                ->required()
                                ->columnSpanFull();

                            $fields[] = \Filament\Forms\Components\Placeholder::make('client_info_note')
                                ->label('')
                                ->content(new \Illuminate\Support\HtmlString("
                                    <div style='padding:10px 14px;background:#eff6ff;border:0.5px solid #bfdbfe;
                                        border-radius:8px;font-size:13px;color:#374151;'>
                                        <strong style='color:#1e3a5f;'>Client billing</strong>
                                        — one billing record will be created for the client.
                                        Each employee's items are tracked individually below.
                                    </div>
                                "))
                                ->columnSpanFull();
                        } else {
                            $fields[] = \Filament\Forms\Components\Placeholder::make('salary_deduct_info')
                                ->label('')
                                ->content(new \Illuminate\Support\HtmlString("
                                    <div style='padding:10px 14px;background:#f5f3ff;border:0.5px solid #ddd6fe;
                                        border-radius:8px;font-size:13px;color:#374151;'>
                                        <strong style='color:#4c1d95;'>Salary deduct billing</strong>
                                        — one billing record will be created <strong>per employee</strong>.
                                    </div>
                                "))
                                ->columnSpanFull();
                        }

                        $fields[] = \Filament\Forms\Components\Hidden::make('status')->default('pending');
                        $fields[] = \Filament\Forms\Components\Hidden::make('billing_items')->default($billingItemsJson);

                        // ── DR Number + DR Image: client billing + at least one posted employee ──
                        if ($isClientBilling) {
                            $hasPostedEmployee = $record->uniformIssuanceRecipient->contains(
                                fn ($recipient) => strtolower($recipient->employee_status ?? '') === 'posted'
                            );

                            if ($hasPostedEmployee) {
                                $fields[] = \Filament\Forms\Components\Placeholder::make('dr_section_divider')
                                    ->label('')
                                    ->content(new \Illuminate\Support\HtmlString("
                                        <div style='border-top:1px dashed #bfdbfe;margin:4px 0 2px;'>
                                            <div style='display:flex;align-items:center;gap:6px;margin-top:10px;margin-bottom:2px;'>
                                                <span style='font-size:11px;font-weight:700;color:#1d4ed8;
                                                    text-transform:uppercase;letter-spacing:.05em;'>
                                                    Delivery Receipt
                                                </span>
                                                <span style='font-size:10px;color:#60a5fa;font-style:italic;'>
                                                    (Optional — posted employees detected)
                                                </span>
                                            </div>
                                        </div>
                                    "))
                                    ->columnSpanFull();

                                $fields[] = \Filament\Forms\Components\TextInput::make('dr_number')
                                    ->label('DR Number')
                                    ->placeholder('e.g. DR-2025-001')
                                    ->columnSpanFull();

                                $fields[] = \Filament\Forms\Components\FileUpload::make('signed_dr')
                                    ->label('DR Image / File (Optional)')
                                    ->image()
                                    ->acceptedFileTypes(['image/jpeg', 'image/png', 'image/webp', 'application/pdf'])
                                    ->directory('uniform-issuances/dr')
                                    ->maxSize(5120)
                                    ->columnSpanFull();
                            }
                        }

                        // ── ATD Upload: salary deduct + at least one posted or reliever employee ──
                        if ($isSalaryDeduct) {
                            $hasAtdEmployee = $record->uniformIssuanceRecipient->contains(function ($recipient) {
                                $status = strtolower($recipient->employee_status ?? '');
                                return in_array($status, ['posted', 'reliever']);
                            });

                            if ($hasAtdEmployee) {
                                $fields[] = \Filament\Forms\Components\Placeholder::make('atd_section_divider')
                                    ->label('')
                                    ->content(new \Illuminate\Support\HtmlString("
                                        <div style='border-top:1px dashed #ddd6fe;margin:4px 0 2px;'>
                                            <div style='display:flex;align-items:center;gap:6px;margin-top:10px;margin-bottom:2px;'>
                                                <span style='font-size:11px;font-weight:700;color:#7c3aed;
                                                    text-transform:uppercase;letter-spacing:.05em;'>
                                                    ATD — Authority to Deduct
                                                </span>
                                                <span style='font-size:10px;color:#a78bfa;font-style:italic;'>
                                                    (posted / reliever employees detected)
                                                </span>
                                            </div>
                                        </div>
                                    "))
                                    ->columnSpanFull();

                                $fields[] = \Filament\Forms\Components\FileUpload::make('signed_atd')
                                    ->label('ATD File / Image (Optional)')
                                    ->image()
                                    ->acceptedFileTypes(['image/jpeg', 'image/png', 'image/webp', 'application/pdf'])
                                    ->directory('uniform-issuances/atd')
                                    ->maxSize(5120)
                                    ->columnSpanFull();
                            }
                        }

                        // ── Summary header ──
                        $fields[] = \Filament\Forms\Components\Placeholder::make('billing_summary_header')
                            ->label('')
                            ->content(new \Illuminate\Support\HtmlString("
                                <div style='display:flex;align-items:center;justify-content:space-between;
                                    padding:8px 0 6px;border-bottom:0.5px solid #e5e7eb;margin-bottom:4px;'>
                                    <span style='font-size:11px;font-weight:500;color:#6b7280;
                                        text-transform:uppercase;letter-spacing:.06em;'>
                                        {$empCount} employee" . ($empCount !== 1 ? 's' : '') . "
                                        &nbsp;&middot;&nbsp; items
                                    </span>
                                    <span style='font-size:15px;font-weight:500;color:#185FA5;'>
                                        Grand total: &#x20B1;" . number_format($grandTotal, 2) . "
                                    </span>
                                </div>
                            "))
                            ->columnSpanFull();

                        // ── Per-employee cards ──
                        $avatarStyles = [
                            ['bg' => '#E6F1FB', 'color' => '#0C447C'],
                            ['bg' => '#E1F5EE', 'color' => '#0F6E56'],
                            ['bg' => '#EEEDFE', 'color' => '#3C3489'],
                            ['bg' => '#FAEEDA', 'color' => '#633806'],
                            ['bg' => '#FAECE7', 'color' => '#712B13'],
                        ];

                        $empIndex = 0;
                        foreach ($grouped as $employeeName => $empItems) {
                            $empIndex++;

                            $empTotal = array_sum(array_map(
                                fn ($i) => (float) ($i['unit_price'] ?? 0) * (int) ($i['quantity'] ?? 0),
                                $empItems
                            ));

                            $words    = explode(' ', trim($employeeName));
                            $initials = strtoupper(
                                (isset($words[0]) ? substr($words[0], 0, 1) : '') .
                                (isset($words[1]) ? substr($words[1], 0, 1) : '')
                            );

                            $av = $avatarStyles[($empIndex - 1) % count($avatarStyles)];

                            $tableRows = '';
                            foreach ($empItems as $item) {
                                $sub        = (float) ($item['unit_price'] ?? 0) * (int) ($item['quantity'] ?? 0);
                                $tableRows .= "
                                    <tr style='border-bottom:0.5px solid #f1f5f9;'>
                                        <td style='padding:7px 14px;font-size:12px;color:#111827;'>" . e($item['item_name']) . "</td>
                                        <td style='padding:7px 14px;font-size:12px;color:#374151;text-align:center;'>" . e($item['size']) . "</td>
                                        <td style='padding:7px 14px;font-size:12px;color:#185FA5;font-weight:500;text-align:center;'>" . (int) $item['quantity'] . "</td>
                                        <td style='padding:7px 14px;font-size:12px;color:#374151;text-align:right;'>&#x20B1;" . number_format((float) $item['unit_price'], 2) . "</td>
                                        <td style='padding:7px 14px;font-size:12px;color:#374151;font-weight:500;text-align:right;'>&#x20B1;" . number_format($sub, 2) . "</td>
                                    </tr>";
                            }

                            $cardHtml = "
                                <div style='background:#fff;border:0.5px solid #e2e8f0;border-radius:12px;overflow:hidden;margin-bottom:4px;'>
                                    <div style='display:flex;align-items:center;gap:10px;padding:10px 14px;
                                        background:#f8fafc;border-bottom:0.5px solid #e2e8f0;'>
                                        <div style='width:34px;height:34px;border-radius:50%;background:{$av['bg']};
                                            display:flex;align-items:center;justify-content:center;
                                            font-size:12px;font-weight:600;color:{$av['color']};flex-shrink:0;'>
                                            {$initials}
                                        </div>
                                        <div>
                                            <div style='font-size:14px;font-weight:500;color:#111827;'>" . e($employeeName) . "</div>
                                            <div style='font-size:11px;color:#6b7280;margin-top:1px;'>Employee #{$empIndex}</div>
                                        </div>
                                        <span style='margin-left:auto;background:{$av['bg']};color:{$av['color']};
                                            font-size:12px;font-weight:600;padding:3px 12px;border-radius:999px;white-space:nowrap;'>
                                            &#x20B1;" . number_format($empTotal, 2) . "
                                        </span>
                                    </div>
                                    <div style='overflow-x:auto;'>
                                        <table style='width:100%;border-collapse:collapse;min-width:420px;'>
                                            <thead>
                                                <tr style='background:#f8fafc;border-bottom:0.5px solid #e2e8f0;'>
                                                    <th style='padding:6px 14px;font-size:10px;font-weight:500;color:#6b7280;text-align:left;text-transform:uppercase;letter-spacing:.05em;'>Item</th>
                                                    <th style='padding:6px 14px;font-size:10px;font-weight:500;color:#6b7280;text-align:center;text-transform:uppercase;letter-spacing:.05em;width:55px;'>Size</th>
                                                    <th style='padding:6px 14px;font-size:10px;font-weight:500;color:#6b7280;text-align:center;text-transform:uppercase;letter-spacing:.05em;width:50px;'>Qty</th>
                                                    <th style='padding:6px 14px;font-size:10px;font-weight:500;color:#6b7280;text-align:right;text-transform:uppercase;letter-spacing:.05em;width:100px;'>Unit price</th>
                                                    <th style='padding:6px 14px;font-size:10px;font-weight:500;color:#6b7280;text-align:right;text-transform:uppercase;letter-spacing:.05em;width:90px;'>Subtotal</th>
                                                </tr>
                                            </thead>
                                            <tbody>{$tableRows}</tbody>
                                            <tfoot>
                                                <tr style='background:#f0f9ff;border-top:0.5px solid #bfdbfe;'>
                                                    <td colspan='4' style='padding:6px 14px;font-size:11px;font-weight:500;color:#374151;text-align:right;'>Employee total</td>
                                                    <td style='padding:6px 14px;font-size:13px;font-weight:600;color:#185FA5;text-align:right;white-space:nowrap;'>
                                                        &#x20B1;" . number_format($empTotal, 2) . "
                                                    </td>
                                                </tr>
                                            </tfoot>
                                        </table>
                                    </div>
                                </div>";

                            $fields[] = \Filament\Forms\Components\Placeholder::make('emp_card_' . $empIndex)
                                ->label('')
                                ->content(new \Illuminate\Support\HtmlString($cardHtml))
                                ->columnSpanFull();

                            if ($empIndex < $empCount) {
                                $fields[] = \Filament\Forms\Components\Placeholder::make('emp_divider_' . $empIndex)
                                    ->label('')
                                    ->content(new \Illuminate\Support\HtmlString(
                                        "<div style='border-top:1px dashed #e2e8f0;margin:8px 0 12px;'></div>"
                                    ))
                                    ->columnSpanFull();
                            }
                        }

                        return $fields;
                    })
                    ->action(function ($record, array $data, Action $action) {
                        // Double-guard: silently abort if billing already exists
                        if (\App\Models\UniformIssuanceBilling::where('uniform_issuance_id', $record->id)->exists()) {
                            return;
                        }

                        $record->load(
                            'uniformIssuanceRecipient.uniformIssuanceItem.uniformItem',
                            'uniformIssuanceRecipient.uniformIssuanceItem.uniformItemVariant',
                            'uniformIssuanceType'
                        );

                        $typeName        = strtolower($record->uniformIssuanceType?->uniform_issuance_type_name ?? '');
                        $isClientBilling = !str_contains($typeName, 'salary deduct');
                        $isSalaryDeduct  = str_contains($typeName, 'salary deduct');

                        $billingItems = json_decode($data['billing_items'] ?? '[]', true);

                        if (!is_array($billingItems) || empty($billingItems)) {
                            \Filament\Notifications\Notification::make()
                                ->title('No billing items')
                                ->body('Could not read billing items. Please try again.')
                                ->warning()
                                ->send();
                            $action->halt();
                            return;
                        }

                        $computeTotal = fn (array $items) => array_sum(
                            array_map(fn ($i) => (float) ($i['unit_price'] ?? 0) * (int) ($i['quantity'] ?? 0), $items)
                        );

                        // ── Re-group items by employee ──
                        $grouped = [];
                        foreach ($billingItems as $item) {
                            $emp = $item['employee'] ?? '—';
                            $grouped[$emp][] = $item;
                        }

                        if ($isClientBilling) {
                            $total = $computeTotal($billingItems);

                            \App\Models\UniformIssuanceBilling::create([
                                'uniform_issuance_id'   => $record->id,
                                'billed_to'             => $data['billed_to'] ?? $record->site?->site_name ?? '—',
                                'billing_type'          => 'client',
                                'billing_items'         => $billingItems,
                                'employee_attachments'  => null,
                                'total_price'           => $total,
                                'status'                => 'pending',
                                'billed_at'             => null,
                                'created_by'            => \Illuminate\Support\Facades\Auth::id(),
                                'signed_receiving_copy' => null,
                                'signed_atd'            => null,
                                'signed_dr'             => $data['signed_dr'] ?? null,
                                'dr_number'             => $data['dr_number'] ?? null,
                            ]);

                            \Filament\Notifications\Notification::make()
                                ->title('Billing Created')
                                ->body('Client billing of ₱' . number_format($total, 2) . ' saved successfully.')
                                ->success()
                                ->send();

                        } elseif ($isSalaryDeduct) {
                            $count = 0;

                            foreach ($grouped as $employeeName => $items) {
                                $total = $computeTotal($items);

                                \App\Models\UniformIssuanceBilling::create([
                                    'uniform_issuance_id'   => $record->id,
                                    'billed_to'             => $employeeName,
                                    'billing_type'          => 'salary_deduct',
                                    'billing_items'         => $items,
                                    'employee_attachments'  => null,
                                    'total_price'           => $total,
                                    'status'                => 'pending',
                                    'billed_at'             => null,
                                    'created_by'            => \Illuminate\Support\Facades\Auth::id(),
                                    'signed_receiving_copy' => null,
                                    'signed_atd'            => $data['signed_atd'] ?? null,
                                    'signed_dr'             => null,
                                    'dr_number'             => null,
                                ]);

                                $count++;
                            }

                            \Filament\Notifications\Notification::make()
                                ->title('Billings Created')
                                ->body("{$count} salary deduct billing(s) created successfully.")
                                ->success()
                                ->send();

                        } else {
                            $total = $computeTotal($billingItems);

                            \App\Models\UniformIssuanceBilling::create([
                                'uniform_issuance_id'   => $record->id,
                                'billed_to'             => $data['billed_to'] ?? '—',
                                'billing_type'          => 'other',
                                'billing_items'         => $billingItems,
                                'employee_attachments'  => null,
                                'total_price'           => $total,
                                'status'                => 'pending',
                                'billed_at'             => null,
                                'created_by'            => \Illuminate\Support\Facades\Auth::id(),
                                'signed_receiving_copy' => null,
                                'signed_atd'            => null,
                                'signed_dr'             => null,
                                'dr_number'             => null,
                            ]);

                            \Filament\Notifications\Notification::make()
                                ->title('Billing Saved')
                                ->success()
                                ->send();
                        }
                    }),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),

                    BulkAction::make('bulk_print_receiving_copy')
                        ->label('Print Receiving Copies')
                        ->icon('heroicon-o-printer')
                        ->color('gray')
                        ->deselectRecordsAfterCompletion()
                        ->action(function (Collection $records) {
                            $eligible = $records->filter(
                                fn ($r) => in_array($r->uniform_issuance_status, ['partial', 'issued'])
                            );

                            if ($eligible->isEmpty()) {
                                Notification::make()
                                    ->title('No eligible records')
                                    ->body('Bulk print only works for partial or issued issuances.')
                                    ->warning()
                                    ->send();
                                return;
                            }

                            $ids = $eligible->pluck('id')->implode(',');
                            $url = route('uniform-issuances.bulk.receiving-copy', ['ids' => $ids]);

                            Notification::make()
                                ->title('Print Preview Ready')
                                ->body("Opening {$eligible->count()} receiving cop(ies) in a new tab.")
                                ->success()
                                ->actions([
                                    \Filament\Actions\Action::make('open')
                                        ->label('Open Print Page')
                                        ->url($url)
                                        ->openUrlInNewTab()
                                        ->button(),
                                ])
                                ->persistent()
                                ->send();
                        }),
                    BulkAction::make('bulk_transmit')
                        ->label('Create Transmittal')
                        ->icon('heroicon-o-paper-airplane')
                        ->color('primary')
                        ->deselectRecordsAfterCompletion()
                        ->form([
                            \Filament\Forms\Components\TextInput::make('transmitted_to')
                                ->label('Transmitted To')
                                ->placeholder('e.g. Site Manager / Supervisor name')
                                ->required(),
                            \Filament\Forms\Components\TextInput::make('transmitted_by')
                                ->label('Transmitted By')
                                ->default(fn () => Auth::user()?->name ?? '')
                                ->required(),
                            \Filament\Forms\Components\TextInput::make('purpose')
                                ->label('Purpose')
                                ->placeholder('e.g. New hire uniform issuance'),
                            \Filament\Forms\Components\TextInput::make('instructions')
                                ->label('Instructions')
                                ->placeholder('e.g. Please sign and return copy'),
                        ])
                        ->action(function (Collection $records, array $data) {
                            $eligible = $records->filter(
                                fn ($r) => in_array($r->uniform_issuance_status, ['partial', 'issued'])
                            );

                            if ($eligible->isEmpty()) {
                                Notification::make()
                                    ->title('No eligible records')
                                    ->body('Bulk transmittal only works for partial or issued issuances.')
                                    ->warning()
                                    ->send();
                                return;
                            }

                            // ── Merge ALL items from ALL issuances into one summary ──
                            $summaryMap  = [];
                            $issuanceIds = [];

                            foreach ($eligible as $issuance) {
                                $issuance->loadMissing(
                                    'uniformIssuanceRecipient.uniformIssuanceItem.uniformItem',
                                    'uniformIssuanceRecipient.uniformIssuanceItem.uniformItemVariant'
                                );

                                foreach ($issuance->uniformIssuanceRecipient as $recipient) {
                                    foreach ($recipient->uniformIssuanceItem as $item) {
                                        $qty = (int) ($item->released_quantity ?: $item->quantity);
                                        if ($qty <= 0) continue;

                                        $itemName = $item->uniformItem?->uniform_item_name ?? '—';
                                        $size     = $item->uniformItemVariant?->uniform_item_size ?? '—';
                                        $key      = $itemName . '||' . $size;

                                        if (!isset($summaryMap[$key])) {
                                            $summaryMap[$key] = ['item_name' => $itemName, 'size' => $size, 'qty' => 0];
                                        }
                                        $summaryMap[$key]['qty'] += $qty;
                                    }
                                }

                                $issuanceIds[] = $issuance->id;
                            }

                            if (empty($summaryMap)) {
                                Notification::make()
                                    ->title('Nothing to transmit')
                                    ->body('No items found in the selected issuances.')
                                    ->warning()
                                    ->send();
                                return;
                            }

                            // ── Create ONE transmittal ──
                            $transmittal = \App\Models\Transmittals::create([
                                'uniform_issuance_id' => $eligible->first()->id,
                                'transmittal_number'  => \App\Models\Transmittals::generateNumber(),
                                'transmitted_by'      => $data['transmitted_by'],
                                'transmitted_to'      => $data['transmitted_to'],
                                'purpose'             => $data['purpose'] ?? '',
                                'instructions'        => $data['instructions'] ?? '',
                                'items_summary'       => array_values($summaryMap),
                                'transmitted_at'      => now()->toDateString(),
                                'status'              => 'pending',
                            ]);

                            // ── Attach all issuances to this transmittal via pivot ──
                            $transmittal->issuances()->attach($issuanceIds);

                            // ── Tag all eligible issuances as transmitted ──
                            \App\Models\UniformIssuances::whereIn('id', $issuanceIds)
                                ->update(['is_for_transmit' => true]);

                            $url = route('uniform-issuances.transmittal', [
                                'issuance'    => $eligible->first()->id,
                                'transmittal' => $transmittal->id,
                            ]);

                            Notification::make()
                                ->title('Transmittal Created')
                                ->body("{$transmittal->transmittal_number} — {$eligible->count()} issuance(s) bundled into 1 transmittal.")
                                ->success()
                                ->actions([
                                    \Filament\Actions\Action::make('open')
                                        ->label('Open Transmittal')
                                        ->url($url)
                                        ->openUrlInNewTab()
                                        ->button(),
                                ])
                                ->persistent()
                                ->send();
                        }),
                ]),
            ]);
    }
}