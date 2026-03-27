<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8"/>
  <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
  <title>@yield('title') — VoiceToAction</title>
  <script src="https://cdn.tailwindcss.com"></script>
  <link href="https://fonts.googleapis.com/css2?family=DM+Serif+Display:ital@0;1&family=DM+Sans:opsz,wght@9..40,300;9..40,400;9..40,500;9..40,600&display=swap" rel="stylesheet"/>
  <style>
    :root { --sage:#5c7a5a; --cream:#faf7f2; --charcoal:#1e2a1e; }
    body { font-family:'DM Sans',sans-serif; background:var(--cream); color:var(--charcoal); }
    .serif { font-family:'DM Serif Display',serif; }
    .btn-primary { background:var(--sage); color:white; transition:background .2s,transform .15s; }
    .btn-primary:hover { background:var(--charcoal); transform:translateY(-1px); }
    .input-field { border:1.5px solid #e2ddd8; transition:border-color .2s; }
    .input-field:focus { outline:none; border-color:var(--sage); }
    .fade-in { animation:fadeIn .5s ease forwards; }
    @keyframes fadeIn { from{opacity:0;transform:translateY(12px)} to{opacity:1;transform:translateY(0)} }
  </style>
</head>
<body class="min-h-screen flex">
  @yield('content')
</body>
</html>
