@extends('layouts.app')
@section('title','Manage Offices')
@push('styles')
<style>.badge-active{background:#d1fae5;color:#065f46;}.badge-inactive{background:#fee2e2;color:#991b1b;}</style>
@endpush
@section('content')
<div class="bg-white border-b border-stone-100 py-8 px-4">
  <div class="max-w-6xl mx-auto flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
    <div><span class="text-xs font-semibold uppercase tracking-wide px-2.5 py-1 rounded-full bg-purple-100 text-purple-700">Super Admin</span><h1 class="serif text-3xl mt-2">Manage Offices</h1><p class="text-stone-500 text-sm mt-1">Create, edit and manage government offices</p></div>
    <div class="flex gap-3">
      <a href="{{ route('admin.staff.index') }}" class="btn-outline px-5 py-2.5 rounded-xl text-sm font-semibold">👷 Staff</a>
      <a href="{{ route('admin.offices.create') }}" class="btn-primary px-5 py-2.5 rounded-xl text-sm font-semibold">+ New Office</a>
    </div>
  </div>
</div>
<div class="max-w-6xl mx-auto px-4 py-8">
  @if(session('success'))<div class="mb-5 p-4 rounded-xl bg-green-50 border border-green-200 text-sm text-green-700 fade-in">✅ {{ session('success') }}</div>@endif
  @if(session('error'))<div class="mb-5 p-4 rounded-xl bg-red-50 border border-red-200 text-sm text-red-700 fade-in">❌ {{ session('error') }}</div>@endif
  <div class="grid grid-cols-3 gap-4 mb-8">
    <div class="card rounded-2xl p-5 text-center"><div class="text-2xl font-bold serif mb-1">{{ $offices->count() }}</div><div class="text-xs text-stone-500">Total</div></div>
    <div class="card rounded-2xl p-5 text-center"><div class="text-2xl font-bold serif mb-1 text-green-600">{{ $offices->where('is_active',true)->count() }}</div><div class="text-xs text-stone-500">Active</div></div>
    <div class="card rounded-2xl p-5 text-center"><div class="text-2xl font-bold serif mb-1 text-stone-400">{{ $offices->where('is_active',false)->count() }}</div><div class="text-xs text-stone-500">Inactive</div></div>
  </div>
  <div class="card rounded-2xl overflow-hidden">
    <table class="w-full text-sm">
      <thead class="bg-stone-50 border-b border-stone-100">
        <tr>
          <th class="text-left px-5 py-3.5 text-xs font-semibold text-stone-500 uppercase">Office</th>
          <th class="text-left px-5 py-3.5 text-xs font-semibold text-stone-500 uppercase hidden sm:table-cell">Slug</th>
          <th class="text-center px-5 py-3.5 text-xs font-semibold text-stone-500 uppercase">Staff</th>
          <th class="text-center px-5 py-3.5 text-xs font-semibold text-stone-500 uppercase">Reports</th>
          <th class="text-center px-5 py-3.5 text-xs font-semibold text-stone-500 uppercase">Status</th>
          <th class="text-right px-5 py-3.5 text-xs font-semibold text-stone-500 uppercase">Actions</th>
        </tr>
      </thead>
      <tbody class="divide-y divide-stone-100">
        @forelse($offices as $o)
        <tr class="hover:bg-stone-50 transition-colors {{ $o->is_active?'':'opacity-60' }}">
          <td class="px-5 py-4"><div class="flex items-center gap-3"><span class="text-xl">{{ $o->icon }}</span><div><p class="font-semibold text-sm" style="color:var(--charcoal)">{{ $o->name }}</p>@if($o->description)<p class="text-xs text-stone-400 mt-0.5">{{ Str::limit($o->description,50) }}</p>@endif</div></div></td>
          <td class="px-5 py-4 hidden sm:table-cell"><code class="font-mono text-xs bg-stone-100 px-2 py-1 rounded-md text-stone-600">{{ $o->slug }}</code></td>
          <td class="px-5 py-4 text-center"><span class="font-semibold {{ ($o->staff_count??0)===0?'text-amber-500':'' }}">{{ $o->staff_count??0 }}</span>@if(($o->staff_count??0)===0)<span class="block text-xs text-amber-400">⚠ None</span>@endif</td>
          <td class="px-5 py-4 text-center"><span class="font-semibold">{{ $o->reports_count }}</span></td>
          <td class="px-5 py-4 text-center"><span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium {{ $o->is_active?'badge-active':'badge-inactive' }}">{{ $o->is_active?'Active':'Inactive' }}</span></td>
          <td class="px-5 py-4"><div class="flex items-center justify-end gap-3">
            <a href="{{ route('admin.offices.edit',$o) }}" class="text-xs font-medium text-stone-500 hover:text-stone-800 underline">Edit</a>
            @if($o->reports_count===0)
            <form method="POST" action="{{ route('admin.offices.destroy',$o) }}" onsubmit="return confirm('Delete {{ addslashes($o->name) }}?')">@csrf @method('DELETE')<button type="submit" class="text-xs font-medium text-red-400 hover:text-red-600">Delete</button></form>
            @else<span class="text-xs text-stone-300 cursor-not-allowed" title="Has reports">Delete</span>@endif
          </div></td>
        </tr>
        @empty
        <tr><td colspan="6" class="px-5 py-16 text-center text-stone-400"><div class="text-4xl mb-3">🏢</div><p>No offices yet. <a href="{{ route('admin.offices.create') }}" class="underline" style="color:var(--sage)">Create one</a>.</p></td></tr>
        @endforelse
      </tbody>
    </table>
  </div>
  <p class="text-xs text-stone-400 mt-4">💡 Offices with ⚠ have no staff. Offices with reports cannot be deleted — deactivate instead.</p>
</div>
@endsection
