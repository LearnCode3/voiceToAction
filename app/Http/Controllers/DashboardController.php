<?php

namespace App\Http\Controllers;

use App\Models\Report;
use App\Models\ReportTimeline;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class DashboardController extends Controller
{
    /**
     * Block office staff and super admin from the citizen dashboard.
     * They should use the office panel instead.
     */
    private function ensureCitizen(): void
    {
        $user = Auth::user();
        if ($user->isOfficeStaff()) {
            redirect()->route('office.panel')->send();
            exit;
        }
        if ($user->isSuperAdmin()) {
            redirect()->route('admin.offices.index')->send();
            exit;
        }
    }

    public function index(Request $request)
    {
        $this->ensureCitizen();

        $user   = Auth::user();
        $filter = $request->get('status', 'all');
        $query  = $user->reports()->with(['timelines', 'office'])->latest();

        if ($filter !== 'all') {
            $query->where('status', $filter);
        }

        $reports       = $query->paginate(10);
        $totalReports  = $user->reports()->count();
        $resolved      = $user->reports()->where('status', 'Resolved')->count();
        $inProgress    = $user->reports()->where('status', 'In Progress')->count();
        $pending       = $user->reports()->whereIn('status', ['Submitted', 'Assigned'])->count();
        $notifications = $user->reports()
            ->where('status', 'Resolved')
            ->whereNull('feedback')
            ->latest()
            ->get();

        return view('dashboard.index', compact(
            'reports', 'filter', 'totalReports', 'resolved', 'inProgress', 'pending', 'notifications'
        ));
    }

    public function show(Report $report)
    {
        $this->ensureCitizen();

        if ($report->user_id !== Auth::id()) {
            abort(403, 'You can only view your own reports.');
        }
        $report->load(['timelines', 'office']);
        return view('dashboard.show', compact('report'));
    }

    public function feedback(Request $request, Report $report)
    {
        $this->ensureCitizen();

        if ($report->user_id !== Auth::id()) {
            abort(403);
        }
        if ($report->status !== 'Resolved' || $report->feedback) {
            return back()->with('error', 'Feedback not available for this report.');
        }

        $data = $request->validate([
            'feedback'        => ['required', 'string', 'max:500'],
            'feedback_rating' => ['required', 'integer', 'min:1', 'max:5'],
        ]);

        $report->update($data);
        return back()->with('success', 'Thank you for your feedback!');
    }

    public function destroy(Report $report)
    {
        $this->ensureCitizen();

        if ($report->user_id !== Auth::id()) {
            abort(403, 'You can only delete your own reports.');
        }

        $number = $report->report_number;
        $report->delete(); // cascades to timelines

        return redirect()->route('dashboard')
            ->with('success', "Report #{$number} has been deleted.");
    }
}
