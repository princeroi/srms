@php
    $statusMap = [
        'paid'           => ['dot' => '#22c55e', 'bg' => '#dcfce7', 'color' => '#15803d', 'label' => 'Paid'],
        'partially_paid' => ['dot' => '#f59e0b', 'bg' => '#fef9c3', 'color' => '#a16207', 'label' => 'Partially Paid'],
        'overdue'        => ['dot' => '#ef4444', 'bg' => '#fee2e2', 'color' => '#b91c1c', 'label' => 'Overdue'],
        'pending'        => ['dot' => '#94a3b8', 'bg' => '#f1f5f9', 'color' => '#475569', 'label' => 'Pending'],
    ];

    $s        = $statusMap[$record->status] ?? $statusMap['pending'];
    $pct      = $record->total_amount > 0 ? min(100, ($record->total_paid / $record->total_amount) * 100) : 0;
    $barColor = $pct >= 100 ? '#22c55e' : ($pct > 0 ? '#f59e0b' : '#e2e8f0');

    $methodStyles = [
        'cash'          => 'background:#dcfce7;color:#15803d;',
        'gcash'         => 'background:#dbeafe;color:#1d4ed8;',
        'bank_transfer' => 'background:#ede9fe;color:#6d28d9;',
        'check'         => 'background:#fef9c3;color:#a16207;',
    ];
    $methodLabels = [
        'cash'          => 'Cash',
        'gcash'         => 'GCash',
        'bank_transfer' => 'Bank Transfer',
        'check'         => 'Check',
    ];

    // Reusable cell styles
    $KEY   = "padding:10px 16px;font-size:11px;font-weight:600;text-transform:uppercase;letter-spacing:0.08em;color:#64748b;background:#f8fafc;width:34%;border-bottom:1px solid #e2e8f0;border-right:2px solid #cbd5e1;vertical-align:middle;";
    $VAL   = "padding:10px 16px;font-size:13px;font-weight:500;color:#1e293b;background:#ffffff;border-bottom:1px solid #e2e8f0;vertical-align:middle;";
    $VAL_M = "padding:10px 16px;font-size:15px;font-weight:700;letter-spacing:-0.01em;background:#ffffff;border-bottom:1px solid #e2e8f0;vertical-align:middle;";

    $TH      = "padding:10px 14px;text-align:left;font-size:10px;font-weight:700;text-transform:uppercase;letter-spacing:0.09em;color:#64748b;background:#f1f5f9;border-bottom:2px solid #cbd5e1;border-right:1px solid #cbd5e1;white-space:nowrap;";
    $TH_R    = "padding:10px 14px;text-align:right;font-size:10px;font-weight:700;text-transform:uppercase;letter-spacing:0.09em;color:#64748b;background:#f1f5f9;border-bottom:2px solid #cbd5e1;border-right:1px solid #cbd5e1;white-space:nowrap;";
    $TH_LAST = "padding:10px 14px;text-align:left;font-size:10px;font-weight:700;text-transform:uppercase;letter-spacing:0.09em;color:#64748b;background:#f1f5f9;border-bottom:2px solid #cbd5e1;white-space:nowrap;";

    $TD      = "padding:12px 14px;font-size:13px;color:#374151;border-bottom:1px solid #e2e8f0;border-right:1px solid #e2e8f0;vertical-align:middle;";
    $TD_R    = "padding:12px 14px;font-size:13px;color:#374151;border-bottom:1px solid #e2e8f0;border-right:1px solid #e2e8f0;vertical-align:middle;text-align:right;";
    $TD_LAST = "padding:12px 14px;font-size:13px;color:#374151;border-bottom:1px solid #e2e8f0;vertical-align:middle;";

    $TF      = "padding:12px 14px;background:#f8fafc;border-top:2px solid #cbd5e1;border-right:1px solid #e2e8f0;vertical-align:middle;";
    $TF_LAST = "padding:12px 14px;background:#f8fafc;border-top:2px solid #cbd5e1;vertical-align:middle;";
@endphp

