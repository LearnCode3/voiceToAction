<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8"/>
  <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
  <title>@yield('title', 'VoiceToAction') — Community Challenge Tracker</title>
  <script src="https://cdn.tailwindcss.com"></script>
  <link href="https://fonts.googleapis.com/css2?family=DM+Serif+Display:ital@0;1&family=DM+Sans:opsz,wght@9..40,300;9..40,400;9..40,500;9..40,600&display=swap" rel="stylesheet"/>
  <style>
    :root {
      --sage:#5c7a5a; --sage-light:#8aab87; --cream:#faf7f2;
      --charcoal:#1e2a1e; --accent:#c8860a;
    }
    body { font-family:'DM Sans',sans-serif; background:var(--cream); color:var(--charcoal); }
    h1,h2,h3,.serif { font-family:'DM Serif Display',serif; }
    .btn-primary { background:var(--sage); color:white; transition:background .2s,transform .15s; }
    .btn-primary:hover { background:var(--charcoal); transform:translateY(-1px); }
    .btn-outline { border:1.5px solid var(--sage); color:var(--sage); transition:all .2s; }
    .btn-outline:hover { background:var(--sage); color:white; }
    .btn-danger { background:#ef4444; color:white; transition:background .2s; }
    .btn-danger:hover { background:#b91c1c; }
    .nav-link { transition:color .2s; }
    .nav-link:hover { color:var(--sage); }
    .card { background:white; border:1px solid #e8e4df; }
    .input-field { border:1.5px solid #e2ddd8; transition:border-color .2s; background:white; }
    .input-field:focus { outline:none; border-color:var(--sage); }
    .fade-in { animation:fadeIn .5s ease forwards; }
    @keyframes fadeIn { from{opacity:0;transform:translateY(12px)} to{opacity:1;transform:translateY(0)} }
  </style>
  @stack('styles')
</head>
<body class="min-h-screen flex flex-col">

<!-- NAVBAR -->
<nav class="sticky top-0 z-50 bg-white/90 backdrop-blur border-b border-stone-200">
  <div class="max-w-6xl mx-auto px-4 sm:px-6 flex items-center justify-between h-16">

    <a href="{{ route('home') }}" class="flex items-center gap-2">
      <span class="w-8 h-8 rounded-full flex items-center justify-center" style="background:var(--sage)">
        <svg class="w-4 h-4 text-white" fill="currentColor" viewBox="0 0 20 20">
          <path d="M10.394 2.08a1 1 0 00-.788 0l-7 3a1 1 0 000 1.84L5.25 8.051a.999.999 0 01.356-.257l4-1.714a1 1 0 11.788 1.838L7.667 9.088l1.94.831a1 1 0 00.787 0l7-3a1 1 0 000-1.838l-7-3zM3.31 9.397L5 10.12v4.102a8.969 8.969 0 00-1.05-.174 1 1 0 01-.89-.89 11.115 11.115 0 01.25-3.762zM9.3 16.573A9.026 9.026 0 007 14.935v-3.957l1.818.78a3 3 0 002.364 0l5.508-2.361a11.026 11.026 0 01.25 3.762 1 1 0 01-.89.89 8.968 8.968 0 00-5.35 2.524 1 1 0 01-1.4 0z"/>
        </svg>
      </span>
      <span class="font-semibold text-lg tracking-tight" style="color:var(--charcoal)">VoiceToAction</span>
    </a>

    {{-- DESKTOP NAV --}}
    <div class="hidden md:flex items-center gap-5 text-sm font-medium text-stone-600">
      <a href="{{ route('home') }}"
         class="nav-link {{ request()->routeIs('home') ? 'font-semibold' : '' }}"
         style="{{ request()->routeIs('home') ? 'color:var(--sage)' : '' }}">Home</a>

      <a href="{{ route('report.create') }}"
         class="nav-link {{ request()->routeIs('report.*') ? 'font-semibold' : '' }}"
         style="{{ request()->routeIs('report.*') ? 'color:var(--sage)' : '' }}">Report Issue</a>

      <a href="{{ route('offices.index') }}"
         class="nav-link {{ request()->routeIs('offices.*') ? 'font-semibold' : '' }}"
         style="{{ request()->routeIs('offices.*') ? 'color:var(--sage)' : '' }}">Offices</a>

      @auth
        @if(Auth::user()->isRegularUser())
          {{-- Citizens only see their dashboard --}}
          <a href="{{ route('dashboard') }}"
             class="nav-link font-semibold {{ request()->routeIs('dashboard*') ? 'underline' : '' }}"
             style="color:var(--sage)">My Reports</a>

        @elseif(Auth::user()->isOfficeStaff())
          {{-- Staff sees office panel only --}}
          <a href="{{ route('office.panel') }}"
             class="nav-link font-semibold text-amber-700 {{ request()->routeIs('office.*') ? 'underline' : '' }}">
             Office Panel
          </a>

        @elseif(Auth::user()->isSuperAdmin())
          {{-- Super admin full menu --}}
          <a href="{{ route('office.panel') }}"
             class="nav-link font-semibold text-amber-700 {{ request()->routeIs('office.*') ? 'underline' : '' }}">
            Panel
          </a>
          <div class="relative group">
            <button class="nav-link font-semibold text-purple-700 flex items-center gap-0">
              Admin ▾
            </button>
            <div class="absolute right-0 mt-0 w-48 bg-white rounded-xl shadow-lg border border-stone-100 py-1 hidden group-hover:block z-50">
              <a href="{{ route('admin.offices.index') }}"
                 class="flex items-center gap-2 px-4 py-2.5 text-xs hover:bg-stone-50 text-stone-700">
                🏢 Manage Offices
              </a>
              <a href="{{ route('admin.staff.index') }}"
                 class="flex items-center gap-2 px-4 py-2.5 text-xs hover:bg-stone-50 text-stone-700">
                👷 Office Staff
              </a>
              <a href="{{ route('admin.users.index') }}"
                 class="flex items-center gap-2 px-4 py-2.5 text-xs hover:bg-stone-50 text-stone-700">
                👤 All Users
              </a>
            </div>
          </div>
        @endif

        <form method="POST" action="{{ route('logout') }}" class="inline">
          @csrf
          <button type="submit" class="text-stone-400 hover:text-red-500 transition-colors text-xs">
            Logout
          </button>
        </form>
      @else
        <a href="{{ route('login') }}" class="nav-link">Login</a>
        <a href="{{ route('register') }}"
           class="btn-primary px-4 py-2 rounded-lg text-xs font-semibold">Register</a>
      @endauth
    </div>

    {{-- MOBILE HAMBURGER --}}
    <button class="md:hidden p-2 text-stone-600"
            onclick="document.getElementById('mobile-menu').classList.toggle('hidden')">
      <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"/>
      </svg>
    </button>
  </div>

  {{-- MOBILE MENU --}}
  <div id="mobile-menu" class="hidden md:hidden border-t border-stone-100 bg-white px-4 py-3 space-y-1 text-sm font-medium">
    <a href="{{ route('home') }}" class="block py-2 nav-link">Home</a>
    <a href="{{ route('report.create') }}" class="block py-2 nav-link">Report Issue</a>
    <a href="{{ route('offices.index') }}" class="block py-2 nav-link">Offices</a>

    @auth
      @if(Auth::user()->isRegularUser())
        <a href="{{ route('dashboard') }}" class="block py-2 font-semibold" style="color:var(--sage)">My Reports</a>

      @elseif(Auth::user()->isOfficeStaff())
        <a href="{{ route('office.panel') }}" class="block py-2 font-semibold text-amber-700">📋 Office Panel</a>

      @elseif(Auth::user()->isSuperAdmin())
        <a href="{{ route('office.panel') }}" class="block py-2 font-semibold text-amber-700">Panel</a>
        <div class="border-t border-stone-100 pt-2 mt-2 space-y-1">
          <p class="text-xs text-stone-400 uppercase tracking-wide px-0 pb-1">Admin</p>
          <a href="{{ route('admin.offices.index') }}" class="block py-2 text-purple-700">🏢 Offices</a>
          <a href="{{ route('admin.staff.index') }}" class="block py-2 text-purple-700">👷 Staff</a>
          <a href="{{ route('admin.users.index') }}" class="block py-2 text-purple-700">👤 Users</a>
        </div>
      @endif

      <div class="border-t border-stone-100 pt-2 mt-2">
        <form method="POST" action="{{ route('logout') }}">
          @csrf
          <button type="submit" class="block py-2 text-red-400 text-left w-full">Logout</button>
        </form>
      </div>
    @else
      <div class="border-t border-stone-100 pt-2 mt-2 space-y-1">
        <a href="{{ route('login') }}" class="block py-2 nav-link">Login</a>
        <a href="{{ route('register') }}" class="block py-2 font-semibold" style="color:var(--sage)">Register</a>
      </div>
    @endauth
  </div>
</nav>

{{-- FLASH MESSAGES --}}
@if(session('success') && !session('report_number'))
<div class="max-w-6xl mx-auto px-4 mt-4 fade-in">
  <div class="p-4 rounded-xl bg-green-50 border border-green-200 text-sm text-green-700 flex items-center gap-2">
    ✅ {{ session('success') }}
  </div>
</div>
@endif
@if(session('error'))
<div class="max-w-6xl mx-auto px-4 mt-4 fade-in">
  <div class="p-4 rounded-xl bg-red-50 border border-red-200 text-sm text-red-700 flex items-center gap-2">
    ❌ {{ session('error') }}
  </div>
</div>
@endif
@if(session('status'))
<div class="max-w-6xl mx-auto px-4 mt-4 fade-in">
  <div class="p-4 rounded-xl bg-blue-50 border border-blue-200 text-sm text-blue-700 flex items-center gap-2">
    ℹ️ {{ session('status') }}
  </div>
</div>
@endif

<main class="flex-1">
  @yield('content')
</main>

<footer class="border-t border-stone-200 py-8 px-4 text-center text-stone-400 text-sm mt-auto">
  <p>© {{ date('Y') }} VoiceToAction · Community Challenge Tracker · Built for the people</p>
</footer>

@stack('scripts')
</body>
</html>
