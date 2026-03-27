@extends('layouts.app')
@section('title', 'Offices')

@section('content')
<div class="bg-white border-b border-stone-100 py-10 px-4">
  <div class="max-w-6xl mx-auto flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
    <div>
      <h1 class="serif text-3xl md:text-4xl mb-2">Government Offices</h1>
      <p class="text-stone-500">Each community report is automatically routed to the relevant department</p>
    </div>
    @auth
      @if(Auth::user()->isSuperAdmin())
        <a href="{{ route('admin.offices.index') }}" class="btn-primary px-5 py-2.5 rounded-xl text-sm font-semibold inline-block">⚙️ Manage Offices</a>
      @endif
    @endauth
  </div>
</div>

<div class="max-w-6xl mx-auto px-4 py-10">
  <div class="grid sm:grid-cols-2 lg:grid-cols-3 gap-5">
    @foreach($offices as $office)
    @php
      $total    = $office->reports_count ?? 0;
      $resolved = $office->resolved_count ?? 0;
      $pending  = $total - $resolved;
    @endphp
    <a href="{{ route('offices.show', $office->slug) }}"
       class="card rounded-2xl p-6 hover:shadow-md hover:border-green-200 transition-all group">
      <div class="flex items-start justify-between mb-4">
        <span class="text-3xl">{{ $office->icon }}</span>
        @if($pending > 0)
          <span class="inline-flex items-center justify-center w-6 h-6 rounded-full text-xs font-bold text-white" style="background:var(--accent)">{{ $pending }}</span>
        @else
          <span class="text-green-500 text-sm font-medium">All clear ✓</span>
        @endif
      </div>
      <h3 class="font-semibold text-base mb-1 group-hover:text-green-700 transition-colors">{{ $office->name }}</h3>
      @if($office->description)
        <p class="text-xs text-stone-400 mb-3 leading-relaxed">{{ Str::limit($office->description, 80) }}</p>
      @endif
      <div class="flex gap-4 text-xs text-stone-500 mt-3">
        <span>{{ $total }} total</span>
        <span>·</span>
        <span class="text-green-600">{{ $resolved }} resolved</span>
        @if($pending > 0)
          <span>·</span>
          <span class="text-amber-600">{{ $pending }} pending</span>
        @endif
      </div>
      @if($total > 0)
      <div class="mt-3 h-1 bg-stone-100 rounded-full overflow-hidden">
        <div class="h-full rounded-full" style="width:{{ round($resolved / $total * 100) }}%;background:var(--sage)"></div>
      </div>
      @endif
    </a>
    @endforeach
  </div>
</div>
@endsection
