@extends('layouts.app')
@section('title', 'Edit ' . $office->name)

@section('content')
<div class="max-w-2xl mx-auto px-4 py-12 fade-in">

  <div class="mb-8">
    <a href="{{ route('admin.offices.index') }}"
       class="text-sm text-stone-500 hover:text-stone-700 mb-4 inline-block">← Back to Offices</a>
    <span class="block text-xs font-semibold uppercase tracking-wide px-2.5 py-1 rounded-full bg-purple-100 text-purple-700 w-fit mb-3">Super Admin</span>
    <div class="flex items-center gap-3">
      <span class="text-4xl">{{ $office->icon }}</span>
      <div>
        <h1 class="serif text-3xl" style="color:var(--charcoal)">Edit Office</h1>
        <p class="text-stone-500 text-sm">Slug: <code class="bg-stone-100 px-1.5 py-0.5 rounded text-xs font-mono">{{ $office->slug }}</code></p>
      </div>
    </div>
  </div>

  @if($errors->any())
  <div class="mb-5 p-4 rounded-xl bg-red-50 border border-red-200 text-sm text-red-700">
    <ul class="space-y-1">@foreach($errors->all() as $e)<li>{{ $e }}</li>@endforeach</ul>
  </div>
  @endif

  @php $reportCount = $office->reports()->count(); @endphp
  @if($reportCount > 0)
  <div class="mb-5 p-4 rounded-xl bg-amber-50 border border-amber-200 text-sm text-amber-700 flex items-start gap-2">
    <span class="mt-0.5">⚠️</span>
    <div>
      <p class="font-medium">This office has {{ $reportCount }} report(s)</p>
      <p class="text-xs mt-0.5">Renaming will update it across all existing reports. The slug cannot be changed.</p>
    </div>
  </div>
  @endif

  <div class="grid md:grid-cols-5 gap-6">
    {{-- EDIT FORM --}}
    <div class="md:col-span-3">
      <div class="card rounded-2xl p-6">
        <h2 class="font-semibold text-sm mb-4">Office Details</h2>
        <form method="POST" action="{{ route('admin.offices.update', $office) }}" class="space-y-4">
          @csrf @method('PATCH')

          <div>
            <label class="block text-sm font-medium mb-1.5">Office Name <span class="text-red-400">*</span></label>
            <input type="text" name="name" value="{{ old('name', $office->name) }}" required
              class="input-field w-full px-4 py-3 rounded-xl text-sm"/>
          </div>
          <div>
            <label class="block text-sm font-medium mb-1.5">Icon (emoji) <span class="text-red-400">*</span></label>
            <input type="text" name="icon" value="{{ old('icon', $office->icon) }}" required
              maxlength="10" class="input-field w-full px-4 py-3 rounded-xl text-sm"
              id="icon-input" oninput="document.getElementById('icon-preview').textContent=this.value||'🏢'"/>
            <p class="text-xs text-stone-400 mt-1">Preview: <span id="icon-preview" class="text-xl">{{ old('icon', $office->icon) }}</span></p>
          </div>
          <div>
            <label class="block text-sm font-medium mb-1.5">Description</label>
            <textarea name="description" rows="3"
              class="input-field w-full px-4 py-3 rounded-xl text-sm resize-none"
              placeholder="What does this office handle?">{{ old('description', $office->description) }}</textarea>
          </div>
          <div class="flex items-center gap-3 p-4 rounded-xl border {{ $office->is_active ? 'bg-green-50 border-green-200' : 'bg-red-50 border-red-200' }}">
            <input type="checkbox" name="is_active" id="is_active" value="1"
              class="w-4 h-4 rounded" {{ old('is_active', $office->is_active) ? 'checked' : '' }}>
            <div>
              <label for="is_active" class="text-sm font-semibold cursor-pointer">Active</label>
              <p class="text-xs text-stone-500 mt-0.5">
                {{ $office->is_active ? 'Visible to citizens in report form.' : 'Hidden from citizens.' }}
              </p>
            </div>
          </div>
          <button type="submit" class="btn-primary w-full py-3.5 rounded-xl text-sm font-semibold">
            Save Changes
          </button>
        </form>
      </div>
    </div>

    {{-- STAFF PANEL --}}
    <div class="md:col-span-2 space-y-4">
      <div class="card rounded-2xl p-5">
        <h2 class="font-semibold text-sm mb-3">Assigned Staff</h2>
        @if($staff->isEmpty())
          <p class="text-xs text-stone-400 py-4 text-center">No staff assigned to this office yet.</p>
          <a href="{{ route('admin.staff.index') }}"
             class="btn-primary w-full py-2.5 rounded-xl text-xs font-semibold text-center block">
            + Add Staff Member
          </a>
        @else
          <div class="space-y-3 mb-3">
            @foreach($staff as $s)
            <div class="flex items-center gap-3">
              <div class="w-8 h-8 rounded-full flex items-center justify-center text-white text-xs font-bold flex-shrink-0"
                   style="background:{{ $s->is_active ? 'var(--accent)' : '#9ca3af' }}">
                {{ strtoupper(substr($s->name, 0, 1)) }}
              </div>
              <div class="flex-1 min-w-0">
                <p class="text-xs font-semibold truncate">{{ $s->name }}</p>
                <p class="text-xs text-stone-400 truncate">{{ $s->email }}</p>
              </div>
              <span class="inline-flex items-center px-1.5 py-0.5 rounded-full text-xs font-medium
                {{ $s->is_active ? 'bg-green-100 text-green-700' : 'bg-red-100 text-red-700' }}">
                {{ $s->is_active ? '●' : '●' }}
              </span>
            </div>
            @endforeach
          </div>
          <a href="{{ route('admin.staff.index') }}"
             class="btn-outline w-full py-2.5 rounded-xl text-xs font-semibold text-center block">
            Manage Staff →
          </a>
        @endif
      </div>

      {{-- Quick stats --}}
      <div class="card rounded-2xl p-5 space-y-3">
        <h2 class="font-semibold text-sm">Office Stats</h2>
        @php
          $total    = $office->reports()->count();
          $resolved = $office->reports()->where('status','Resolved')->count();
          $pending  = $total - $resolved;
        @endphp
        <div class="flex justify-between text-xs"><span class="text-stone-500">Total reports</span><span class="font-semibold">{{ $total }}</span></div>
        <div class="flex justify-between text-xs"><span class="text-stone-500">Resolved</span><span class="font-semibold text-green-600">{{ $resolved }}</span></div>
        <div class="flex justify-between text-xs"><span class="text-stone-500">Pending</span><span class="font-semibold text-amber-600">{{ $pending }}</span></div>
        <div class="flex justify-between text-xs"><span class="text-stone-500">Staff members</span><span class="font-semibold">{{ $staff->count() }}</span></div>
        @if($total > 0)
        <div class="mt-2 h-1.5 bg-stone-100 rounded-full overflow-hidden">
          <div class="h-full rounded-full" style="width:{{ round($resolved/$total*100) }}%;background:var(--sage)"></div>
        </div>
        <p class="text-xs text-stone-400">{{ round($resolved/$total*100) }}% resolved</p>
        @endif
      </div>
    </div>
  </div>

  {{-- DANGER ZONE --}}
  <div class="mt-8 p-5 rounded-2xl border border-red-200 bg-red-50">
    <h3 class="text-sm font-semibold text-red-800 mb-1">Danger Zone</h3>
    <p class="text-xs text-red-600 mb-3">
      @if($reportCount > 0)
        Cannot delete — this office has {{ $reportCount }} report(s). Deactivate it instead to hide it from citizens.
      @else
        Permanently delete this office. Staff will be unassigned. This cannot be undone.
      @endif
    </p>
    @if($reportCount === 0)
    <form method="POST" action="{{ route('admin.offices.destroy', $office) }}"
          onsubmit="return confirm('Permanently delete {{ addslashes($office->name) }}?')">
      @csrf @method('DELETE')
      <button type="submit" class="btn-danger px-5 py-2 rounded-xl text-xs font-semibold">
        Delete Office
      </button>
    </form>
    @else
    <button disabled class="px-5 py-2 rounded-xl text-xs font-semibold bg-stone-200 text-stone-400 cursor-not-allowed">
      Delete Office (has reports)
    </button>
    @endif
  </div>
</div>
@endsection
