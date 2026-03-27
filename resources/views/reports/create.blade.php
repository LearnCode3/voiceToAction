@extends('layouts.app')
@section('title', 'Report an Issue')

@push('styles')
<style>
  .category-btn { border:1.5px solid #e2ddd8; transition:all .2s; cursor:pointer; background:white; }
  .category-btn:hover { border-color:var(--sage); }
  .category-btn.selected { border-color:var(--sage); background:#f0f5f0; }
  .category-btn.selected .cat-icon-bg { background:var(--sage) !important; }
  .step-badge { background:#e8f0e8; color:var(--sage); }
</style>
@endpush

@section('content')
<div class="max-w-2xl mx-auto px-4 py-12 fade-in">

  {{-- SUCCESS STATE --}}
  @if(session('success') && session('report_number'))
  <div class="text-center py-8">
    <div class="w-16 h-16 rounded-full flex items-center justify-center mx-auto mb-6 text-3xl" style="background:#e8f0e8">✅</div>
    <h1 class="serif text-3xl mb-3" style="color:var(--charcoal)">Report Submitted!</h1>
    <p class="text-stone-500 mb-6">Your report has been received and routed to the right office.</p>

    <div class="card rounded-2xl p-6 text-left space-y-3 text-sm mb-8">
      <div class="flex justify-between items-center pb-3 border-b border-stone-100">
        <span class="text-stone-500">Report ID</span>
        <span class="font-mono font-semibold text-xs">{{ session('report_number') }}</span>
      </div>
      <div class="flex justify-between items-center py-2">
        <span class="text-stone-500">Assigned to</span>
        <span class="font-medium" style="color:var(--sage)">{{ session('office_icon') }} {{ session('office_name') }}</span>
      </div>
      <div class="flex justify-between items-center py-2">
        <span class="text-stone-500">Status</span>
        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-blue-100 text-blue-700">Assigned</span>
      </div>
      <div class="flex justify-between items-center pt-2 border-t border-stone-100">
        <span class="text-stone-500">Priority</span>
        <span class="font-medium">{{ session('priority') }}</span>
      </div>
    </div>

    <div class="flex flex-col sm:flex-row gap-3 justify-center">
      @auth
        <a href="{{ route('dashboard') }}" class="btn-primary px-6 py-3 rounded-xl text-sm font-semibold inline-block">View Dashboard</a>
      @else
        <a href="{{ route('register') }}" class="btn-primary px-6 py-3 rounded-xl text-sm font-semibold inline-block">Track This Report</a>
      @endauth
      <a href="{{ route('report.create') }}" class="btn-outline px-6 py-3 rounded-xl text-sm font-semibold inline-block">Submit Another</a>
      <a href="{{ route('home') }}" class="text-stone-400 hover:text-stone-600 text-sm px-4 py-3 inline-block">Back to Home</a>
    </div>
  </div>

  @else
  {{-- FORM --}}
  <div class="mb-8">
    <span class="step-badge text-xs font-semibold px-3 py-1 rounded-full uppercase tracking-wide">Submit Report</span>
    <h1 class="serif text-3xl md:text-4xl mt-3 mb-2">Report a community issue</h1>
    <p class="text-stone-500">
      @auth Fill in the form below. Your report is automatically routed to the relevant office.
      @else You're reporting as a guest. <a href="{{ route('register') }}" class="underline" style="color:var(--sage)">Create an account</a> to track your report.
      @endauth
    </p>
  </div>

  @guest
  <div class="mb-6 p-4 rounded-xl bg-amber-50 border border-amber-200 text-sm text-amber-700 flex items-start gap-3">
    <span class="text-lg mt-0.5">👤</span>
    <div>
      <p class="font-medium">Reporting as a guest</p>
      <p class="text-xs mt-0.5">You won't be able to track this report. <a href="{{ route('register') }}" class="underline font-medium">Create an account</a> to monitor your reports.</p>
    </div>
  </div>
  @endguest

  @if($errors->any())
  <div class="mb-4 p-3 rounded-lg bg-red-50 text-red-700 border border-red-200 text-sm">
    <ul class="space-y-1">@foreach($errors->all() as $e)<li>{{ $e }}</li>@endforeach</ul>
  </div>
  @endif

  <form method="POST" action="{{ route('report.store') }}" class="space-y-6">
    @csrf

    {{-- CATEGORY GRID — built from DB offices --}}
    <div>
      <label class="block text-sm font-semibold mb-3">Category <span class="text-red-400">*</span></label>
      <div class="grid grid-cols-2 sm:grid-cols-3 gap-2">
        @foreach($offices as $office)
        <button type="button"
          class="category-btn rounded-xl p-3 flex items-center gap-2 text-left {{ old('category') === $office->slug ? 'selected' : '' }}"
          onclick="selectCategory('{{ $office->slug }}', this)">
          <span class="cat-icon-bg w-8 h-8 rounded-lg flex items-center justify-center text-base transition-colors" style="background:#f0f5f0">{{ $office->icon }}</span>
          <span class="text-xs font-medium text-stone-700">{{ Str::replace(' Office', '', $office->name) }}</span>
        </button>
        @endforeach
      </div>
      <input type="hidden" name="category" id="category-input" value="{{ old('category') }}"/>
      @error('category')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
    </div>

    {{-- LOCATION --}}
    <div>
      <label class="block text-sm font-semibold mb-1.5">Location / Address <span class="text-red-400">*</span></label>
      <input type="text" name="location" value="{{ old('location') }}" required
        placeholder="e.g., Kariakoo Street, Block 5"
        class="input-field w-full px-4 py-3 rounded-xl text-sm"/>
      @error('location')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
    </div>

    {{-- DESCRIPTION --}}
    <div>
      <label class="block text-sm font-semibold mb-1.5">Description <span class="text-red-400">*</span></label>
      <textarea name="description" rows="4" required
        placeholder="Describe the issue in detail (at least 20 characters)..."
        class="input-field w-full px-4 py-3 rounded-xl text-sm resize-none">{{ old('description') }}</textarea>
      <p class="text-xs text-stone-400 mt-1">Be as specific as possible to help the office resolve it faster.</p>
      @error('description')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
    </div>

    {{-- PRIORITY --}}
    <div>
      <label class="block text-sm font-semibold mb-1.5">Priority</label>
      <select name="priority" class="input-field w-full px-4 py-3 rounded-xl text-sm">
        <option value="Low"    {{ old('priority','Medium')==='Low'    ? 'selected' : '' }}>Low — not urgent</option>
        <option value="Medium" {{ old('priority','Medium')==='Medium' ? 'selected' : '' }}>Medium — should be fixed soon</option>
        <option value="High"   {{ old('priority','Medium')==='High'   ? 'selected' : '' }}>High — urgent, affecting many people</option>
      </select>
    </div>

    {{-- GUEST CONTACT --}}
    @guest
    <div class="space-y-4 p-4 bg-stone-50 rounded-xl border border-stone-200">
      <p class="text-xs font-semibold text-stone-600 uppercase tracking-wide">Your Contact Info (Optional)</p>
      <div class="grid sm:grid-cols-2 gap-4">
        <div>
          <label class="block text-sm font-medium mb-1.5">Your name</label>
          <input type="text" name="guest_name" value="{{ old('guest_name') }}" placeholder="Anonymous"
            class="input-field w-full px-4 py-3 rounded-xl text-sm"/>
        </div>
        <div>
          <label class="block text-sm font-medium mb-1.5">Email (optional)</label>
          <input type="email" name="guest_email" value="{{ old('guest_email') }}" placeholder="for reference"
            class="input-field w-full px-4 py-3 rounded-xl text-sm"/>
        </div>
      </div>
    </div>
    @endguest

    <button type="submit" class="btn-primary w-full py-3.5 rounded-xl text-sm font-semibold">
      Submit Report →
    </button>
  </form>
  @endif
</div>

@push('scripts')
<script>
  document.addEventListener('DOMContentLoaded', function () {
    const old = document.getElementById('category-input').value;
    if (old) {
      document.querySelectorAll('.category-btn').forEach(btn => {
        if (btn.getAttribute('onclick').includes("'" + old + "'")) {
          btn.classList.add('selected');
          btn.querySelector('.cat-icon-bg').style.background = 'var(--sage)';
        }
      });
    }
  });

  function selectCategory(slug, btn) {
    document.getElementById('category-input').value = slug;
    document.querySelectorAll('.category-btn').forEach(b => {
      b.classList.remove('selected');
      b.querySelector('.cat-icon-bg').style.background = '#f0f5f0';
    });
    btn.classList.add('selected');
    btn.querySelector('.cat-icon-bg').style.background = 'var(--sage)';
  }
</script>
@endpush
@endsection
