@extends('layouts.app')
@section('title', 'Registered Users')

@push('styles')
<style>
  .badge-active   { background:#d1fae5; color:#065f46; }
  .badge-banned   { background:#fee2e2; color:#991b1b; }
  .tab-btn { border-bottom:2px solid transparent; transition:all .2s; }
  .tab-btn.active { border-color:var(--sage); color:var(--sage); }
</style>
@endpush

@section('content')

<div class="bg-white border-b border-stone-100 py-8 px-4">
  <div class="max-w-6xl mx-auto flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
    <div>
      <span class="text-xs font-semibold uppercase tracking-wide px-2.5 py-1 rounded-full bg-purple-100 text-purple-700">Super Admin</span>
      <h1 class="serif text-3xl mt-2">Registered Users</h1>
      <p class="text-stone-500 text-sm mt-1">Manage citizen accounts — activate, ban or delete</p>
    </div>
    <a href="{{ route('admin.staff.index') }}"
       class="btn-outline px-5 py-2.5 rounded-xl text-sm font-semibold inline-block">
      👥 Manage Staff
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

  {{-- STATS --}}
  <div class="grid grid-cols-3 gap-4 mb-8">
    <div class="card rounded-2xl p-5 text-center">
      <div class="text-2xl font-bold serif mb-1">{{ $totalUsers }}</div>
      <div class="text-xs text-stone-500">Total Users</div>
    </div>
    <div class="card rounded-2xl p-5 text-center">
      <div class="text-2xl font-bold serif mb-1 text-green-600">{{ $active }}</div>
      <div class="text-xs text-stone-500">Active</div>
    </div>
    <div class="card rounded-2xl p-5 text-center">
      <div class="text-2xl font-bold serif mb-1 text-red-500">{{ $banned }}</div>
      <div class="text-xs text-stone-500">Banned</div>
    </div>
  </div>

  {{-- SEARCH + FILTER --}}
  <form method="GET" action="{{ route('admin.users.index') }}" class="flex flex-col sm:flex-row gap-3 mb-6">
    <input type="text" name="search" value="{{ $search }}"
      placeholder="Search by name or email…"
      class="input-field px-4 py-2.5 rounded-xl text-sm flex-1"/>
    <select name="filter" onchange="this.form.submit()"
      class="input-field px-4 py-2.5 rounded-xl text-sm">
      <option value="all"    {{ $filter==='all'    ? 'selected' : '' }}>All Users</option>
      <option value="active" {{ $filter==='active' ? 'selected' : '' }}>Active Only</option>
      <option value="banned" {{ $filter==='banned' ? 'selected' : '' }}>Banned Only</option>
    </select>
    <button type="submit" class="btn-primary px-5 py-2.5 rounded-xl text-sm font-semibold whitespace-nowrap">
      Search
    </button>
    @if($search || $filter !== 'all')
    <a href="{{ route('admin.users.index') }}"
       class="btn-outline px-5 py-2.5 rounded-xl text-sm font-semibold whitespace-nowrap text-center">
      Clear
    </a>
    @endif
  </form>

  {{-- TABLE --}}
  <div class="card rounded-2xl overflow-hidden">
    <table class="w-full text-sm">
      <thead class="bg-stone-50 border-b border-stone-100">
        <tr>
          <th class="text-left px-5 py-3.5 text-xs font-semibold text-stone-500 uppercase tracking-wide">User</th>
          <th class="text-center px-5 py-3.5 text-xs font-semibold text-stone-500 uppercase tracking-wide hidden md:table-cell">Reports</th>
          <th class="text-center px-5 py-3.5 text-xs font-semibold text-stone-500 uppercase tracking-wide hidden sm:table-cell">Joined</th>
          <th class="text-center px-5 py-3.5 text-xs font-semibold text-stone-500 uppercase tracking-wide">Status</th>
          <th class="text-right px-5 py-3.5 text-xs font-semibold text-stone-500 uppercase tracking-wide">Actions</th>
        </tr>
      </thead>
      <tbody class="divide-y divide-stone-100">
        @forelse($users as $u)
        <tr class="hover:bg-stone-50 transition-colors {{ $u->is_active ? '' : 'opacity-60' }}">
          <td class="px-5 py-4">
            <div class="flex items-center gap-3">
              <div class="w-8 h-8 rounded-full flex items-center justify-center text-white text-xs font-bold flex-shrink-0"
                   style="background:{{ $u->is_active ? 'var(--sage)' : '#9ca3af' }}">
                {{ strtoupper(substr($u->name, 0, 1)) }}
              </div>
              <div>
                <p class="font-semibold text-sm" style="color:var(--charcoal)">{{ $u->name }}</p>
                <p class="text-xs text-stone-400">{{ $u->email }}</p>
              </div>
            </div>
          </td>
          <td class="px-5 py-4 text-center hidden md:table-cell">
            <span class="font-semibold">{{ $u->reports()->count() }}</span>
            <span class="text-stone-400 text-xs ml-1">reports</span>
          </td>
          <td class="px-5 py-4 text-center hidden sm:table-cell">
            <span class="text-xs text-stone-500">{{ $u->created_at->format('M d, Y') }}</span>
          </td>
          <td class="px-5 py-4 text-center">
            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium
              {{ $u->is_active ? 'badge-active' : 'badge-banned' }}">
              {{ $u->is_active ? 'Active' : 'Banned' }}
            </span>
          </td>
          <td class="px-5 py-4">
            <div class="flex items-center justify-end gap-3">

              {{-- Ban / Activate toggle --}}
              @if($u->is_active)
              <form method="POST" action="{{ route('admin.users.ban', $u) }}">
                @csrf @method('PATCH')
                <button type="submit"
                  onclick="return confirm('Ban {{ addslashes($u->name) }}? They will be logged out and cannot sign in.')"
                  class="text-xs font-medium text-amber-600 hover:text-amber-800 transition-colors">
                  Ban
                </button>
              </form>
              @else
              <form method="POST" action="{{ route('admin.users.activate', $u) }}">
                @csrf @method('PATCH')
                <button type="submit"
                  class="text-xs font-medium text-green-600 hover:text-green-800 transition-colors">
                  Activate
                </button>
              </form>
              @endif

              {{-- Delete --}}
              <form method="POST" action="{{ route('admin.users.delete', $u) }}"
                    onsubmit="return confirm('Permanently delete {{ addslashes($u->name) }}? Their reports will be kept but unlinked.')">
                @csrf @method('DELETE')
                <button type="submit"
                  class="text-xs font-medium text-red-400 hover:text-red-600 transition-colors">
                  Delete
                </button>
              </form>
            </div>
          </td>
        </tr>
        @empty
        <tr>
          <td colspan="5" class="px-5 py-16 text-center text-stone-400">
            <div class="text-4xl mb-3">👤</div>
            <p>No users found{{ $search ? ' matching "'.$search.'"' : '' }}.</p>
          </td>
        </tr>
        @endforelse
      </tbody>
    </table>
  </div>

  <div class="mt-4">{{ $users->links() }}</div>

  <p class="text-xs text-stone-400 mt-3">
    💡 Banned users are immediately logged out and cannot sign in until reactivated.
    Deleted users' reports are kept but unlinked from their account.
  </p>
</div>
@endsection
