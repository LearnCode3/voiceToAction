@extends('layouts.app')
@section('title', $office->name)

@section('content')
<div class="bg-white border-b border-stone-100 py-10 px-4">
  <div class="max-w-6xl mx-auto">
    <a href="{{ route('offices.index') }}" class="text-sm text-stone-500 hover:text-stone-700 mb-3 inline-block">← All Offices</a>
    <div class="flex items-center gap-3">
      <span class="text-4xl">{{ $office->icon }}</span>
      <div>
        <h1 class="serif text-3xl md:text-4xl">{{ $office->name }}</h1>
        <p class="text-stone-500 text-sm mt-1">{{ $reports->total() }} reports assigned · Public view</p>
      </div>
    </div>
    @if($office->description)
      <p class="text-stone-400 text-sm mt-3 max-w-xl">{{ $office->description }}</p>
    @endif
  </div>
</div>

<div class="max-w-6xl mx-auto px-4 py-8">
  @if($reports->isEmpty())
  <div class="text-center py-20 text-stone-400">
    <div class="text-5xl mb-3">📭</div>
    <p>No reports assigned to this office yet.</p>
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
      <div class="flex flex-col sm:flex-row sm:items-start sm:justify-between gap-3">
        <div class="flex-1">
          <div class="flex items-center gap-2 mb-1">
            <span class="text-xs font-mono text-stone-400">{{ $report->report_number }}</span>
            @if($report->priority === 'High')
              <span class="text-xs text-red-500 font-semibold">● High</span>
            @elseif($report->priority === 'Medium')
              <span class="text-xs text-amber-500 font-semibold">● Medium</span>
            @endif
          </div>
          <p class="text-sm font-medium mb-1">{{ Str::limit($report->description, 120) }}</p>
          <div class="flex flex-wrap gap-3 text-xs text-stone-500">
            <span>📍 {{ $report->location }}</span>
            <span>· {{ $report->created_at->format('M d, Y') }}</span>
            <span>· By {{ $report->submitter_name ?? 'Anonymous' }}</span>
          </div>
          @if($report->feedback)
            <div class="mt-2 text-xs text-stone-500">
              ⭐ {{ $report->feedback_rating }}/5 citizen satisfaction
            </div>
          @endif
        </div>
        <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-medium {{ $statusClass }}">{{ $report->status }}</span>
      </div>
    </div>
    @endforeach
  </div>
  <div class="mt-6">{{ $reports->links() }}</div>
  @endif
</div>
@endsection
