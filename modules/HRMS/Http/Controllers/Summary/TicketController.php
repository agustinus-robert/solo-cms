<?php

namespace Modules\HRMS\Http\Controllers\Summary;

use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;
use Carbon\CarbonPeriod;
use Illuminate\Http\Request;
use Modules\HRMS\Models\Employee;
use Illuminate\Support\Arr;
use Modules\Support\Models\SupportTicket;
use Modules\HRMS\Http\Controllers\Controller;

class TicketController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $start_at = Carbon::parse($request->get('start_at', cmp_cutoff(0)->format('Y-m-d')) . ' 00:00:00');
        $end_at = Carbon::parse($request->get('end_at', cmp_cutoff(1)->format('Y-m-d')) . ' 23:59:59');

        $tickets = SupportTicket::with('meta', 'targetable', 'user')
            ->search($request->get('search'))
            ->whereBetween('created_at', [$start_at, $end_at])
            ->paginate($request->get('limit', 10));

        return view('hrms::summary.tickets.index', compact('start_at', 'end_at', 'tickets'));
    }

    public function show(Request $request){
        $start_at = $request->start_at;
        $end_at = $request->end_at;

        $tickets = SupportTicket::with('meta', 'targetable', 'user')
            ->whereBetween('created_at', [$start_at, $end_at])
            ->get();

        $stats = [
            'total' => $tickets->count(),
            'done' => $tickets->where('job_status', \Modules\Support\Enums\TicketJobEnum::DONE)->count(),
            'on_process' => $tickets->where('job_status', \Modules\Support\Enums\TicketJobEnum::ONPROCESS)->count(),
            'not_done' => $tickets->where('job_status', \Modules\Support\Enums\TicketJobEnum::NOTDONE)->count(),
        ];

        return view('hrms::summary.tickets.show', compact('start_at', 'end_at', 'tickets', 'stats'));
    }
}
