<?php

namespace App\Http\Controllers;

use App\Models\Office;
use App\Models\Report;
use App\Models\ReportTimeline;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ReportController extends Controller
{
    public function create()
    {
        $offices = Office::where('is_active', true)->orderBy('name')->get();
        return view('reports.create', compact('offices'));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'category'    => ['required', 'string', 'exists:offices,slug'],
            'location'    => ['required', 'string', 'max:255'],
            'description' => ['required', 'string', 'min:20'],
            'priority'    => ['required', 'in:Low,Medium,High'],
            'guest_name'  => ['nullable', 'string', 'max:255'],
            'guest_email' => ['nullable', 'email', 'max:255'],
        ]);

        $office = Office::where('slug', $data['category'])->firstOrFail();
        $user   = Auth::user();

        $report = Report::create([
            'report_number'   => Report::generateNumber(),
            'user_id'         => $user?->id,
            'submitter_name'  => $user?->name ?? ($data['guest_name'] ?? 'Guest'),
            'submitter_email' => $user?->email ?? ($data['guest_email'] ?? null),
            'category'        => $data['category'],
            'office_id'       => $office->slug,
            'office_name'     => $office->name,
            'location'        => $data['location'],
            'description'     => $data['description'],
            'priority'        => $data['priority'],
            'status'          => 'Assigned',
        ]);

        ReportTimeline::create([
            'report_id' => $report->id,
            'status'    => 'Submitted',
            'note'      => 'Report received by VoiceToAction',
        ]);
        ReportTimeline::create([
            'report_id' => $report->id,
            'status'    => 'Assigned',
            'note'      => "Automatically routed to {$office->name}",
        ]);

        return redirect()->route('report.create')->with([
            'success'       => true,
            'report_number' => $report->report_number,
            'office_name'   => $office->name,
            'office_icon'   => $office->icon,
            'priority'      => $report->priority,
        ]);
    }
}
