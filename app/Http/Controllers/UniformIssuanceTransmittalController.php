<?php

namespace App\Http\Controllers;

use App\Models\UniformIssuances;
use App\Models\UniformIssuanceLog;
use App\Services\UniformTransmittalService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class UniformIssuanceTransmittalController extends Controller
{
    /**
     * Full transmittal — all released items from DB (current state).
     * Accepts ?transmitted_to=, ?transmitted_by=, ?purpose=, ?instructions= from query string.
     */
    public function issuance(UniformIssuances $issuance, Request $request): \Illuminate\Http\Response
    {
        abort_unless(Auth::check(), 403);

        abort_unless(
            in_array($issuance->uniform_issuance_status, ['partial', 'issued']),
            404, 'Transmittal only available for partial or issued issuances.'
        );

        return response(
            UniformTransmittalService::generateFromIssuance(
                $issuance,
                $request->query('transmitted_to', '—'),
                $request->query('transmitted_by', Auth::user()?->name ?? '—'),
                $request->query('purpose', ''),
                $request->query('instructions', '')
            ),
            200,
            ['Content-Type' => 'text/html; charset=UTF-8']
        );
    }

    /**
     * Batch transmittal — only items released in a specific log entry.
     * Used for individual batch cards in the modal.
     */
    public function fromLog(UniformIssuances $issuance, UniformIssuanceLog $log, Request $request): \Illuminate\Http\Response
    {
        abort_unless(Auth::check(), 403);

        abort_unless(
            in_array($issuance->uniform_issuance_status, ['partial', 'issued']),
            404, 'Transmittal only available for partial or issued issuances.'
        );

        abort_unless(
            $log->uniform_issuance_id === $issuance->id,
            404, 'Log does not belong to this issuance.'
        );

        abort_unless(
            in_array($log->action, ['issued', 'partial', 'item_changed', 'item_released']),
            404, 'Transmittal not available for this log type.'
        );

        return response(
            UniformTransmittalService::generateFromLog(
                $issuance,
                $log,
                $request->query('transmitted_to', '—'),
                $request->query('transmitted_by', Auth::user()?->name ?? '—'),
                $request->query('purpose', ''),
                $request->query('instructions', '')
            ),
            200,
            ['Content-Type' => 'text/html; charset=UTF-8']
        );
    }
}