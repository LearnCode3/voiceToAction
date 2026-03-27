<?php

namespace App\Http\Controllers;

use App\Models\Office;
use App\Models\Report;
use App\Models\ReportTimeline;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class OfficeController extends Controller
{
    // ------------------------------------------------------------------
    // PUBLIC
    // ------------------------------------------------------------------

    public function index()
    {
        $offices = Office::withCount([
            'reports',
            'reports as resolved_count' => fn($q) => $q->where('status', 'Resolved'),
        ])->where('is_active', true)->orderBy('name')->get();

        return view('offices.index', compact('offices'));
    }

    public function show(string $slug)
    {
        $office  = Office::where('slug', $slug)->where('is_active', true)->firstOrFail();
        $reports = Report::where('office_id', $slug)->with('timelines')->latest()->paginate(15);
        return view('offices.show', compact('office', 'reports'));
    }

    // ------------------------------------------------------------------
    // OFFICE STAFF PANEL  (office_staff + super_admin)
    // ------------------------------------------------------------------

    public function panel(Request $request)
    {
        $user         = Auth::user();
        $statusFilter = $request->get('status', 'all');

        if ($user->isSuperAdmin()) {
            $selectedOffice = $request->get('office_filter', 'all');
            $query          = Report::with(['timelines', 'office'])->latest();
            if ($selectedOffice !== 'all') {
                $query->where('office_id', $selectedOffice);
            }
            $allOffices   = Office::where('is_active', true)->orderBy('name')->get();
            $totalPending = Report::whereNotIn('status', ['Resolved'])->count();
        } else {
            // Staff sees ONLY their assigned office
            $selectedOffice = $user->office_id;
            $query          = Report::with(['timelines', 'office'])
                ->where('office_id', $user->office_id)
                ->latest();
            $allOffices   = collect();
            $totalPending = Report::where('office_id', $user->office_id)
                ->whereNotIn('status', ['Resolved'])->count();
        }

        if ($statusFilter !== 'all') {
            $query->where('status', $statusFilter);
        }

        $reports = $query->paginate(20);

        return view('offices.panel', compact(
            'user', 'reports', 'totalPending', 'statusFilter',
            'allOffices', 'selectedOffice'
        ));
    }

    public function updateStatus(Request $request, Report $report)
    {
        $user = Auth::user();

        // Staff can ONLY update their own office's reports
        if (!$user->isSuperAdmin() && $report->office_id !== $user->office_id) {
            abort(403, 'You can only update reports assigned to your office.');
        }

        $data = $request->validate([
            'status' => ['required', 'in:Assigned,In Progress,Resolved'],
        ]);

        $oldStatus = $report->status;
        $report->update(['status' => $data['status']]);

        $noteMap = [
            'In Progress' => 'Office staff has started working on this issue',
            'Resolved'    => 'Issue has been successfully resolved by the office',
        ];

        if (isset($noteMap[$data['status']]) && $oldStatus !== $data['status']) {
            ReportTimeline::create([
                'report_id' => $report->id,
                'status'    => $data['status'],
                'note'      => $noteMap[$data['status']],
            ]);
        }

        return back()->with('success', "Report #{$report->report_number} updated to \"{$data['status']}\".");
    }
}
