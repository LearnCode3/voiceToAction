@extends('layouts.app')
@section('title','Home')
@push('styles')
<style>
  .hero-bg{background:linear-gradient(135deg,#e8f0e8 0%,#faf7f2 60%,#f5ede0 100%);}
  .step-line::after{content:'';position:absolute;top:20px;left:calc(50% + 20px);width:calc(100% - 40px);height:1px;background:#d1ccc6;}
  .cat-chip{border:1px solid #d1ccc6;transition:all .2s;}.cat-chip:hover{border-color:var(--sage);color:var(--sage);background:#f0f5f0;}
</style>
@endpush
@section('content')
<section class="hero-bg py-20 md:py-28 px-4">
  <div class="max-w-4xl mx-auto text-center fade-in">
    <span class="inline-block text-xs font-semibold tracking-widest uppercase mb-4 px-3 py-1 rounded-full" style="background:#e8f0e8;color:var(--sage)">Community-First Platform</span>
    <h1 class="text-4xl md:text-6xl leading-tight mb-6" style="color:var(--charcoal)">Report. Track.<br/><em style="color:var(--sage)">Resolve.</em></h1>
    <p class="text-lg md:text-xl text-stone-500 max-w-2xl mx-auto mb-10 leading-relaxed">VoiceToAction connects citizens with the right government offices to solve everyday community challenges — now with photo evidence support.</p>
    <div class="flex flex-col sm:flex-row gap-3 justify-center">
      <a href="{{ route('report.create') }}" class="btn-primary px-8 py-3.5 rounded-xl text-sm font-semibold inline-block">Report an Issue</a>
      @guest<a href="{{ route('register') }}" class="btn-outline px-8 py-3.5 rounded-xl text-sm font-semibold inline-block">Create Account</a>
      @else<a href="{{ route('dashboard') }}" class="btn-outline px-8 py-3.5 rounded-xl text-sm font-semibold inline-block">My Dashboard</a>@endguest
    </div>
  </div>
</section>

<section class="py-20 px-4">
  <div class="max-w-5xl mx-auto">
    <div class="text-center mb-14"><h2 class="text-3xl md:text-4xl mb-3" style="color:var(--charcoal)">How It Works</h2><p class="text-stone-500 max-w-xl mx-auto">Four simple steps to get your community issue resolved</p></div>
    <div class="grid md:grid-cols-4 gap-8">
      @foreach([['1','Submit & Upload','Describe your issue, choose a category and attach up to 5 photos',false],['2','Auto-Assigned','Instantly routed to the correct government office',false],['3','Tracked Live','Follow progress as staff works on resolving the issue',false],['4','Resolved & Feedback','Rate the resolution once your issue is marked complete',true]] as $s)
      <div class="text-center relative {{ !$s[3]?'step-line':'' }}">
        <div class="w-10 h-10 rounded-full flex items-center justify-center mx-auto mb-4 text-white text-sm font-bold" style="background:{{ $s[3]?'var(--accent)':'var(--sage)' }}">{{ $s[0] }}</div>
        <h3 class="font-semibold text-base mb-2">{{ $s[1] }}</h3>
        <p class="text-stone-500 text-sm leading-relaxed">{{ $s[2] }}</p>
      </div>
      @endforeach
    </div>
  </div>
</section>

<section class="py-16 px-4 bg-white border-y border-stone-100">
  <div class="max-w-5xl mx-auto">
    <div class="text-center mb-10"><h2 class="text-2xl md:text-3xl mb-2">Report Any Category</h2><p class="text-stone-500 text-sm">Every category routes to a specialized office automatically</p></div>
    <div class="flex flex-wrap justify-center gap-3">
      @foreach(\App\Models\Office::where('is_active',true)->orderBy('name')->get() as $o)
      <a href="{{ route('offices.show',$o->slug) }}" class="cat-chip px-4 py-2 rounded-full text-sm text-stone-600">{{ $o->icon }} {{ Str::replace(' Office','',$o->name) }}</a>
      @endforeach
    </div>
  </div>
</section>

{{-- PHOTO FEATURE HIGHLIGHT --}}
<section class="py-16 px-4">
  <div class="max-w-4xl mx-auto">
    <div class="card rounded-3xl p-8 md:p-12 flex flex-col md:flex-row items-center gap-8">
      <div class="flex-shrink-0 text-6xl">📷</div>
      <div>
        <h2 class="text-2xl md:text-3xl mb-3" style="color:var(--charcoal)">Photo Evidence Support</h2>
        <p class="text-stone-500 leading-relaxed mb-4">Attach up to 5 photos to your report to help office staff understand the problem faster. Drag & drop or click to upload — JPEG, PNG, GIF and WEBP supported.</p>
        <a href="{{ route('report.create') }}" class="btn-primary px-6 py-2.5 rounded-xl text-sm font-semibold inline-block">Try it now →</a>
      </div>
    </div>
  </div>
</section>

<section class="py-14 px-4" style="background:var(--charcoal)">
  <div class="max-w-4xl mx-auto grid grid-cols-3 gap-8 text-center text-white">
    <div><div class="text-3xl md:text-4xl font-bold serif mb-1">{{ $totalReports }}</div><div class="text-stone-400 text-sm">Reports Submitted</div></div>
    <div><div class="text-3xl md:text-4xl font-bold serif mb-1">{{ $resolvedReports }}</div><div class="text-stone-400 text-sm">Issues Resolved</div></div>
    <div><div class="text-3xl md:text-4xl font-bold serif mb-1">{{ \App\Models\Office::where('is_active',true)->count() }}</div><div class="text-stone-400 text-sm">Offices Connected</div></div>
  </div>
</section>

<section class="py-20 px-4 text-center">
  <div class="max-w-xl mx-auto">
    <h2 class="text-3xl md:text-4xl mb-4">Ready to make a difference?</h2>
    <p class="text-stone-500 mb-8">Join citizens already using VoiceToAction to improve their communities.</p>
    <div class="flex flex-col sm:flex-row gap-3 justify-center">
      @guest<a href="{{ route('register') }}" class="btn-primary px-8 py-3.5 rounded-xl text-sm font-semibold inline-block">Get Started Free</a>@endguest
      <a href="{{ route('report.create') }}" class="btn-outline px-8 py-3.5 rounded-xl text-sm font-semibold inline-block">Report an Issue</a>
    </div>
  </div>
</section>
@endsection
