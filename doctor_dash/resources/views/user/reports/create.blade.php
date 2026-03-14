@extends('layouts.user')

@section('title', 'Submit a Case')
@section('header', 'Submit a New Case')

@section('content')
    <div class="max-w-2xl mx-auto">
        <div class="rounded-2xl bg-[#0c0c0c] border border-white/10 p-5 md:p-8 text-sm bh-page-animate shadow-2xl">
            <div class="flex items-center justify-between mb-8">
                <div class="flex items-start md:items-center gap-4">
                    <a href="{{ route('user.reports.index') }}" class="mt-1 md:mt-0 group flex items-center justify-center h-10 w-10 md:h-11 md:w-11 rounded-full bg-white/5 border border-white/10 hover:border-[#FACC15] hover:bg-white/10 transition-all shadow-lg" title="Back to My Cases">
                        <svg class="w-4 h-4 md:w-5 md:h-5 text-white/60 group-hover:text-[#FACC15] transition-colors" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18" />
                        </svg>
                    </a>
                    <div>
                        <h2 class="text-xl md:text-2xl font-black text-white tracking-tight">
                            @if(request('type') === 'full_arch')
                                Submit Full Arch Case
                            @elseif(request('type') === 'single_implant')
                                Submit Single Implant Case
                            @else
                                Submit a Case
                            @endif
                        </h2>
                        <p class="text-[9px] md:text-[11px] text-[#FACC15] font-bold uppercase tracking-widest mt-1">Premium Surgical Planning</p>
                    </div>
                </div>
            </div>

            @if ($errors->any())
                <div class="mb-6 rounded-2xl border border-red-500/30 bg-red-950/50 px-5 py-4 text-sm text-red-200 shadow-xl bh-page-animate">
                    <div class="flex items-center gap-3 mb-2">
                        <svg class="w-5 h-5 text-red-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/></svg>
                        <span class="font-black uppercase tracking-wider">Please fix the following:</span>
                    </div>
                    <ul class="list-disc list-inside space-y-1 ml-8">
                        @foreach ($errors->all() as $error)
                            <li class="font-medium">{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <form id="bh-upload-case-form" action="{{ route('user.reports.store') }}" method="POST" enctype="multipart/form-data" class="space-y-8">
                @csrf
                
                <!-- General Information -->
                <div class="space-y-4">
                    <div class="flex items-center gap-3 border-l-4 border-[#FACC15] pl-4">
                        <h3 class="text-xs font-black uppercase tracking-[0.2em] text-white">General Information</h3>
                    </div>
                    
                    <div class="grid grid-cols-1 gap-6">
                        <div>
                            <label for="title" class="block mb-2 text-[10px] font-black uppercase tracking-widest text-[#FACC15]">
                                Patient Name <span class="text-red-500">*</span>
                            </label>
                            <input id="title" name="title" type="text" value="{{ old('title') }}" required
                                placeholder="Enter patient name" dir="auto"
                                class="w-full rounded-xl border border-white/5 bg-white/5 px-4 py-3.5 text-sm text-white placeholder:text-white/20 focus:outline-none focus:ring-1 focus:ring-[#FACC15] focus:border-[#FACC15] transition-all focus:bg-white/10">
                        </div>
                    </div>
                </div>

                <!-- Specialized Clinical Fields -->
                @if(request('type') === 'full_arch' || request('type') === 'single_implant')
                    <div class="space-y-4 pt-4 border-t border-white/5">
                        <div class="flex items-center gap-3 border-l-4 border-emerald-500 pl-4">
                            <h3 class="text-xs font-black uppercase tracking-[0.2em] text-white">Clinical Requirements</h3>
                        </div>
                        
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <div>
                                <label class="block mb-2 text-[10px] font-black uppercase tracking-widest text-emerald-400">Arch Type</label>
                                <div class="flex gap-4">
                                    <label class="flex-1 flex items-center justify-center p-3 rounded-xl border border-white/5 bg-white/5 cursor-pointer hover:bg-white/10 transition-all has-[:checked]:border-emerald-500 has-[:checked]:text-emerald-400">
                                        <input type="radio" name="arch_type" value="upper" class="hidden">
                                        <span class="text-xs font-bold uppercase tracking-wider">Upper</span>
                                    </label>
                                    <label class="flex-1 flex items-center justify-center p-3 rounded-xl border border-white/5 bg-white/5 cursor-pointer hover:bg-white/10 transition-all has-[:checked]:border-emerald-500 has-[:checked]:text-emerald-400">
                                        <input type="radio" name="arch_type" value="lower" class="hidden">
                                        <span class="text-xs font-bold uppercase tracking-wider">Lower</span>
                                    </label>
                                </div>
                            </div>
                            <div>
                                <label class="block mb-2 text-[10px] font-black uppercase tracking-widest text-emerald-400">Anticipated Implants</label>
                                <input type="number" name="implants_count" placeholder="Number of expected implants"
                                    class="w-full rounded-xl border border-white/5 bg-white/5 px-4 py-3.5 text-sm text-white focus:outline-none focus:ring-1 focus:ring-emerald-500 transition-all focus:bg-white/10">
                            </div>
                        </div>

                        <div>
                            <label class="block mb-2 text-[10px] font-black uppercase tracking-widest text-emerald-400">Implant Brand / Preferred System</label>
                            <input type="text" name="implant_brand" placeholder="e.g. Straumann, Nobel, BioHorizons..."
                                class="w-full rounded-xl border border-white/5 bg-white/5 px-4 py-3.5 text-sm text-white focus:outline-none focus:ring-1 focus:ring-emerald-500 transition-all focus:bg-white/10">
                        </div>
                    </div>
                @endif

                <!-- Description -->
                <div class="space-y-4 pt-4 border-t border-white/5">
                    <div class="flex items-center gap-3 border-l-4 border-sky-500 pl-4">
                        <h3 class="text-xs font-black uppercase tracking-[0.2em] text-white">Instructions & Notes</h3>
                    </div>
                    <textarea id="description" name="description" rows="5"
                        placeholder="Add any specific clinical notes, tooth extractions, or special instructions about this case..."
                        dir="auto"
                        class="w-full rounded-xl border border-white/5 bg-white/5 px-4 py-3.5 text-sm text-white placeholder:text-white/20 focus:outline-none focus:ring-1 focus:ring-sky-500 focus:border-sky-500 transition-all resize-none focus:bg-white/10">{{ old('description') }}</textarea>
                    <p class="text-[10px] text-white/40 font-bold uppercase tracking-widest">Provide as much detail as possible for better planning Results</p>
                </div>

                <!-- File Upload -->
                <div class="space-y-4 pt-4 border-t border-white/5">
                    <div class="flex items-center gap-3 border-l-4 border-purple-500 pl-4">
                        <h3 class="text-xs font-black uppercase tracking-[0.2em] text-white">Records & Assets</h3>
                    </div>
                    
                    <div id="file-inputs">
                        <div class="flex items-center gap-2">
                            <input id="files" type="file" multiple data-report-file-input data-preview-container="#file-previews" class="hidden">
                            <label for="files" class="flex-1 cursor-pointer">
                                <div class="flex flex-col sm:flex-row items-center justify-center gap-4 md:gap-5 rounded-[24px] border-2 border-dashed border-white/10 bg-white/5 px-4 py-8 md:px-6 md:py-10 hover:border-[#FACC15] hover:bg-white/10 transition-all group scale-100 hover:scale-[1.01] active:scale-[0.99] text-center sm:text-left">
                                    <div class="h-14 w-14 md:h-16 md:w-16 rounded-2xl md:rounded-3xl bg-white/5 flex items-center justify-center border border-white/10 group-hover:border-[#FACC15]/30 group-hover:bg-[#FACC15]/5 transition-all outline-none">
                                        <svg class="w-8 h-8 md:w-10 md:h-10 text-white/40 group-hover:text-[#FACC15] transition-colors" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M15 13l-3 3m0 0l-3-3m3 3V10" />
                                        </svg>
                                    </div>
                                    <div class="min-w-0">
                                        <p class="text-base md:text-lg font-black text-white group-hover:text-[#FACC15] transition-colors">Records Upload</p>
                                        <p class="text-[10px] md:text-xs text-white/40 font-medium mt-1">CBCT, DICOM, STL Intraoral Scans, Face Scans, Photos</p>
                                    </div>
                                </div>
                            </label>
                        </div>
                    </div>
                    
                    <div id="file-previews" class="grid grid-cols-1 md:grid-cols-2 gap-4 pt-4 empty:hidden bg-white/5 rounded-2xl p-4 border border-white/5"></div>
                </div>
                
                <!-- Submit Button -->
                <button type="submit" id="main-submit-btn"
                    class="w-full py-5 rounded-2xl bg-[#FACC15] text-black text-sm font-black uppercase tracking-widest flex items-center justify-center gap-3 hover:bg-[#F5C211] transition-all shadow-2xl shadow-yellow-400/20 group hover:scale-[1.01] active:scale-[0.99]">
                    <svg class="w-6 h-6 transition-transform group-hover:rotate-12" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 19l9 2-9-18-9 18 9-2zm0 0v-8" />
                    </svg>
                    <span>Send Case for Planning</span>
                </button>
            </form>
        </div>
    </div>
@endsection

@push('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            var inputsWrapper = document.getElementById('file-inputs');
            var addBtn = document.getElementById('add-file-input');
            var previews = document.getElementById('file-previews');
            var form = document.getElementById('bh-upload-case-form');
            var mainSubmitBtn = document.getElementById('main-submit-btn');

            var activeUploads = 0;

            if (inputsWrapper && previews) {
                
                function renderPreviews(input) {
                    if (!input.files || !input.files.length) return;

                    Array.from(input.files).forEach(function(file) {
                        var fileId = 'file-' + Math.random().toString(36).substr(2, 9);
                        
                        var wrapper = document.createElement('div');
                        wrapper.id = 'wrapper-' + fileId;
                        wrapper.className = 'rounded-xl border border-slate-700/70 bg-slate-900/80 p-4 flex flex-col gap-3 bh-page-animate';

                        var topRow = document.createElement('div');
                        topRow.className = 'flex items-center gap-3';

                        var info = document.createElement('div');
                        info.className = 'flex-1 min-w-0';
                        info.innerHTML = '<p class="text-[13px] font-bold text-slate-100 truncate">' + file.name + '</p>' +
                            '<p class="text-[11px] text-slate-400 font-bold uppercase tracking-wider">' + Math.round(file.size / 1024) + ' KB</p>';

                        topRow.appendChild(info);

                        if (file.type && file.type.startsWith('image/')) {
                            var thumb = document.createElement('img');
                            thumb.className = 'w-10 h-10 rounded-lg object-cover border border-slate-700/70';
                            var reader = new FileReader();
                            reader.onload = function(e) { thumb.src = e.target.result; };
                            reader.readAsDataURL(file);
                            topRow.appendChild(thumb);
                        } else {
                            var ext = file.name.split('.').pop().toUpperCase();
                            var badge = document.createElement('span');
                            badge.className = 'inline-flex items-center rounded-lg bg-yellow-400/10 px-2 py-1 text-[10px] font-black text-yellow-400 border border-yellow-400/20';
                            badge.textContent = ext;
                            topRow.appendChild(badge);
                        }
                        
                        var removeBtn = document.createElement('button');
                        removeBtn.type = 'button';
                        removeBtn.className = 'text-red-400 hover:text-red-300 transition-colors p-1';
                        removeBtn.innerHTML = '<svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" /></svg>';
                        removeBtn.onclick = function() {
                            if (window['xhr_' + fileId]) window['xhr_' + fileId].abort();
                            wrapper.remove();
                            // Also remove associated hidden inputs if they exist
                            var inputs = form.querySelectorAll(`input[data-file-id="${fileId}"]`);
                            inputs.forEach(i => i.remove());
                            activeUploads = Math.max(0, activeUploads - (window['xhr_' + fileId] && window['xhr_' + fileId].readyState !== 4 ? 1 : 0));
                            updateSubmitButtonState();
                        };
                        topRow.appendChild(removeBtn);
                        
                        wrapper.appendChild(topRow);

                        var progressOuter = document.createElement('div');
                        progressOuter.className = 'h-4 w-full rounded-full bg-slate-800/50 overflow-hidden border border-slate-700/50 relative';

                        var progressInner = document.createElement('div');
                        progressInner.className = 'h-full w-0 bg-yellow-400 transition-[width] duration-300 shadow-[0_0_15px_rgba(250,204,21,0.4)] relative z-10';
                        progressInner.id = 'progress-' + fileId;

                        var progressText = document.createElement('span');
                        progressText.className = 'absolute inset-0 flex items-center justify-center text-[9px] font-bold text-white z-20';
                        progressText.id = 'text-' + fileId;
                        progressText.textContent = '0%';

                        progressOuter.appendChild(progressInner);
                        progressOuter.appendChild(progressText);
                        wrapper.appendChild(progressOuter);

                        // Ensure scrolls to previews
                        wrapper.scrollIntoView({ behavior: 'smooth', block: 'end' });

                        // Determine which preview container to use
                        var containerSelector = input.getAttribute('data-preview-container');
                        var targetPreviews = containerSelector ? document.querySelector(containerSelector) : previews;
                        
                        if (targetPreviews) {
                            targetPreviews.appendChild(wrapper);
                        } else {
                            previews.appendChild(wrapper);
                        }

                        // Auto-start upload
                        startIndividualUpload(file, fileId);
                    });
                }

                function startIndividualUpload(file, fileId) {
                    activeUploads++;
                    updateSubmitButtonState();

                    var xhr = new XMLHttpRequest();
                    window['xhr_' + fileId] = xhr;
                    var formData = new FormData();
                    formData.append('file', file);
                    formData.append('_token', '{{ csrf_token() }}');

                    xhr.upload.addEventListener('progress', function(e) {
                        if (!e.lengthComputable) return;
                        var percent = Math.round((e.loaded / e.total) * 100);
                        var bar = document.getElementById('progress-' + fileId);
                        var txtEl = document.getElementById('text-' + fileId);
                        if (bar) bar.style.width = percent + '%';
                        if (txtEl) txtEl.textContent = percent + '%';
                    });

                    xhr.onreadystatechange = function() {
                        if (xhr.readyState !== 4) return;
                        
                        activeUploads--;
                        updateSubmitButtonState();

                        if (xhr.status >= 200 && xhr.status < 300) {
                            var resp = JSON.parse(xhr.responseText);
                            if (resp.ok) {
                                 // Add hidden inputs
                                 var suffix = resp.path.replace(/\./g, '_');
                                form.insertAdjacentHTML('beforeend', `
                                    <input type="hidden" name="temp_paths[]" value="${resp.path}" data-file-id="${fileId}">
                                    <input type="hidden" name="original_names[${suffix}]" value="${resp.original_name}" data-file-id="${fileId}">
                                    <input type="hidden" name="mime_types[${suffix}]" value="${resp.mime_type}" data-file-id="${fileId}">
                                    <input type="hidden" name="sizes[${suffix}]" value="${resp.size}" data-file-id="${fileId}">
                                `);

                                var bar = document.getElementById('progress-' + fileId);
                                var txtEl = document.getElementById('text-' + fileId);
                                if (bar) {
                                    bar.classList.remove('bg-yellow-400');
                                    bar.classList.add('bg-emerald-500');
                                }
                                if (txtEl) {
                                    txtEl.textContent = '✓ Complete';
                                    txtEl.classList.add('text-emerald-400');
                                }
                            }
                        } else {
                            var bar = document.getElementById('progress-' + fileId);
                            var txtEl = document.getElementById('text-' + fileId);
                            if (bar) bar.classList.replace('bg-yellow-400', 'bg-red-500');
                            if (txtEl) txtEl.textContent = 'Failed';
                        }
                    };

                    xhr.open('POST', "{{ route('user.reports.upload-temp') }}");
                    xhr.setRequestHeader('X-Requested-With', 'XMLHttpRequest');
                    xhr.send(formData);
                }

                function updateSubmitButtonState() {
                    if (activeUploads > 0) {
                        mainSubmitBtn.disabled = true;
                        mainSubmitBtn.classList.add('opacity-50', 'cursor-not-allowed');
                        mainSubmitBtn.innerHTML = '<span class="flex items-center justify-center gap-2"><svg class="animate-spin h-5 w-5" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path></svg>Uploading files...</span>';
                    } else {
                        mainSubmitBtn.disabled = false;
                        mainSubmitBtn.classList.remove('opacity-50', 'cursor-not-allowed');
                        mainSubmitBtn.innerHTML = `
                            <svg class="h-4 w-4 transition-transform group-hover:-translate-y-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M15 13l-3 3m0 0l-3-3m3 3V10" />
                            </svg>
                            <span class="text-sm font-bold uppercase tracking-wider">Save case collection</span>
                        `;
                    }
                }

                inputsWrapper.addEventListener('change', function(e) {
                    if (e.target && e.target.matches('[data-report-file-input]')) {
                        renderPreviews(e.target);
                    }
                });

                if (addBtn) {
                    addBtn.addEventListener('click', function() {
                        var row = document.createElement('div');
                        row.className = 'flex items-center gap-2 bh-page-animate mt-2';
                        var input = document.createElement('input');
                        input.type = 'file';
                        input.multiple = true;
                        input.setAttribute('data-report-file-input', '1');
                        input.className = 'w-full text-xs text-slate-200 file:mr-3 file:rounded-md file:border-0 file:bg-yellow-400 file:px-3 file:py-1.5 file:text-xs file:font-semibold file:text-black hover:file:bg-yellow-300';
                        row.appendChild(input);
                        inputsWrapper.appendChild(row);
                    });
                }
            }

            if (form) {
                form.addEventListener('submit', function(e) {
                    if (mainSubmitBtn.disabled) {
                        e.preventDefault();
                        return;
                    }
                });
            }
        });
    </script>
@endpush
