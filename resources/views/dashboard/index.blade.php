@extends('layouts.app')
@section('title', 'Dashboard')

@push('styles')
<style>
  .tab-btn { border-bottom:2px solid transparent; transition:all .2s; }
  .tab-btn.active { border-color:var(--sage); color:var(--sage); }
  .notification-banner { background:linear-gradient(135deg,#e8f0e8,#d4e6d3); border:1px solid #b8d4b6; }
  .priority-High { color:#ef4444; font-weight:600; }
  .priority-Medium { color:#f59e0b; font-weight:600; }
  .priority-Low { color:#6b7280; }
</style>
@endpush

@section('content')
<div class="max-w-6xl mx-auto px-4 py-10">

  {{-- RESOLVED NOTIFICATIONS --}}
  @if($notifications->count())
  <div class="mb-6 space-y-3">
    @foreach($notifications as $n)
    <div class="notification-banner rounded-2xl p-4 flex items-center justify-between fade-in">
      <div class="flex items-center gap-3">
        <span class="text-xl">🔔</span>
        <div>
          <p class="text-sm font-semibold text-green-800">Issue resolved by {{ $n->office_name }}</p>
          <p class="text-xs text-green-600">{{ Str::limit($n->description, 70) }}</p>
        </div>
      </div>
      <a href="{{ route('dashboard.report', $n) }}"
         class="text-xs font-semibold px-3 py-1.5 rounded-lg whitespace-nowrap text-white"
         style="background:var(--sage)">Rate it</a>
    </div>
    @endforeach
  </div>
  @endif

  {{-- HEADER --}}
  <div class="flex flex-col sm:flex-row sm:items-end sm:justify-between mb-8">
    <div>
      <h1 class="serif text-3xl md:text-4xl">My Dashboard</h1>
      <p class="text-stone-500 text-sm mt-1">Welcome back, {{ Auth::user()->name }}</p>
    </div>
    <a href="{{ route('report.create') }}"
       class="btn-primary mt-4 sm:mt-0 px-6 py-2.5 rounded-xl text-sm font-semibold inline-block">
      + Report New Issue
    </a>
  </div>

  {{-- STATS --}}
  <div class="grid grid-cols-2 md:grid-cols-4 gap-4 mb-8">
    <div class="card rounded-2xl p-5">
      <div class="text-2xl font-bold serif mb-1">{{ $totalReports }}</div>
      <div class="text-xs text-stone-500">Total Reports</div>
    </div>
    <div class="card rounded-2xl p-5">
      <div class="text-2xl font-bold serif mb-1 text-blue-600">{{ $pending }}</div>
      <div class="text-xs text-stone-500">Pending</div>
    </div>
    <div class="card rounded-2xl p-5">
      <div class="text-2xl font-bold serif mb-1 text-amber-600">{{ $inProgress }}</div>
      <div class="text-xs text-stone-500">In Progress</div>
    </div>
    <div class="card rounded-2xl p-5">
      <div class="text-2xl font-bold serif mb-1 text-green-600">{{ $resolved }}</div>
      <div class="text-xs text-stone-500">Resolved</div>
    </div>
  </div>

  {{-- STATUS TABS --}}
  <div class="flex gap-6 border-b border-stone-200 mb-6 text-sm font-medium text-stone-500">
    @foreach(['all' => 'All Reports', 'Assigned' => 'Assigned', 'In Progress' => 'In Progress', 'Resolved' => 'Resolved'] as $val => $label)
      <a href="{{ route('dashboard', ['status' => $val]) }}"
         class="tab-btn pb-3 {{ $filter === $val ? 'active' : '' }}">{{ $label }}</a>
    @endforeach
  </div>

  {{-- REPORTS LIST --}}
  @if($reports->isEmpty())
  <div class="py-20 text-center">
    <div class="text-5xl mb-4">📋</div>
    <h3 class="font-semibold text-lg mb-2">No reports yet</h3>
    <p class="text-stone-500 text-sm mb-6">Submit your first community issue report</p>
    <a href="{{ route('report.create') }}" class="btn-primary px-6 py-3 rounded-xl text-sm font-semibold inline-block">Report an Issue</a>
  </div>
  @else
  <div class="space-y-4">
    @foreach($reports as $report)
    @php
      $statusClass = match($report->status) {
        'Submitted'   => 'bg-stone-100 text-stone-600',
        'Assigned'    => 'bg-blue-100 text-blue-700',
        'In Progress' => 'bg-amber-100 text-amber-700',
        'Resolved'    => 'bg-green-100 text-green-700',
        default       => 'bg-stone-100 text-stone-600',
      };
      $stages     = ['Submitted','Assigned','In Progress','Resolved'];
      $currentIdx = array_search($report->status, $stages);
    @endphp
    <div class="card rounded-2xl p-5 hover:shadow-sm transition-shadow">

      {{-- Resolved but no feedback banner --}}
      @if($report->status === 'Resolved' && !$report->feedback)
      <div class="notification-banner rounded-xl p-3 mb-4 flex items-center justify-between">
        <div class="flex items-center gap-2 text-sm">
          <span>🎉</span>
          <span class="font-medium text-green-800">Your issue has been resolved!</span>
        </div>
        <a href="{{ route('dashboard.report', $report) }}"
           class="text-xs font-semibold px-3 py-1.5 rounded-lg text-white whitespace-nowrap"
           style="background:var(--sage)">Leave Feedback</a>
      </div>
      @endif

      <div class="flex flex-col sm:flex-row sm:items-start sm:justify-between gap-3">
        <div class="flex-1">
          <div class="flex items-center gap-2 flex-wrap mb-1">
            <span class="text-base">{{ optional($report->office)->icon ?? '📋' }}</span>
            <span class="font-semibold text-sm">{{ Str::limit($report->description, 70) }}</span>
          </div>
          <div class="flex flex-wrap gap-2 text-xs text-stone-500 mb-2">
            <span>📍 {{ $report->location }}</span>
            <span>·</span>
            <span>{{ $report->created_at->format('M d, Y') }}</span>
            <span>·</span>
            <span class="priority-{{ $report->priority }}">{{ $report->priority }} priority</span>
          </div>
          <div class="flex items-center gap-2 text-xs">
            <span class="text-stone-400">Office:</span>
            <span class="font-medium" style="color:var(--sage)">{{ $report->office_name }}</span>
          </div>
          @if($report->feedback)
            <div class="mt-1 text-xs text-stone-500">
              ⭐ {{ $report->feedback_rating }}/5 — "{{ Str::limit($report->feedback, 60) }}"
            </div>
          @endif
        </div>
        <div class="flex items-center gap-3">
          <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-medium {{ $statusClass }}">
            {{ $report->status }}
          </span>
          <a href="{{ route('dashboard.report', $report) }}"
             class="text-xs text-stone-400 hover:text-stone-700 underline">Details</a>
        </div>
      </div>

      {{-- Progress bar --}}
      <div class="mt-4">
        <div class="flex gap-1">
          @foreach($stages as $i => $stage)
          <div class="flex-1 h-1 rounded-full {{ $i <= $currentIdx ? '' : 'bg-stone-100' }}"
               style="{{ $i <= $currentIdx ? 'background:var(--sage)' : '' }}"></div>
          @endforeach
        </div>
        <div class="flex justify-between mt-1 text-xs text-stone-400">
          @foreach($stages as $stage)<span>{{ $stage }}</span>@endforeach
        </div>
      </div>

      {{-- Delete button (only if not in progress or resolved) --}}
      <div class="mt-3 pt-3 border-t border-stone-100 flex justify-end">
        <form method="POST" action="{{ route('dashboard.report.destroy', $report) }}"
              onsubmit="return confirm('Are you sure you want to delete this report? This cannot be undone.')">
          @csrf
          @method('DELETE')
          <button type="submit"
                  class="text-xs text-red-400 hover:text-red-600 transition-colors font-medium">
            🗑 Delete report
          </button>
        </form>
      </div>
    </div>
    @endforeach
  </div>
  <div class="mt-6">{{ $reports->links() }}</div>
  @endif

</div>
@endsection
