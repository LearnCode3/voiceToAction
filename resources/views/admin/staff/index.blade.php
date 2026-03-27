@extends('layouts.app')
@section('title', 'Office Staff')

@push('styles')
<style>
  .badge-active { background:#d1fae5; color:#065f46; }
  .badge-banned { background:#fee2e2; color:#991b1b; }
</style>
@endpush

@section('content')

<div class="bg-white border-b border-stone-100 py-8 px-4">
  <div class="max-w-6xl mx-auto flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
    <div>
      <span class="text-xs font-semibold uppercase tracking-wide px-2.5 py-1 rounded-full bg-purple-100 text-purple-700">Super Admin</span>
      <h1 class="serif text-3xl mt-2">Office Staff</h1>
      <p class="text-stone-500 text-sm mt-1">Create staff accounts and assign them to offices. One office can have multiple staff members.</p>
    </div>
    <a href="{{ route('admin.users.index') }}"
       class="btn-outline px-5 py-2.5 rounded-xl text-sm font-semibold inline-block">
      👤 Manage Citizens
    </a>
  </div>
</div>

<div class="max-w-6xl mx-auto px-4 py-8">

  @if(session('success'))
  <div class="mb-5 p-4 rounded-xl bg-green-50 border border-green-200 text-sm text-green-700 fade-in flex items-center gap-2">
    ✅ {{ session('success') }}
  </div>
  @endif
  @if(session('error'))
  <div class="mb-5 p-4 rounded-xl bg-red-50 border border-red-200 text-sm text-red-700 fade-in flex items-center gap-2">
    ❌ {{ session('error') }}
  </div>
  @endif

  <div class="grid lg:grid-cols-12 gap-8">

    {{-- CREATE STAFF FORM --}}
    <div class="lg:col-span-4">
      <div class="card rounded-2xl p-6 sticky top-24">
        <h2 class="font-semibold text-base mb-1" style="color:var(--charcoal)">Add New Staff Member</h2>
        <p class="text-xs text-stone-400 mb-5">Staff can only access their assigned office's reports.</p>

        @if($errors->any())
        <div class="mb-4 p-3 rounded-xl bg-red-50 border border-red-200 text-xs text-red-700">
          <ul class="space-y-1">@foreach($errors->all() as $e)<li>{{ $e }}</li>@endforeach</ul>
        </div>
        @endif

        <form method="POST" action="{{ route('admin.staff.store') }}" class="space-y-4">
          @csrf
          <div>
            <label class="block text-xs font-semibold mb-1.5">Full Name <span class="text-red-400">*</span></label>
            <input type="text" name="name" value="{{ old('name') }}" required
              placeholder="Jane Doe"
              class="input-field w-full px-3 py-2.5 rounded-xl text-sm"/>
          </div>
          <div>
            <label class="block text-xs font-semibold mb-1.5">Email Address <span class="text-red-400">*</span></label>
            <input type="email" name="email" value="{{ old('email') }}" required
              placeholder="jane@office.gov"
              class="input-field w-full px-3 py-2.5 rounded-xl text-sm"/>
          </div>
          <div>
            <label class="block text-xs font-semibold mb-1.5">Password <span class="text-red-400">*</span></label>
            <input type="password" name="password" required
              placeholder="Min 6 characters"
              class="input-field w-full px-3 py-2.5 rounded-xl text-sm"/>
          </div>
          <div>
            <label class="block text-xs font-semibold mb-1.5">Assign to Office <span class="text-red-400">*</span></label>
            <select name="office_id" required class="input-field w-full px-3 py-2.5 rounded-xl text-sm">
              <option value="">Select an office…</option>
              @foreach($offices as $o)
                <option value="{{ $o->slug }}" {{ old('office_id')===$o->slug ? 'selected' : '' }}>
                  {{ $o->icon }} {{ $o->name }}
                </option>
              @endforeach
            </select>
            <p class="text-xs text-stone-400 mt-1">Staff can only view and update reports for this office.</p>
          </div>
          <button type="submit"
            class="btn-primary w-full py-3 rounded-xl text-sm font-semibold">
            Create Staff Member
          </button>
        </form>
      </div>
    </div>

    {{-- STAFF LIST --}}
    <div class="lg:col-span-8">

      {{-- Group by office --}}
      @php
        $byOffice = $staff->groupBy('office_id');
      @endphp

      @if($staff->isEmpty())
      <div class="card rounded-2xl py-20 text-center text-stone-400">
        <div class="text-5xl mb-3">👷</div>
        <p class="text-sm">No staff members yet. Create one using the form.</p>
      </div>
      @else
      <div class="card rounded-2xl overflow-hidden">
        <table class="w-full text-sm">
          <thead class="bg-stone-50 border-b border-stone-100">
            <tr>
              <th class="text-left px-5 py-3.5 text-xs font-semibold text-stone-500 uppercase tracking-wide">Staff Member</th>
              <th class="text-left px-5 py-3.5 text-xs font-semibold text-stone-500 uppercase tracking-wide">Assigned Office</th>
              <th class="text-center px-5 py-3.5 text-xs font-semibold text-stone-500 uppercase tracking-wide">Status</th>
              <th class="text-right px-5 py-3.5 text-xs font-semibold text-stone-500 uppercase tracking-wide">Actions</th>
            </tr>
          </thead>
          <tbody class="divide-y divide-stone-100">
            @foreach($staff as $s)
            <tr class="hover:bg-stone-50 transition-colors {{ $s->is_active ? '' : 'opacity-60' }}">
              <td class="px-5 py-4">
                <div class="flex items-center gap-3">
                  <div class="w-8 h-8 rounded-full flex items-center justify-center text-white text-xs font-bold flex-shrink-0"
                       style="background:{{ $s->is_active ? 'var(--accent)' : '#9ca3af' }}">
                    {{ strtoupper(substr($s->name, 0, 1)) }}
                  </div>
                  <div>
                    <p class="font-semibold text-sm">{{ $s->name }}</p>
                    <p class="text-xs text-stone-400">{{ $s->email }}</p>
                  </div>
                </div>
              </td>
              <td class="px-5 py-4">
                {{-- Reassign dropdown --}}
                <form method="POST" action="{{ route('admin.staff.reassign', $s) }}">
                  @csrf @method('PATCH')
                  <select name="office_id" onchange="this.form.submit()"
                    class="text-xs border border-stone-200 rounded-lg px-2.5 py-1.5 bg-white focus:outline-none focus:border-green-400 transition-colors">
                    @foreach($offices as $o)
                    <option value="{{ $o->slug }}" {{ $s->office_id === $o->slug ? 'selected' : '' }}>
                      {{ $o->icon }} {{ Str::limit($o->name, 22) }}
                    </option>
                    @endforeach
                  </select>
                </form>
              </td>
              <td class="px-5 py-4 text-center">
                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium
                  {{ $s->is_active ? 'badge-active' : 'badge-banned' }}">
                  {{ $s->is_active ? 'Active' : 'Banned' }}
                </span>
              </td>
              <td class="px-5 py-4">
                <div class="flex items-center justify-end gap-3">
                  @if($s->is_active)
                  <form method="POST" action="{{ route('admin.staff.ban', $s) }}">
                    @csrf @method('PATCH')
                    <button type="submit"
                      onclick="return confirm('Ban {{ addslashes($s->name) }}? They will lose access to the office panel.')"
                      class="text-xs font-medium text-amber-600 hover:text-amber-800 transition-colors">
                      Ban
                    </button>
                  </form>
                  @else
                  <form method="POST" action="{{ route('admin.staff.activate', $s) }}">
                    @csrf @method('PATCH')
                    <button type="submit"
                      class="text-xs font-medium text-green-600 hover:text-green-800 transition-colors">
                      Activate
                    </button>
                  </form>
                  @endif

                  <form method="POST" action="{{ route('admin.staff.delete', $s) }}"
                        onsubmit="return confirm('Permanently delete staff member {{ addslashes($s->name) }}?')">
                    @csrf @method('DELETE')
                    <button type="submit"
                      class="text-xs font-medium text-red-400 hover:text-red-600 transition-colors">
                      Delete
                    </button>
                  </form>
                </div>
              </td>
            </tr>
            @endforeach
          </tbody>
        </table>
      </div>

      {{-- Per-office staff summary --}}
      <div class="mt-6">
        <h3 class="text-sm font-semibold text-stone-500 mb-3 uppercase tracking-wide text-xs">Staff per Office</h3>
        <div class="grid sm:grid-cols-2 gap-3">
          @foreach($offices as $o)
          @php $count = $staff->where('office_id', $o->slug)->count(); @endphp
          <div class="card rounded-xl p-4 flex items-center gap-3">
            <span class="text-xl">{{ $o->icon }}</span>
            <div class="flex-1">
              <p class="text-xs font-semibold">{{ $o->name }}</p>
              <p class="text-xs text-stone-400">{{ $count }} staff member{{ $count !== 1 ? 's' : '' }}</p>
            </div>
            @if($count === 0)
            <span class="text-xs text-amber-500">⚠ Unassigned</span>
            @else
            <span class="text-xs text-green-500">✓ Covered</span>
            @endif
          </div>
          @endforeach
        </div>
      </div>
      @endif
    </div>
  </div>
</div>
@endsection
