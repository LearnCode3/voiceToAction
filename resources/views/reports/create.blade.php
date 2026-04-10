@extends('layouts.app')
@section('title','Report an Issue')
@push('styles')
<style>
  .cat-btn{border:1.5px solid #e2ddd8;transition:all .2s;cursor:pointer;background:white;}
  .cat-btn:hover{border-color:var(--sage);}
  .cat-btn.selected{border-color:var(--sage);background:#f0f5f0;}
  .cat-btn.selected .cat-icon{background:var(--sage)!important;}
  #drop-zone{border:2px dashed #e2ddd8;transition:all .2s;}
  #drop-zone.dragover{border-color:var(--sage);background:#f0f5f0;}
  .preview-img{width:80px;height:80px;object-fit:cover;border-radius:8px;}
  .remove-img{position:absolute;top:-6px;right:-6px;width:20px;height:20px;background:#ef4444;color:white;border-radius:50%;font-size:12px;display:flex;align-items:center;justify-content:center;cursor:pointer;line-height:1;}
</style>
@endpush
@section('content')
<div class="max-w-2xl mx-auto px-4 py-12 fade-in">

@if(session('success') && session('report_number'))
  <div class="text-center py-8">
    <div class="w-16 h-16 rounded-full flex items-center justify-center mx-auto mb-6 text-3xl" style="background:#e8f0e8">✅</div>
    <h1 class="serif text-3xl mb-3" style="color:var(--charcoal)">Report Submitted!</h1>
    <p class="text-stone-500 mb-6">Your report has been received and assigned to the right office.</p>
    <div class="card rounded-2xl p-6 text-left space-y-3 text-sm mb-8">
      <div class="flex justify-between pb-3 border-b border-stone-100"><span class="text-stone-500">Report ID</span><span class="font-mono font-semibold text-xs">{{ session('report_number') }}</span></div>
      <div class="flex justify-between py-2"><span class="text-stone-500">Assigned to</span><span class="font-medium" style="color:var(--sage)">{{ session('office_icon') }} {{ session('office_name') }}</span></div>
      <div class="flex justify-between py-2"><span class="text-stone-500">Status</span><span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-blue-100 text-blue-700">Assigned</span></div>
      <div class="flex justify-between py-2"><span class="text-stone-500">Priority</span><span class="font-medium">{{ session('priority') }}</span></div>
      @if(session('image_count') > 0)
      <div class="flex justify-between pt-3 border-t border-stone-100"><span class="text-stone-500">Images uploaded</span><span class="font-medium text-green-600">{{ session('image_count') }} photo{{ session('image_count') > 1 ? 's' : '' }} attached</span></div>
      @endif
    </div>
    <div class="flex flex-col sm:flex-row gap-3 justify-center">
      @auth<a href="{{ route('dashboard') }}" class="btn-primary px-6 py-3 rounded-xl text-sm font-semibold inline-block">View Dashboard</a>
      @else<a href="{{ route('register') }}" class="btn-primary px-6 py-3 rounded-xl text-sm font-semibold inline-block">Track This Report</a>@endauth
      <a href="{{ route('report.create') }}" class="btn-outline px-6 py-3 rounded-xl text-sm font-semibold inline-block">Submit Another</a>
    </div>
  </div>
@else
  <div class="mb-8">
    <span class="text-xs font-semibold px-3 py-1 rounded-full uppercase tracking-wide" style="background:#e8f0e8;color:var(--sage)">Submit Report</span>
    <h1 class="serif text-3xl md:text-4xl mt-3 mb-2">Report a community issue</h1>
    <p class="text-stone-500">@auth Fill in the form below. Your report is automatically routed to the relevant office.
    @else You're reporting as a guest. <a href="{{ route('register') }}" class="underline" style="color:var(--sage)">Create an account</a> to track your report.@endauth</p>
  </div>

  @guest
  <div class="mb-6 p-4 rounded-xl bg-amber-50 border border-amber-200 text-sm text-amber-700 flex items-start gap-3">
    <span class="text-lg">👤</span>
    <div><p class="font-medium">Reporting as a guest</p><p class="text-xs mt-0.5">You won't be able to track this report. <a href="{{ route('register') }}" class="underline font-medium">Create an account</a> to monitor progress.</p></div>
  </div>
  @endguest

  @if($errors->any())
  <div class="mb-4 p-3 rounded-lg bg-red-50 text-red-700 border border-red-200 text-sm"><ul>@foreach($errors->all() as $e)<li>{{ $e }}</li>@endforeach</ul></div>
  @endif

  <form method="POST" action="{{ route('report.store') }}" enctype="multipart/form-data" class="space-y-6">
    @csrf

    {{-- CATEGORY --}}
    <div>
      <label class="block text-sm font-semibold mb-3">Category <span class="text-red-400">*</span></label>
      <div class="grid grid-cols-2 sm:grid-cols-3 gap-2">
        @foreach($offices as $office)
        <button type="button" class="cat-btn rounded-xl p-3 flex items-center gap-2 text-left {{ old('category')===$office->slug?'selected':'' }}" onclick="selectCat('{{ $office->slug }}',this)">
          <span class="cat-icon w-8 h-8 rounded-lg flex items-center justify-center text-base transition-colors" style="background:#f0f5f0">{{ $office->icon }}</span>
          <span class="text-xs font-medium text-stone-700">{{ Str::replace(' Office','',$office->name) }}</span>
        </button>
        @endforeach
      </div>
      <input type="hidden" name="category" id="cat-input" value="{{ old('category') }}"/>
      @error('category')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
    </div>

    {{-- LOCATION --}}
    <div>
      <label class="block text-sm font-semibold mb-1.5">Location / Address <span class="text-red-400">*</span></label>
      <input type="text" name="location" value="{{ old('location') }}" required placeholder="e.g., Kariakoo Street, Block 5" class="input-field w-full px-4 py-3 rounded-xl text-sm"/>
      @error('location')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
    </div>

    {{-- DESCRIPTION --}}
    <div>
      <label class="block text-sm font-semibold mb-1.5">Description <span class="text-red-400">*</span></label>
      <textarea name="description" rows="4" required placeholder="Describe the issue in detail (at least 20 characters)..." class="input-field w-full px-4 py-3 rounded-xl text-sm resize-none">{{ old('description') }}</textarea>
      @error('description')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
    </div>

    {{-- PRIORITY --}}
    <div>
      <label class="block text-sm font-semibold mb-1.5">Priority</label>
      <select name="priority" class="input-field w-full px-4 py-3 rounded-xl text-sm">
        <option value="Low" {{ old('priority','Medium')==='Low'?'selected':'' }}>Low — not urgent</option>
        <option value="Medium" {{ old('priority','Medium')==='Medium'?'selected':'' }}>Medium — should be fixed soon</option>
        <option value="High" {{ old('priority','Medium')==='High'?'selected':'' }}>High — urgent, affecting many</option>
      </select>
    </div>

    {{-- IMAGE UPLOAD --}}
    <div>
      <label class="block text-sm font-semibold mb-1.5">
        Photos <span class="text-stone-400 font-normal text-xs">(optional — up to 5 images, max 5MB each)</span>
      </label>
      <div id="drop-zone" class="rounded-xl p-6 text-center cursor-pointer" onclick="document.getElementById('img-input').click()" ondragover="event.preventDefault();this.classList.add('dragover')" ondragleave="this.classList.remove('dragover')" ondrop="handleDrop(event)">
        <div class="text-3xl mb-2">📷</div>
        <p class="text-sm text-stone-500 font-medium">Click to upload or drag & drop images</p>
        <p class="text-xs text-stone-400 mt-1">JPG, PNG, GIF, WEBP · max 5MB per image · up to 5 photos</p>
      </div>
      <input type="file" id="img-input" name="images[]" accept="image/*" multiple class="hidden" onchange="previewImages(this.files)"/>
      @error('images.*')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
      @error('images')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
      <div id="preview-grid" class="flex flex-wrap gap-3 mt-3"></div>
      <p id="img-count" class="text-xs text-stone-400 mt-1 hidden"></p>
    </div>

    {{-- GUEST FIELDS --}}
    @guest
    <div class="space-y-4 p-4 bg-stone-50 rounded-xl border border-stone-200">
      <p class="text-xs font-semibold text-stone-600 uppercase tracking-wide">Your Contact Info (Optional)</p>
      <div class="grid sm:grid-cols-2 gap-4">
        <div><label class="block text-sm font-medium mb-1.5">Your name</label><input type="text" name="guest_name" value="{{ old('guest_name') }}" placeholder="Anonymous" class="input-field w-full px-4 py-3 rounded-xl text-sm"/></div>
        <div><label class="block text-sm font-medium mb-1.5">Email (optional)</label><input type="email" name="guest_email" value="{{ old('guest_email') }}" placeholder="for reference" class="input-field w-full px-4 py-3 rounded-xl text-sm"/></div>
      </div>
    </div>
    @endguest

    <button type="submit" class="btn-primary w-full py-3.5 rounded-xl text-sm font-semibold">Submit Report →</button>
  </form>
@endif
</div>
@push('scripts')
<script>
  let selectedFiles = [];
  function selectCat(slug, btn) {
    document.getElementById('cat-input').value = slug;
    document.querySelectorAll('.cat-btn').forEach(b => { b.classList.remove('selected'); b.querySelector('.cat-icon').style.background = '#f0f5f0'; });
    btn.classList.add('selected'); btn.querySelector('.cat-icon').style.background = 'var(--sage)';
  }
  // init old value
  const oldCat = document.getElementById('cat-input').value;
  if (oldCat) document.querySelectorAll('.cat-btn').forEach(b => { if (b.getAttribute('onclick').includes("'"+oldCat+"'")) { b.classList.add('selected'); b.querySelector('.cat-icon').style.background = 'var(--sage)'; }});

  function handleDrop(e) {
    e.preventDefault(); document.getElementById('drop-zone').classList.remove('dragover');
    previewImages(e.dataTransfer.files);
  }

  function previewImages(files) {
    const grid  = document.getElementById('preview-grid');
    const count = document.getElementById('img-count');
    const input = document.getElementById('img-input');
    const maxFiles = 5;

    // Merge new files into selectedFiles (avoid duplicates by name+size)
    Array.from(files).forEach(f => {
      if (selectedFiles.length >= maxFiles) return;
      if (!f.type.startsWith('image/')) return;
      if (selectedFiles.find(s => s.name === f.name && s.size === f.size)) return;
      selectedFiles.push(f);
    });

    // Rebuild preview
    grid.innerHTML = '';
    selectedFiles.forEach((file, idx) => {
      const reader = new FileReader();
      reader.onload = e => {
        const wrapper = document.createElement('div'); wrapper.style.cssText = 'position:relative;display:inline-block;';
        const img = document.createElement('img'); img.src = e.target.result; img.className = 'preview-img'; img.title = file.name;
        const rm  = document.createElement('button'); rm.type = 'button'; rm.className = 'remove-img'; rm.innerHTML = '×';
        rm.onclick = () => { selectedFiles.splice(idx, 1); previewImages([]); };
        wrapper.appendChild(img); wrapper.appendChild(rm); grid.appendChild(wrapper);
      };
      reader.readAsDataURL(file);
    });

    // Sync to real input via DataTransfer
    const dt = new DataTransfer();
    selectedFiles.forEach(f => dt.items.add(f));
    input.files = dt.files;

    if (selectedFiles.length > 0) {
      count.textContent = selectedFiles.length + ' photo' + (selectedFiles.length > 1 ? 's' : '') + ' selected';
      count.classList.remove('hidden');
    } else { count.classList.add('hidden'); }
  }
</script>
@endpush
@endsection