<div style="font-family:-apple-system,BlinkMacSystemFont,'Segoe UI',sans-serif;padding:4px 0 8px;color:#374151;">

    {{-- ═══════════════════════════════════════
         HEADER
    ═══════════════════════════════════════ --}}
    <div style="display:flex;justify-content:space-between;align-items:flex-start;margin-bottom:20px;padding-bottom:16px;border-bottom:2px solid #e2e8f0;">
        <div>
            <p style="margin:0 0 4px 0;font-size:11px;font-weight:600;text-transform:uppercase;letter-spacing:0.1em;color:#94a3b8;">
                Invoice — {{ $record->invoice_number }}
            </p>
            <p style="margin:0 0 2px 0;font-size:20px;font-weight:700;color:#0f172a;letter-spacing:-0.02em;">
                {{ $record->client->client_name }}
            </p>
            <p style="margin:0;font-size:12px;color:#94a3b8;">
                Due {{ \Carbon\Carbon::parse($record->due_date)->format('M d, Y') }}
            </p>
        </div>
        <div style="text-align:right;">
            <span style="display:inline-flex;align-items:center;gap:6px;padding:5px 14px;border-radius:999px;font-size:11px;font-weight:700;letter-spacing:0.07em;text-transform:uppercase;background:{{ $s['bg'] }};color:{{ $s['color'] }};">
                <span style="display:inline-block;width:7px;height:7px;border-radius:50%;background:{{ $s['dot'] }};flex-shrink:0;"></span>
                {{ $s['label'] }}
            </span>
        </div>
    </div>

    {{-- ═══════════════════════════════════════
         METRIC CARDS
    ═══════════════════════════════════════ --}}
    <div style="display:grid;grid-template-columns:repeat(3,1fr);border:2px solid #cbd5e1;border-radius:10px;overflow:hidden;margin-bottom:20px;">

        <div style="padding:16px 18px;background:#ffffff;border-right:2px solid #cbd5e1;">
            <p style="margin:0 0 6px 0;font-size:10px;font-weight:700;text-transform:uppercase;letter-spacing:0.12em;color:#94a3b8;">Total Billed</p>
            <p style="margin:0;font-size:22px;font-weight:700;color:#0f172a;letter-spacing:-0.02em;">
                ₱{{ number_format($record->total_amount, 2) }}
            </p>
        </div>

        <div style="padding:16px 18px;background:#ffffff;border-right:2px solid #cbd5e1;">
            <p style="margin:0 0 6px 0;font-size:10px;font-weight:700;text-transform:uppercase;letter-spacing:0.12em;color:#94a3b8;">Total Paid</p>
            <p style="margin:0;font-size:22px;font-weight:700;color:#16a34a;letter-spacing:-0.02em;">
                ₱{{ number_format($record->total_paid, 2) }}
            </p>
        </div>

        <div style="padding:16px 18px;background:#ffffff;">
            <p style="margin:0 0 6px 0;font-size:10px;font-weight:700;text-transform:uppercase;letter-spacing:0.12em;color:#94a3b8;">Remaining</p>
            <p style="margin:0;font-size:22px;font-weight:700;letter-spacing:-0.02em;color:{{ $record->remaining_balance > 0 ? '#dc2626' : '#16a34a' }};">
                ₱{{ number_format($record->remaining_balance, 2) }}
            </p>
        </div>

    </div>

    {{-- ═══════════════════════════════════════
         PROGRESS BAR
    ═══════════════════════════════════════ --}}
    <div style="margin-bottom:20px;padding:14px 16px;border-radius:10px;border:2px solid #cbd5e1;background:#ffffff;">
        <div style="display:flex;justify-content:space-between;align-items:center;margin-bottom:10px;">
            <span style="font-size:11px;font-weight:700;text-transform:uppercase;letter-spacing:0.1em;color:#94a3b8;">
                Collection Progress
            </span>
            <span style="font-size:13px;font-weight:700;color:#475569;">
                {{ number_format($pct, 1) }}% collected
            </span>
        </div>
        <div style="height:8px;border-radius:999px;background:#e2e8f0;overflow:hidden;">
            <div style="height:100%;width:{{ $pct }}%;border-radius:999px;background:{{ $barColor }};"></div>
        </div>
        <div style="display:flex;justify-content:space-between;margin-top:6px;">
            <span style="font-size:10px;color:#cbd5e1;">₱0</span>
            <span style="font-size:10px;color:#cbd5e1;">₱{{ number_format($record->total_amount, 0) }}</span>
        </div>
    </div>

    {{-- ═══════════════════════════════════════
         BILLING DETAILS TABLE
    ═══════════════════════════════════════ --}}
    <p style="margin:0 0 10px 0;font-size:10px;font-weight:700;text-transform:uppercase;letter-spacing:0.14em;color:#64748b;">
        Billing Details
    </p>

    <div style="border:2px solid #cbd5e1;border-radius:10px;overflow:hidden;margin-bottom:20px;">
        <table style="width:100%;border-collapse:separate;border-spacing:0;">
            <tbody>
                <tr>
                    <td style="{{ $KEY }}">Period</td>
                    <td style="{{ $VAL }}">
                        {{ \Carbon\Carbon::parse($record->billing_start_period)->format('M d, Y') }}
                        <span style="color:#cbd5e1;margin:0 8px;">→</span>
                        {{ \Carbon\Carbon::parse($record->billing_end_period)->format('M d, Y') }}
                    </td>
                </tr>
                <tr>
                    <td style="{{ $KEY }}">Billing Date</td>
                    <td style="{{ $VAL }}">{{ \Carbon\Carbon::parse($record->billing_date)->format('M d, Y') }}</td>
                </tr>
                <tr>
                    <td style="{{ $KEY }}">Due Date</td>
                    <td style="{{ $VAL }}">
                        {{ \Carbon\Carbon::parse($record->due_date)->format('M d, Y') }}
                        @if(\Carbon\Carbon::parse($record->due_date)->isPast() && $record->status !== 'paid')
                            <span style="display:inline-block;margin-left:8px;padding:2px 8px;border-radius:4px;font-size:9px;font-weight:700;text-transform:uppercase;letter-spacing:0.08em;background:#fee2e2;color:#b91c1c;vertical-align:middle;">
                                Overdue
                            </span>
                        @endif
                    </td>
                </tr>
                <tr>
                    <td style="{{ $KEY }}">Total Amount</td>
                    <td style="{{ $VAL_M }}color:#0f172a;">₱{{ number_format($record->total_amount, 2) }}</td>
                </tr>
                <tr>
                    <td style="{{ $KEY }}">Total Paid</td>
                    <td style="{{ $VAL_M }}color:#16a34a;">₱{{ number_format($record->total_paid, 2) }}</td>
                </tr>
                <tr>
                    <td style="padding:10px 16px;font-size:11px;font-weight:600;text-transform:uppercase;letter-spacing:0.08em;color:#64748b;background:#f8fafc;width:34%;border-right:2px solid #cbd5e1;vertical-align:middle;">
                        Remaining
                    </td>
                    <td style="padding:10px 16px;font-size:15px;font-weight:700;letter-spacing:-0.01em;background:#ffffff;vertical-align:middle;color:{{ $record->remaining_balance > 0 ? '#dc2626' : '#16a34a' }};">
                        ₱{{ number_format($record->remaining_balance, 2) }}
                        @if($record->remaining_balance == 0)
                            <span style="display:inline-block;margin-left:8px;padding:2px 8px;border-radius:4px;font-size:9px;font-weight:700;text-transform:uppercase;letter-spacing:0.08em;background:#dcfce7;color:#15803d;vertical-align:middle;">
                                Settled
                            </span>
                        @endif
                    </td>
                </tr>
            </tbody>
        </table>
    </div>

    {{-- ═══════════════════════════════════════
         PAYMENT ENTRIES TABLE
    ═══════════════════════════════════════ --}}
    <p style="margin:0 0 10px 0;font-size:10px;font-weight:700;text-transform:uppercase;letter-spacing:0.14em;color:#64748b;">
        Payment Entries
        <span style="font-weight:400;text-transform:none;letter-spacing:0;color:#94a3b8;font-size:12px;margin-left:6px;">
            — {{ $record->collection->count() }} {{ Str::plural('record', $record->collection->count()) }}
        </span>
    </p>

    @if($record->collection->isEmpty())

        <div style="padding:48px 24px;text-align:center;border:2px dashed #e2e8f0;border-radius:10px;">
            <p style="margin:0 0 6px 0;font-size:15px;font-weight:600;color:#94a3b8;">No payments recorded</p>
            <p style="margin:0;font-size:12px;color:#cbd5e1;">No collection entries found for this invoice.</p>
        </div>

    @else

        <div style="border:2px solid #cbd5e1;border-radius:10px;overflow:hidden;">
            <table style="width:100%;border-collapse:separate;border-spacing:0;">

                {{-- Head --}}
                <thead>
                    <tr>
                        <th style="{{ $TH }}width:44px;">#</th>
                        <th style="{{ $TH }}">Collected By</th>
                        <th style="{{ $TH }}">Payment Date</th>
                        <th style="{{ $TH_R }}">Amount Paid</th>
                        <th style="{{ $TH }}">Method</th>
                        <th style="{{ $TH }}">Reference</th>
                        <th style="{{ $TH_LAST }}">Logged At</th>
                    </tr>
                </thead>

                {{-- Body --}}
                <tbody>
                    @foreach($record->collection->sortByDesc('created_at') as $i => $log)
                        <tr>

                            {{-- # --}}
                            <td style="{{ $TD }}font-size:11px;font-weight:600;color:#94a3b8;">
                                {{ str_pad($record->collection->count() - $i, 2, '0', STR_PAD_LEFT) }}
                            </td>

                            {{-- Collected By --}}
                            <td style="{{ $TD }}font-weight:600;color:#0f172a;">
                                {{ $log->collect_by }}
                            </td>

                            {{-- Payment Date --}}
                            <td style="{{ $TD }}">
                                <p style="margin:0;font-weight:500;color:#1e293b;">
                                    {{ \Carbon\Carbon::parse($log->payment_date)->format('M d, Y') }}
                                </p>
                                <p style="margin:2px 0 0;font-size:11px;color:#94a3b8;">
                                    {{ \Carbon\Carbon::parse($log->payment_date)->format('l') }}
                                </p>
                            </td>

                            {{-- Amount --}}
                            <td style="{{ $TD_R }}font-size:15px;font-weight:700;color:#16a34a;letter-spacing:-0.01em;">
                                ₱{{ number_format($log->amount_paid, 2) }}
                            </td>

                            {{-- Method --}}
                            <td style="{{ $TD }}">
                                <span style="display:inline-block;padding:3px 10px;border-radius:4px;font-size:10px;font-weight:700;text-transform:uppercase;letter-spacing:0.07em;{{ $methodStyles[$log->payment_method] ?? 'background:#f1f5f9;color:#475569;' }}">
                                    {{ $methodLabels[$log->payment_method] ?? ucfirst($log->payment_method) }}
                                </span>
                            </td>

                            {{-- Reference --}}
                            <td style="{{ $TD }}font-size:12px;color:{{ $log->reference_number ? '#475569' : '#cbd5e1' }};">
                                {{ $log->reference_number ?? '—' }}
                            </td>

                            {{-- Logged At --}}
                            <td style="{{ $TD_LAST }}">
                                <p style="margin:0;font-size:12px;font-weight:500;color:#475569;">
                                    {{ $log->created_at->format('M d, Y') }}
                                </p>
                                <p style="margin:2px 0 0;font-size:11px;color:#94a3b8;">
                                    {{ $log->created_at->format('h:i A') }}
                                </p>
                            </td>

                        </tr>
                    @endforeach
                </tbody>

                {{-- Footer --}}
                <tfoot>
                    <tr>
                        <td colspan="3" style="{{ $TF }}text-align:right;">
                            <span style="font-size:11px;font-weight:700;text-transform:uppercase;letter-spacing:0.09em;color:#94a3b8;">
                                {{ $record->collection->count() }} {{ Str::plural('Payment', $record->collection->count()) }} Total
                            </span>
                        </td>
                        <td style="{{ $TF }}text-align:right;font-size:18px;font-weight:700;color:#16a34a;letter-spacing:-0.02em;">
                            ₱{{ number_format($record->collection->sum('amount_paid'), 2) }}
                        </td>
                        <td style="{{ $TF }}"></td>
                        <td style="{{ $TF }}"></td>
                        <td style="{{ $TF_LAST }}"></td>
                    </tr>
                </tfoot>

            </table>
        </div>

    @endif

</div>