@extends('layouts.auth')
@section('title', 'Register')

@section('content')
{{-- LEFT PANEL --}}
<div class="hidden lg:flex lg:w-5/12 flex-col justify-between p-12" style="background:linear-gradient(160deg,#f5ede0 0%,#eeddd0 100%)">
  <a href="{{ route('home') }}" class="flex items-center gap-2">
    <span class="w-8 h-8 rounded-full flex items-center justify-center bg-white/60">
      <svg class="w-4 h-4" fill="none" stroke="currentColor" style="color:#c8860a" viewBox="0 0 24 24">
        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"/>
      </svg>
    </span>
    <span class="font-semibold" style="color:var(--charcoal)">VoiceToAction</span>
  </a>
  <div>
    <h2 class="serif text-4xl leading-tight mb-4" style="color:var(--charcoal)">Be the change in your community.</h2>
    <p class="text-stone-500 leading-relaxed mb-6">Create a free account to submit reports, track progress, and get notified when your issues are resolved.</p>
    <ul class="space-y-3 text-sm text-stone-500">
      @foreach(['Free to use','Track all your reports in one place','Get notified when resolved','Provide feedback on solutions'] as $benefit)
        <li class="flex items-center gap-2">
          <span class="w-5 h-5 rounded-full bg-white/70 flex items-center justify-center text-xs text-green-700">✓</span>
          {{ $benefit }}
        </li>
      @endforeach
    </ul>
  </div>
  <p class="text-xs text-stone-400">© {{ date('Y') }} VoiceToAction</p>
</div>

{{-- RIGHT PANEL --}}
<div class="flex-1 flex items-center justify-center px-4 py-16">
  <div class="w-full max-w-sm fade-in">
    <div class="mb-8">
      <a href="{{ route('home') }}" class="lg:hidden flex items-center gap-2 mb-6">
        <span class="w-7 h-7 rounded-full flex items-center justify-center" style="background:var(--sage)">
          <svg class="w-3.5 h-3.5 text-white" fill="currentColor" viewBox="0 0 20 20"><path d="M10.394 2.08a1 1 0 00-.788 0l-7 3a1 1 0 000 1.84L5.25 8.051a.999.999 0 01.356-.257l4-1.714a1 1 0 11.788 1.838L7.667 9.088l1.94.831a1 1 0 00.787 0l7-3a1 1 0 000-1.838l-7-3z"/></svg>
        </span>
        <span class="font-semibold text-sm">VoiceToAction</span>
      </a>
      <h1 class="serif text-3xl mb-1" style="color:var(--charcoal)">Create account</h1>
      <p class="text-stone-500 text-sm">Join VoiceToAction and start reporting issues</p>
    </div>

    @if($errors->any())
      <div class="mb-4 p-3 rounded-lg bg-red-50 text-red-700 border border-red-200 text-sm">
        <ul class="space-y-1">
          @foreach($errors->all() as $error)
            <li>{{ $error }}</li>
          @endforeach
        </ul>
      </div>
    @endif

    <form method="POST" action="{{ route('register') }}" class="space-y-4">
      @csrf
      <div>
        <label class="block text-sm font-medium mb-1.5">Full name</label>
        <input type="text" name="name" value="{{ old('name') }}" required placeholder="Jane Doe"
          class="input-field w-full px-4 py-3 rounded-xl text-sm bg-white"/>
      </div>
      <div>
        <label class="block text-sm font-medium mb-1.5">Email address</label>
        <input type="email" name="email" value="{{ old('email') }}" required placeholder="you@example.com"
          class="input-field w-full px-4 py-3 rounded-xl text-sm bg-white"/>
      </div>
      <div>
        <label class="block text-sm font-medium mb-1.5">Password</label>
        <input type="password" name="password" required placeholder="Min 6 characters"
          class="input-field w-full px-4 py-3 rounded-xl text-sm bg-white"/>
      </div>
      <div>
        <label class="block text-sm font-medium mb-1.5">Confirm password</label>
        <input type="password" name="password_confirmation" required placeholder="Repeat password"
          class="input-field w-full px-4 py-3 rounded-xl text-sm bg-white"/>
      </div>
      <button type="submit" class="btn-primary w-full py-3.5 rounded-xl text-sm font-semibold mt-2">
        Create Account
      </button>
    </form>

    <p class="text-center text-sm text-stone-500 mt-6">
      Already have an account? <a href="{{ route('login') }}" class="font-medium" style="color:var(--sage)">Sign in</a>
    </p>
    <p class="text-center text-sm text-stone-400 mt-2">
      Or <a href="{{ route('report.create') }}" class="underline hover:text-stone-600">report as a guest</a>
    </p>
  </div>
</div>
@endsection
