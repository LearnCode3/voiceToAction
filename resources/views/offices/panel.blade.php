@extends('layouts.app')
@section('title', 'Office Panel')

@push('styles')
<style>
  .status-select { border:1px solid #e2ddd8; transition:border-color .2s; }
  .status-select:focus { outline:none; border-color:var(--sage); }
  .office-tab { border-bottom:2px solid transparent; transition:all .2s; }
  .office-tab.active { border-color:var(--sage); color:var(--sage); }
  .priority-High { color:#ef4444; font-weight:600; }
  .priority-Medium { color:#f59e0b; font-weight:600; }
  .priority-Low { color:#6b7280; }
</style>
@endpush

@section('content')
<div class="bg-white border-b border-stone-100 py-8 px-4">
  <div class="max-w-6xl mx-auto flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
    <div>
      <span class="text-xs font-semibold uppercase tracking-wide px-2.5 py-1 rounded-full bg-amber-100 text-amber-700">
        {{ $user->isSuperAdmin() ? 'Super Admin' : 'Office Admin' }}
      </span>
      <h1 class="serif text-3xl mt-2">
        {{ $user->isSuperAdmin() ? 'All Offices Panel' : 'Office Panel' }}
      </h1>
      <p class="text-stone-500 text-sm mt-1">
        @if($user->isSuperAdmin())
          Viewing and managing reports across all departments
        @else
          Managing reports for your assigned office
        @endif
      </p>
    </div>
    @if($totalPending > 0)
    <div class="flex items-center gap-2 px-4 py-2 rounded-xl bg-amber-50 border border-amber-200">
      <span class="text-amber-600 font-bold text-xl">{{ $totalPending }}</span>
      <span class="text-amber-700 text-sm">pending reports</span>
    </div>
    @endif
  </div>
</div>

<div class="max-w-6xl mx-auto px-4 py-8">

  @if(session('success'))
  <div class="mb-5 p-4 rounded-xl bg-green-50 border border-green-200 text-sm text-green-700 fade-in flex items-center gap-2">
    ✅ {{ session('success') }}
  </div>
  @endif

  {{-- FILTERS ROW --}}
  <div class="flex flex-col sm:flex-row gap-4 mb-6">

    {{-- STATUS TABS --}}
    <div class="flex gap-5 border-b border-stone-200 text-sm font-medium text-stone-500 overflow-x-auto flex-1">
      @foreach(['all' => 'All', 'Assigned' => 'Assigned', 'In Progress' => 'In Progress', 'Resolved' => 'Resolved'] as $val => $label)
        <a href="{{ route('office.panel', array_merge(request()->only('office_filter'), ['status' => $val])) }}"
           class="office-tab pb-3 whitespace-nowrap {{ $statusFilter === $val ? 'active' : '' }}">{{ $label }}</a>
      @endforeach
    </div>

    {{-- OFFICE FILTER (super admin only) --}}
    @if($user->isSuperAdmin() && $allOffices->count())
    <form method="GET" action="{{ route('office.panel') }}" class="flex items-center gap-2">
      <input type="hidden" name="status" value="{{ $statusFilter }}"/>
      <select name="office_filter" onchange="this.form.submit()"
        class="status-select bg-white rounded-xl px-3 py-2 text-sm">
        <option value="all" {{ $selectedOffice === 'all' ? 'selected' : '' }}>All Offices</option>
        @foreach($allOffices as $o)
          <option value="{{ $o->slug }}" {{ $selectedOffice === $o->slug ? 'selected' : '' }}>
            {{ $o->icon }} {{ $o->name }}
          </option>
        @endforeach
      </select>
    </form>
    @endif
  </div>

  @if($reports->isEmpty())
  <div class="text-center py-20 text-stone-400">
    <div class="text-5xl mb-3">📭</div>
    <p class="text-sm">No reports match this filter.</p>
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
    @endphp
    <div class="card rounded-2xl p-5">
      <div class="flex flex-col sm:flex-row sm:items-start sm:justify-between gap-3 mb-4">
        <div class="flex-1">
          <div class="flex items-center gap-2 flex-wrap mb-1">
            <span class="text-xs font-mono text-stone-400">{{ $report->report_number }}</span>
            <span class="priority-{{ $report->priority }} text-xs">● {{ $report->priority }}</span>
            {{-- Show office badge for super admin --}}
            @if($user->isSuperAdmin())
              <span class="text-xs px-2 py-0.5 rounded-full bg-stone-100 text-stone-600">
                {{ optional($report->office)->icon ?? '📋' }} {{ $report->office_name }}
              </span>
            @endif
          </div>
          <p class="text-sm font-medium mb-2">{{ Str::limit($report->description, 120) }}</p>
          <div class="flex flex-wrap gap-3 text-xs text-stone-500">
            <span>📍 {{ $report->location }}</span>
            <span>· {{ $report->created_at->format('M d, Y') }}</span>
            <span>· By {{ $report->submitter_name ?? 'Anonymous' }}</span>
          </div>
        </div>
        <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-medium {{ $statusClass }}">
          {{ $report->status }}
        </span>
      </div>

      {{-- STATUS UPDATE --}}
      <div class="flex flex-col sm:flex-row items-start sm:items-center gap-3 pt-4 border-t border-stone-100">
        <form method="POST" action="{{ route('office.updateStatus', $report) }}"
              class="flex items-center gap-3 flex-1">
          @csrf
          @method('PATCH')
          <label class="text-xs text-stone-500 whitespace-nowrap">Update status:</label>
          <select name="status" class="status-select bg-white rounded-lg px-3 py-2 text-xs flex-1 max-w-xs">
            <option value="Assigned"    {{ $report->status === 'Assigned'    ? 'selected' : '' }}>Assigned</option>
            <option value="In Progress" {{ $report->status === 'In Progress' ? 'selected' : '' }}>In Progress</option>
            <option value="Resolved"    {{ $report->status === 'Resolved'    ? 'selected' : '' }}>Resolved</option>
          </select>
          <button type="submit" class="btn-primary px-4 py-2 rounded-lg text-xs font-semibold whitespace-nowrap">Update</button>
        </form>
        <button onclick="toggleTimeline('tl-{{ $report->id }}')"
                class="text-xs text-stone-400 hover:text-stone-700 underline whitespace-nowrap">
          Timeline
        </button>
      </div>

      {{-- TIMELINE (collapsible) --}}
      <div id="tl-{{ $report->id }}" class="hidden mt-4 pt-4 border-t border-stone-100">
        <p class="text-xs font-semibold text-stone-400 uppercase tracking-wide mb-3">Timeline</p>
        @php $stages = ['Submitted','Assigned','In Progress','Resolved']; @endphp
        <div class="space-y-3">
          @foreach($stages as $stage)
          @php $entry = $report->timelines->firstWhere('status', $stage); @endphp
          <div class="flex gap-3 {{ $entry ? '' : 'opacity-40' }}">
            <div class="w-2.5 h-2.5 rounded-full mt-1 flex-shrink-0"
                 style="background:{{ $entry ? 'var(--sage)' : '#d1d5db' }}"></div>
            <div>
              <p class="text-xs font-semibold">{{ $stage }}</p>
              @if($entry)
                <p class="text-xs text-stone-500">{{ $entry->note }} · {{ $entry->created_at->format('M d, Y H:i') }}</p>
              @else
                <p class="text-xs text-stone-400">Pending</p>
              @endif
            </div>
          </div>
          @endforeach
        </div>
        @if($report->feedback)
          <div class="mt-3 p-3 bg-green-50 rounded-xl text-xs text-green-700">
            <span class="font-semibold">Citizen feedback:</span>
            {{ str_repeat('★', $report->feedback_rating) }}{{ str_repeat('☆', 5 - $report->feedback_rating) }}
            — {{ $report->feedback }}
          </div>
        @endif
      </div>
    </div>
    @endforeach
  </div>
  <div class="mt-6">{{ $reports->links() }}</div>
  @endif

</div>

@push('scripts')
<script>
  function toggleTimeline(id) {
    const el = document.getElementById(id);
    if (el) el.classList.toggle('hidden');
  }
</script>
@endpush
@endsection
