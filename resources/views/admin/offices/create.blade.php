@extends('layouts.app')
@section('title', 'Create Office')

@section('content')
<div class="max-w-xl mx-auto px-4 py-12 fade-in">

  <div class="mb-8">
    <a href="{{ route('admin.offices.index') }}" class="text-sm text-stone-500 hover:text-stone-700 mb-4 inline-block">← Back to Offices</a>
    <span class="block text-xs font-semibold uppercase tracking-wide px-2.5 py-1 rounded-full bg-purple-100 text-purple-700 w-fit mb-3">Super Admin</span>
    <h1 class="serif text-3xl" style="color:var(--charcoal)">Create New Office</h1>
    <p class="text-stone-500 text-sm mt-1">New offices are immediately available for report routing.</p>
  </div>

  @if($errors->any())
  <div class="mb-5 p-4 rounded-xl bg-red-50 border border-red-200 text-sm text-red-700">
    <ul class="space-y-1">@foreach($errors->all() as $e)<li>{{ $e }}</li>@endforeach</ul>
  </div>
  @endif

  <form method="POST" action="{{ route('admin.offices.store') }}" class="space-y-5">
    @csrf

    {{-- NAME --}}
    <div>
      <label class="block text-sm font-semibold mb-1.5">Office Name <span class="text-red-400">*</span></label>
      <input type="text" name="name" value="{{ old('name') }}" required
        placeholder="e.g., Electricity Office"
        class="input-field w-full px-4 py-3 rounded-xl text-sm"/>
      <p class="text-xs text-stone-400 mt-1">The slug is generated automatically from the name.</p>
    </div>

    {{-- ICON --}}
    <div>
      <label class="block text-sm font-semibold mb-1.5">Icon (emoji) <span class="text-red-400">*</span></label>
      <input type="text" name="icon" value="{{ old('icon', '🏢') }}" required
        maxlength="10" placeholder="🏢"
        class="input-field w-full px-4 py-3 rounded-xl text-sm"
        id="icon-input" oninput="updatePreview()"/>
      <p class="text-xs text-stone-400 mt-1">Paste or type a single emoji. Preview: <span id="icon-preview" class="text-xl">{{ old('icon', '🏢') }}</span></p>
    </div>

    {{-- DESCRIPTION --}}
    <div>
      <label class="block text-sm font-semibold mb-1.5">Description</label>
      <textarea name="description" rows="3"
        placeholder="Brief description of what this office handles..."
        class="input-field w-full px-4 py-3 rounded-xl text-sm resize-none">{{ old('description') }}</textarea>
    </div>

    {{-- ACTIVE --}}
    <div class="flex items-center gap-3 p-4 bg-stone-50 rounded-xl border border-stone-200">
      <input type="checkbox" name="is_active" id="is_active" value="1"
             class="w-4 h-4 rounded" {{ old('is_active', '1') ? 'checked' : '' }}>
      <div>
        <label for="is_active" class="text-sm font-semibold cursor-pointer">Active</label>
        <p class="text-xs text-stone-400">Only active offices appear in the report form and public offices list.</p>
      </div>
    </div>

    <div class="flex gap-3 pt-2">
      <button type="submit" class="btn-primary flex-1 py-3.5 rounded-xl text-sm font-semibold">
        Create Office
      </button>
      <a href="{{ route('admin.offices.index') }}"
         class="btn-outline px-6 py-3.5 rounded-xl text-sm font-semibold text-center">Cancel</a>
    </div>
  </form>
</div>

@push('scripts')
<script>
  function updatePreview() {
    document.getElementById('icon-preview').textContent =
      document.getElementById('icon-input').value || '🏢';
  }
</script>
@endpush
@endsection
