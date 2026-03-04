@extends('layouts.user')

@section('title', 'Upload case')
@section('header', 'Upload new case')

@section('content')
    <div class="max-w-xl mx-auto rounded-2xl bg-slate-900/80 border border-slate-700/70 p-6 text-xs bh-page-animate">
        <div class="flex items-center justify-between mb-6">
            <div class="flex items-center gap-3">
                <a href="{{ route('user.reports.index') }}" class="group flex items-center justify-center h-9 w-9 rounded-full bg-slate-800 border border-slate-700 hover:border-amber-400/50 hover:bg-slate-700 transition-all shadow-lg" title="Back to My Cases">
                    <svg class="w-4 h-4 text-slate-400 group-hover:text-amber-400 transition-colors" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18" />
                    </svg>
                </a>
                <h2 class="text-base md:text-lg font-bold text-slate-100">Upload new case</h2>
            </div>
        </div>

        @if ($errors->any())
            <div class="mb-4 rounded-lg border border-red-500/40 bg-red-500/10 px-3 py-2 text-xs text-red-200">
                <ul class="list-disc list-inside space-y-1">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <form id="bh-upload-case-form" action="{{ route('user.reports.store') }}" method="POST" enctype="multipart/form-data" class="space-y-4">
            @csrf
            <div>
                <label for="title" class="block mb-1">Patient Name</label>
                <input id="title" name="title" type="text" value="{{ old('title') }}" required
                    class="w-full rounded-lg border border-slate-600 bg-slate-950/60 px-3 py-2 text-xs focus:outline-none focus:ring-2 focus:ring-amber-400 focus:border-amber-400">
            </div>
            <div>
                <label for="description" class="block mb-1">Description (optional)</label>
                <textarea id="description" name="description" rows="3"
                    class="w-full rounded-lg border border-slate-600 bg-slate-950/60 px-3 py-2 text-xs focus:outline-none focus:ring-2 focus:ring-amber-400 focus:border-amber-400">{{ old('description') }}</textarea>
            </div>
            <div class="space-y-2">
                <label class="block mb-1">Files (any type, large sizes allowed)</label>
                <div id="file-inputs" class="space-y-2">
                    <div class="flex items-center gap-2">
                        <input id="files" name="files[]" type="file" required multiple data-report-file-input
                            class="w-full text-xs text-slate-200 file:mr-3 file:rounded-md file:border-0 file:bg-yellow-400 file:px-3 file:py-1.5 file:text-xs file:font-semibold file:text-black hover:file:bg-yellow-300">
                    </div>
                </div>
                <button type="button" id="add-file-input"
                    class="btn btn-black btn-sm flex items-center gap-1">
                    <span class="text-sm">+</span>
                    <span>Add another file</span>
                </button>
                <div id="file-previews" class="space-y-3 pt-1"></div>
            </div>
            
            <button type="submit" id="main-submit-btn"
                class="btn btn-yellow w-full py-3 flex items-center justify-center gap-2 group transition-all hover:scale-[1.01] active:scale-[0.99] shadow-lg shadow-yellow-400/10">
                <svg class="h-4 w-4 transition-transform group-hover:-translate-y-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M15 13l-3 3m0 0l-3-3m3 3V10" />
                </svg>
                <span class="text-sm font-bold uppercase tracking-wider">Save case collection</span>
            </button>
        </form>
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

            if (inputsWrapper && addBtn && previews) {
                
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

                        previews.appendChild(wrapper);

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
                                if (bar) bar.classList.replace('bg-yellow-400', 'bg-green-500');
                                if (txtEl) txtEl.textContent = 'Success';
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
                        mainSubmitBtn.innerHTML = '<span class="flex items-center justify-center gap-2">Uploading files...</span>';
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

                addBtn.addEventListener('click', function() {
                    var row = document.createElement('div');
                    row.className = 'flex items-center gap-2 bh-page-animate mt-2';
                    var input = document.createElement('input');
                    input.type = 'file';
                    input.name = 'files[]';
                    input.multiple = true;
                    input.setAttribute('data-report-file-input', '1');
                    input.className = 'w-full text-xs text-slate-200 file:mr-3 file:rounded-md file:border-0 file:bg-yellow-400 file:px-3 file:py-1.5 file:text-xs file:font-semibold file:text-black hover:file:bg-yellow-300';
                    row.appendChild(input);
                    inputsWrapper.appendChild(row);
                });
            }

            if (form) {
                form.addEventListener('submit', function(e) {
                    e.preventDefault();
                    if (mainSubmitBtn.disabled) return;

                    mainSubmitBtn.disabled = true;
                    mainSubmitBtn.innerHTML = '<span class="flex items-center justify-center gap-2">Saving Case...</span>';

                    var formData = new FormData(form);
                    var xhr = new XMLHttpRequest();
                    xhr.open('POST', form.getAttribute('action'));
                    xhr.setRequestHeader('X-Requested-With', 'XMLHttpRequest');
                    xhr.setRequestHeader('Accept', 'application/json');

                    xhr.onreadystatechange = function() {
                        if (xhr.readyState !== 4) return;
                        if (xhr.status >= 200 && xhr.status < 300) {
                            window.location.href = "{{ route('user.reports.index') }}";
                            return;
                        }
                        
                        mainSubmitBtn.disabled = false;
                        mainSubmitBtn.innerHTML = `
                            <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M15 13l-3 3m0 0l-3-3m3 3V10" />
                            </svg>
                            <span class="text-sm font-bold uppercase tracking-wider">Save case collection</span>
                        `;
                        form.submit();
                    };
                    xhr.send(formData);
                });
            }
        });
    </script>
@endpush
