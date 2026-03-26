<?php

namespace App\Http\Controllers;

use App\Models\UniformIssuances;
use App\Models\UniformIssuanceRecipients;
use App\Models\UniformIssuanceLog;
use App\Services\UniformIssuanceReceivingCopyService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class UniformIssuanceReceivingCopyController extends Controller
{
    public function issuance(UniformIssuances $issuance, Request $request): \Illuminate\Http\Response
    {
        abort_unless(Auth::check(), 403);

        abort_unless(
            in_array($issuance->uniform_issuance_status, ['partial', 'issued']),
            404, 'Receiving copy only available for partial or issued issuances.'
        );

        $logId = $request->query('log');

        if ($logId) {
            $log = UniformIssuanceLog::where('id', $logId)
                ->where('uniform_issuance_id', $issuance->id)
                ->whereIn('action', ['issued', 'partial', 'item_changed', 'item_released'])
                ->firstOrFail();

            return response(
                UniformIssuanceReceivingCopyService::generateFromLog($issuance, $log),
                200,
                ['Content-Type' => 'text/html; charset=UTF-8']
            );
        }

        return response(
            UniformIssuanceReceivingCopyService::generate($issuance),
            200,
            ['Content-Type' => 'text/html; charset=UTF-8']
        );
    }

    public function recipient(UniformIssuanceRecipients $recipient): \Illuminate\Http\Response
    {
        abort_unless(Auth::check(), 403);

        $issuance = $recipient->uniformIssuance;

        abort_unless(
            in_array($issuance->uniform_issuance_status, ['issued']),
            404, 'Receiving copy only available for issued issuances.'
        );

        return response(
            UniformIssuanceReceivingCopyService::generate($issuance, $recipient),
            200,
            ['Content-Type' => 'text/html; charset=UTF-8']
        );
    }

    public function bulk(Request $request): \Illuminate\Http\Response
    {
        abort_unless(Auth::check(), 403);

        $ids = collect(explode(',', $request->query('ids', '')))
            ->map(fn ($id) => (int) trim($id))
            ->filter()
            ->unique()
            ->values();

        abort_if($ids->isEmpty(), 400, 'No IDs provided.');

        $issuances = UniformIssuances::whereIn('id', $ids)
            ->whereIn('uniform_issuance_status', ['partial', 'issued'])
            ->get();

        abort_if($issuances->isEmpty(), 404, 'No eligible issuances found.');

        // Collect ALL slips across all issuances — only the "complete" snapshot
        // (top card from the modal, i.e. current released quantities)
        $allSlips = [];

        foreach ($issuances as $issuance) {
            $issuance->loadMissing(
                'site',
                'uniformIssuanceType',
                'uniformIssuanceRecipient.position',
                'uniformIssuanceRecipient.uniformIssuanceItem.uniformItem',
                'uniformIssuanceRecipient.uniformIssuanceItem.uniformItemVariant'
            );

            foreach ($issuance->uniformIssuanceRecipient as $rec) {
                $items = [];

                foreach ($rec->uniformIssuanceItem as $item) {
                    $qty = (int) ($item->released_quantity ?: $item->quantity);
                    if ($qty <= 0) continue;

                    $items[] = [
                        'name' => $item->uniformItem?->uniform_item_name ?? "Item #{$item->uniform_item_id}",
                        'size' => $item->uniformItemVariant?->uniform_item_size ?? '—',
                        'qty'  => $qty,
                    ];
                }

                if (empty($items)) continue;

                $allSlips[] = [
                    'txn_id'           => $rec->transaction_id ?? '—',
                    'date'             => $issuance->issued_at
                                            ? \Carbon\Carbon::parse($issuance->issued_at)->format('M d, Y')
                                            : now()->format('M d, Y'),
                    'employee'         => $rec->employee_name ?? 'Unknown Employee',
                    'position'         => $rec->position?->position_name ?? '—',
                    'site'             => $issuance->site?->site_name ?? '—',
                    'issuance_type'    => $issuance->uniformIssuanceType?->uniform_issuance_type_name ?? '—',
                    'is_salary_deduct' => (bool) $issuance->uniformIssuanceType?->is_salary_deduct,
                    'items'            => $items,
                    'change_note'      => null,
                    'is_amendment'     => false,
                ];
            }
        }

        abort_if(empty($allSlips), 404, 'No recipients with issued items found.');

        $title = 'Bulk Receiving Copies (' . $issuances->count() . ' issuance(s))';

        $html = UniformIssuanceReceivingCopyService::wrapDocument(
            $allSlips,
            $title,
            false  // salary-deduct is handled per-slip via is_salary_deduct flag
        );

        return response($html, 200, ['Content-Type' => 'text/html; charset=UTF-8']);
    }
}