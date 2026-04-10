@extends('layouts.auth')
@section('title','Login')
@section('content')
<div class="hidden lg:flex lg:w-5/12 flex-col justify-between p-12" style="background:linear-gradient(160deg,#e8f0e8,#d4e6d3)">
  <a href="{{ route('home') }}" class="flex items-center gap-2">
    <span class="w-8 h-8 rounded-full flex items-center justify-center" style="background:var(--sage)"><svg class="w-4 h-4 text-white" fill="currentColor" viewBox="0 0 20 20"><path d="M10.394 2.08a1 1 0 00-.788 0l-7 3a1 1 0 000 1.84L5.25 8.051a.999.999 0 01.356-.257l4-1.714a1 1 0 11.788 1.838L7.667 9.088l1.94.831a1 1 0 00.787 0l7-3a1 1 0 000-1.838l-7-3z"/></svg></span>
    <span class="font-semibold" style="color:var(--charcoal)">CivicPulse</span>
  </a>
  <div><h2 class="serif text-4xl leading-tight mb-4" style="color:var(--charcoal)">Your voice shapes your community.</h2><p class="text-stone-500">Log in to track your reports and get notified when issues are resolved.</p></div>
  <p class="text-xs text-stone-400">© {{ date('Y') }} CivicPulse</p>
</div>
<div class="flex-1 flex items-center justify-center px-4 py-16">
  <div class="w-full max-w-sm fade-in">
    <div class="mb-8"><h1 class="serif text-3xl mb-1" style="color:var(--charcoal)">Welcome back</h1><p class="text-stone-500 text-sm">Sign in to continue</p></div>
    @if(session('status'))<div class="mb-4 p-3 rounded-lg bg-blue-50 text-blue-700 border border-blue-200 text-sm">ℹ️ {{ session('status') }}</div>@endif
    @if($errors->any())<div class="mb-4 p-3 rounded-lg bg-red-50 text-red-700 border border-red-200 text-sm">{{ $errors->first() }}</div>@endif
    <form method="POST" action="{{ route('login') }}" class="space-y-4">
      @csrf
      <div><label class="block text-sm font-medium mb-1.5">Email</label><input type="email" name="email" value="{{ old('email') }}" required placeholder="you@example.com" class="input-field w-full px-4 py-3 rounded-xl text-sm bg-white"/></div>
      <div><label class="block text-sm font-medium mb-1.5">Password</label><input type="password" name="password" required placeholder="••••••••" class="input-field w-full px-4 py-3 rounded-xl text-sm bg-white"/></div>
      <div class="flex items-center justify-between">
        <label class="flex items-center gap-2 text-sm text-stone-500 cursor-pointer"><input type="checkbox" name="remember" class="rounded"> Remember me</label>
        <a href="{{ route('password.request') }}" class="text-xs text-stone-400 hover:text-stone-600 underline">Forgot password?</a>
      </div>
      <button type="submit" class="btn-primary w-full py-3.5 rounded-xl text-sm font-semibold">Sign In</button>
    </form>
    <p class="text-center text-sm text-stone-500 mt-6">No account? <a href="{{ route('register') }}" class="font-medium" style="color:var(--sage)">Register</a></p>
    <div class="mt-6 p-4 rounded-xl bg-stone-50 border border-stone-200 text-xs text-stone-500 space-y-1">
      <p class="font-semibold text-stone-600 mb-2">Demo accounts</p>
      <p>Citizen: <strong>user@demo.com</strong> / password</p>
      <p>Staff (Electricity): <strong>staff@demo.com</strong> / password</p>
      <p>Staff (Roads): <strong>roads@demo.com</strong> / password</p>
      <p>Super Admin: <strong>admin@demo.com</strong> / password</p>
    </div>
  </div>
</div>
@endsection
