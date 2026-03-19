<?php

namespace App\Http\Controllers;

use App\Models\UniformIssuances;
use App\Models\UniformIssuanceLog;
use App\Models\Transmittals;
use App\Services\UniformTransmittalService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class UniformIssuanceTransmittalController extends Controller
{
    /**
     * Print transmittal by transmittal ID (saved record).
     * Called after saving from the modal action.
     */
    public function issuance(UniformIssuances $issuance, Request $request): \Illuminate\Http\Response
    {
        abort_unless(Auth::check(), 403);

        abort_unless(
            in_array($issuance->uniform_issuance_status, ['partial', 'issued']),
            404, 'Transmittal only available for partial or issued issuances.'
        );

        // If a saved transmittal ID is passed, load from DB
        $transmittalId = $request->query('transmittal');

        if ($transmittalId) {
            $transmittal = Transmittals::where('id', $transmittalId)
                ->where('uniform_issuance_id', $issuance->id)
                ->firstOrFail();

            return response(
                UniformTransmittalService::generateFromSaved($issuance, $transmittal),
                200,
                ['Content-Type' => 'text/html; charset=UTF-8']
            );
        }

        // Fallback: generate from query params (preview mode)
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
     * Print transmittal for a specific batch log entry.
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