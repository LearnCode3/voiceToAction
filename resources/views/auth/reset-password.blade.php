@extends('layouts.auth')
@section('title','Reset Password')
@section('content')
<div class="hidden lg:flex lg:w-5/12 flex-col justify-between p-12" style="background:linear-gradient(160deg,#f5ede0,#eeddd0)">
  <a href="{{ route('home') }}" class="flex items-center gap-2"><span class="w-8 h-8 rounded-full flex items-center justify-center" style="background:var(--sage)"><svg class="w-4 h-4 text-white" fill="currentColor" viewBox="0 0 20 20"><path d="M10.394 2.08a1 1 0 00-.788 0l-7 3a1 1 0 000 1.84L5.25 8.051a.999.999 0 01.356-.257l4-1.714a1 1 0 11.788 1.838L7.667 9.088l1.94.831a1 1 0 00.787 0l7-3a1 1 0 000-1.838l-7-3z"/></svg></span><span class="font-semibold" style="color:var(--charcoal)">VoiceToAction</span></a>
  <div><h2 class="serif text-4xl leading-tight mb-4" style="color:var(--charcoal)">Create a new password.</h2><p class="text-stone-500">Choose a strong password of at least 6 characters.</p></div>
  <p class="text-xs text-stone-400">© {{ date('Y') }} VoiceToAction</p>
</div>
<div class="flex-1 flex items-center justify-center px-4 py-16">
  <div class="w-full max-w-sm fade-in">
    <div class="mb-8"><h1 class="serif text-3xl mb-1" style="color:var(--charcoal)">Set new password</h1><p class="text-stone-500 text-sm">Enter and confirm your new password below.</p></div>
    @if($errors->any())<div class="mb-4 p-3 rounded-lg bg-red-50 text-red-700 border border-red-200 text-sm"><ul>@foreach($errors->all() as $e)<li>{{ $e }}</li>@endforeach</ul></div>@endif
    <form method="POST" action="{{ route('password.update') }}" class="space-y-4">
      @csrf
      <input type="hidden" name="token" value="{{ $token }}"/>
      <div><label class="block text-sm font-medium mb-1.5">Email</label><input type="email" name="email" value="{{ old('email',$email) }}" required class="input-field w-full px-4 py-3 rounded-xl text-sm bg-white"/></div>
      <div><label class="block text-sm font-medium mb-1.5">New password</label><input type="password" name="password" required placeholder="Min 6 characters" class="input-field w-full px-4 py-3 rounded-xl text-sm bg-white"/></div>
      <div><label class="block text-sm font-medium mb-1.5">Confirm password</label><input type="password" name="password_confirmation" required class="input-field w-full px-4 py-3 rounded-xl text-sm bg-white"/></div>
      <button type="submit" class="btn-primary w-full py-3.5 rounded-xl text-sm font-semibold">Reset Password</button>
    </form>
    <p class="text-center text-sm text-stone-500 mt-6"><a href="{{ route('login') }}" class="font-medium" style="color:var(--sage)">Back to Sign In</a></p>
  </div>
</div>
@endsection
