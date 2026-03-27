@extends('layouts.app')
@section('title', 'Report ' . $report->report_number)

@push('styles')
<style>
  .star-btn { cursor:pointer; font-size:1.75rem; transition:color .1s; }
</style>
@endpush

@section('content')
<div class="max-w-2xl mx-auto px-4 py-10">

  <div class="mb-6 flex items-center justify-between">
    <a href="{{ route('dashboard') }}" class="text-sm text-stone-500 hover:text-stone-700 flex items-center gap-1">
      ← Back to Dashboard
    </a>
    {{-- Delete button --}}
    <form method="POST" action="{{ route('dashboard.report.destroy', $report) }}"
          onsubmit="return confirm('Delete this report permanently?')">
      @csrf
      @method('DELETE')
      <button type="submit" class="text-xs text-red-400 hover:text-red-600 font-medium transition-colors">
        🗑 Delete report
      </button>
    </form>
  </div>

  @php
    $statusClass = match($report->status) {
      'Submitted'   => 'bg-stone-100 text-stone-600',
      'Assigned'    => 'bg-blue-100 text-blue-700',
      'In Progress' => 'bg-amber-100 text-amber-700',
      'Resolved'    => 'bg-green-100 text-green-700',
      default       => 'bg-stone-100 text-stone-600',
    };
    $stages = ['Submitted','Assigned','In Progress','Resolved'];
  @endphp

  @if(session('success'))
  <div class="mb-6 p-4 rounded-xl bg-green-50 border border-green-200 text-sm text-green-700 flex items-center gap-2">
    ✅ {{ session('success') }}
  </div>
  @endif

  @if($report->status === 'Resolved' && !$report->feedback)
  <div class="mb-6 p-5 rounded-2xl" style="background:linear-gradient(135deg,#e8f0e8,#d4e6d3);border:1px solid #b8d4b6">
    <div class="flex items-start gap-3">
      <span class="text-2xl">🎉</span>
      <div>
        <p class="font-semibold text-green-800">Your issue has been resolved!</p>
        <p class="text-sm text-green-700 mt-0.5">Please rate the resolution below to help us improve.</p>
      </div>
    </div>
  </div>
  @endif

  {{-- REPORT HEADER --}}
  <div class="card rounded-2xl p-6 mb-5">
    <div class="flex items-start justify-between gap-4 mb-5">
      <div class="flex items-center gap-3">
        <span class="text-3xl">{{ optional($report->office)->icon ?? '📋' }}</span>
        <div>
          <p class="font-semibold text-lg">{{ $report->office_name }}</p>
          <p class="text-xs text-stone-400 font-mono">{{ $report->report_number }}</p>
        </div>
      </div>
      <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-medium {{ $statusClass }}">
        {{ $report->status }}
      </span>
    </div>

    <div class="grid sm:grid-cols-2 gap-4 text-sm">
      <div><p class="text-xs text-stone-400 mb-1">Location</p><p class="font-medium">{{ $report->location }}</p></div>
      <div>
        <p class="text-xs text-stone-400 mb-1">Priority</p>
        <p class="font-medium {{ match($report->priority){ 'High'=>'text-red-500','Medium'=>'text-amber-500',default=>'text-stone-500' } }}">
          {{ $report->priority }}
        </p>
      </div>
      <div><p class="text-xs text-stone-400 mb-1">Submitted</p><p class="font-medium">{{ $report->created_at->format('M d, Y H:i') }}</p></div>
      <div><p class="text-xs text-stone-400 mb-1">Last Updated</p><p class="font-medium">{{ $report->updated_at->format('M d, Y H:i') }}</p></div>
    </div>

    <div class="mt-4 pt-4 border-t border-stone-100">
      <p class="text-xs text-stone-400 mb-1">Description</p>
      <p class="text-sm text-stone-700 leading-relaxed">{{ $report->description }}</p>
    </div>
  </div>

  {{-- TIMELINE --}}
  <div class="card rounded-2xl p-6 mb-5">
    <h2 class="font-semibold text-base mb-5">Report Timeline</h2>
    <div class="space-y-0">
      @foreach($stages as $i => $stage)
        @php $entry = $report->timelines->firstWhere('status', $stage); @endphp
        <div class="flex gap-4">
          <div class="flex flex-col items-center">
            <div class="w-3 h-3 rounded-full flex-shrink-0 mt-1"
                 style="background:{{ $entry ? 'var(--sage)' : '#d1d5db' }};{{ $entry ? '' : 'opacity:0.3' }}"></div>
            @if($i < count($stages) - 1)
              <div class="w-0.5 flex-1 mt-1" style="background:{{ $entry ? '#8aab87' : '#e5e7eb' }};min-height:32px"></div>
            @endif
          </div>
          <div class="pb-5 {{ $entry ? '' : 'opacity-40' }}">
            <p class="text-sm font-semibold {{ $entry ? '' : 'text-stone-400' }}">{{ $stage }}</p>
            @if($entry)
              <p class="text-xs text-stone-500 mt-0.5">{{ $entry->note }}</p>
              <p class="text-xs text-stone-400 mt-0.5">{{ $entry->created_at->format('M d, Y H:i') }}</p>
            @else
              <p class="text-xs text-stone-400 mt-0.5">Pending</p>
            @endif
          </div>
        </div>
      @endforeach
    </div>
  </div>

  {{-- FEEDBACK --}}
  @if($report->status === 'Resolved')
    @if($report->feedback)
    <div class="card rounded-2xl p-6">
      <h2 class="font-semibold text-base mb-3">Your Feedback</h2>
      <div class="flex items-center gap-1 text-amber-400 text-2xl mb-3">
        @for($i = 1; $i <= 5; $i++)
          <span>{{ $i <= $report->feedback_rating ? '★' : '☆' }}</span>
        @endfor
        <span class="text-sm text-stone-500 ml-2">{{ $report->feedback_rating }}/5</span>
      </div>
      <p class="text-sm text-stone-700 italic">"{{ $report->feedback }}"</p>
    </div>
    @else
    <div class="card rounded-2xl p-6">
      <h2 class="font-semibold text-base mb-1">Rate the Resolution</h2>
      <p class="text-stone-500 text-sm mb-4">How satisfied are you with how your issue was handled?</p>

      @if($errors->any())
      <div class="mb-4 p-3 rounded-lg bg-red-50 text-red-700 border border-red-200 text-sm">
        @foreach($errors->all() as $e)<p>{{ $e }}</p>@endforeach
      </div>
      @endif

      <form method="POST" action="{{ route('dashboard.feedback', $report) }}">
        @csrf
        <div class="mb-4">
          <div class="flex gap-2 text-3xl" id="star-row">
            @for($i = 1; $i <= 5; $i++)
            <span class="star-btn text-stone-300" data-val="{{ $i }}" onclick="setStar({{ $i }})">★</span>
            @endfor
          </div>
          <input type="hidden" name="feedback_rating" id="rating-input"
                 value="{{ old('feedback_rating', 0) }}" required/>
        </div>
        <textarea name="feedback" rows="3" required
          placeholder="Tell us more about your experience..."
          class="input-field w-full px-4 py-3 rounded-xl text-sm resize-none mb-4">{{ old('feedback') }}</textarea>
        <button type="submit" class="btn-primary w-full py-3 rounded-xl text-sm font-semibold">
          Submit Feedback
        </button>
      </form>
    </div>
    @endif
  @endif

</div>

@push('scripts')
<script>
  function setStar(val) {
    document.getElementById('rating-input').value = val;
    document.querySelectorAll('.star-btn').forEach(s => {
      s.style.color = parseInt(s.dataset.val) <= val ? '#f59e0b' : '#d1d5db';
    });
  }
  const old = parseInt(document.getElementById('rating-input')?.value || 0);
  if (old > 0) setStar(old);
</script>
@endpush
@endsection
