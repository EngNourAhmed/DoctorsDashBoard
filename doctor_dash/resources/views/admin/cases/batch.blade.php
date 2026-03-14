@extends('layouts.admin')

@section('title', 'Case Collection - ' . $title)
@section('header', 'Case Collection')

@section('content')
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <meta name="user-id" content="{{ auth()->id() }}">
    
    @if(session('success'))
        <div class="mb-6 bg-green-500/10 border border-green-500/20 rounded-xl p-4">
            <p class="text-green-400 text-sm font-medium">{{ session('success') }}</p>
        </div>
    @endif
    
    <div class="mb-5 flex items-center justify-between px-1">
        <a href="{{ route('admin.cases.index') }}" class="text-sm text-gray-400 hover:text-white flex items-center gap-2 font-medium transition-colors">
            <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18" />
            </svg>
            Back to All Cases
        </a>
    </div>

    <!-- Main Header Card -->
    <div class="bg-[#0c0c0c] rounded-[24px] border border-white/5 p-5 md:p-8 mb-6 md:mb-8" style="box-shadow: 0 10px 40px -10px rgba(0,0,0,0.5);">
        <div class="flex flex-col md:flex-row md:items-start justify-between gap-6">
            <div class="space-y-3">
                <h2 class="text-xl md:text-2xl font-bold text-[#FACC15] tracking-tight leading-tight">{{ $title }}</h2>
                @php $firstReport = $reports->first(); @endphp
                @if($firstReport && $firstReport->description)
                    <p class="text-white text-sm font-medium leading-relaxed max-w-2xl">{{ $firstReport->description }}</p>
                @endif
                <p class="text-gray-400 text-[10px] md:text-[11px] font-medium tracking-wide mt-2">
                    Collection #{{ $batch_id }} • Uploaded on {{ $reports->first()->created_at->format('Y-m-d h:i A') }}
                </p>
            </div>
            <div class="flex flex-row md:flex-col items-center md:items-end justify-between md:justify-start gap-4 shrink-0 border-t md:border-t-0 border-white/5 pt-4 md:pt-0">
                <span class="inline-flex items-center px-4 py-1.5 rounded-full text-[10px] md:text-[11px] font-bold border {{ \App\Models\Report::STATUSES[$firstReport->status] ?? 'border-slate-500/50 text-slate-400 bg-transparent' }}">
                    {{ $firstReport->status }}
                </span>
                <a href="{{ route('admin.cases.downloadBatch', $batch_id) }}" 
                   class="inline-flex items-center gap-2 px-6 py-2.5 rounded-xl bg-white/5 border border-white/10 text-xs font-black text-[#FACC15] hover:bg-white/10 hover:border-[#FACC15] transition-all shadow-lg">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a2 2 0 002 2h12a2 2 0 002-2v-1m-4-4l-4 4m0 0l-4-4m4 4V4" />
                    </svg>
                    {{ $reports->count() === 1 ? 'Save File' : 'Save All' }}
                </a>
            </div>
        </div>
    </div>

    <!-- Tabbed Interface Container -->
    <div id="case-detail-tabs" class="bg-[#0c0c0c] rounded-[24px] border border-white/5 p-5 md:p-8" style="box-shadow: 0 10px 40px -10px rgba(0,0,0,0.5);">
        <!-- Tab Navigation -->
        <div class="flex gap-2 mb-8 border-b border-white/10 overflow-x-auto no-scrollbar scroll-smooth">
            <button data-tab="files" class="tab-button px-6 py-3 text-sm font-bold text-gray-400 hover:text-white transition-colors border-b-2 border-transparent whitespace-nowrap">
                Files
            </button>
            <button data-tab="replies" class="tab-button px-6 py-3 text-sm font-bold text-gray-400 hover:text-white transition-colors border-b-2 border-transparent whitespace-nowrap">
                Case Replies ({{ $caseReplies->count() }})
            </button>
            <button data-tab="notes" class="tab-button px-6 py-3 text-sm font-bold text-gray-400 hover:text-white transition-colors border-b-2 border-transparent whitespace-nowrap">
                Case Notes
            </button>
            <button data-tab="chat" class="tab-button px-6 py-3 text-sm font-bold text-gray-400 hover:text-white transition-colors border-b-2 border-transparent whitespace-nowrap relative">
                Client Chat
                <span id="chat-notification-indicator" class="hidden absolute top-2 right-2 w-2 h-2 bg-red-500 rounded-full"></span>
            </button>
        </div>

        <!-- Files Tab Content -->
        <div data-tab-content="files" class="tab-content hidden">
            <div class="flex flex-col sm:flex-row items-start sm:items-center justify-between gap-4 mb-8">
                <h3 class="text-xl font-bold text-white tracking-tight">Original Case Files ({{ $originalFiles->count() }})</h3>
            </div>
            
            @if($originalFiles->count() > 0)
                <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-4 md:gap-6 mb-8">
                    @foreach ($originalFiles as $report)
                        <div class="bg-[#111111] rounded-[20px] border border-white/10 p-5 group flex flex-col justify-between hover:border-white/20 transition-all duration-300">
                            <div class="flex flex-col mb-6">
                                <div class="flex items-start justify-between mb-2">
                                    <p class="text-sm font-bold text-white leading-tight pr-4 break-words line-clamp-2" title="{{ $report->original_name }}">
                                        {{ $report->original_name }}
                                    </p>
                                    <span class="shrink-0 inline-flex items-center rounded-md bg-[#FACC15]/10 px-2 py-0.5 text-[10px] font-bold text-[#FACC15] border border-[#FACC15]/20 uppercase tracking-widest mt-0.5">
                                        {{ strtoupper(pathinfo($report->original_name, PATHINFO_EXTENSION)) }}
                                    </span>
                                </div>
                                <p class="text-[10px] font-bold text-gray-400 uppercase tracking-widest">
                                    {{ $report->mime_type }} • {{ round($report->size / 1024, 1) }} KB
                                </p>
                                <p class="text-[10px] text-gray-500 mt-1">
                                    Uploaded: {{ $report->created_at->format('Y-m-d h:i A') }}
                                </p>
                            </div>

                            <div class="flex flex-col gap-2">
                                <div class="flex items-center gap-2">
                                    <button type="button"
                                        class="flex-1 flex items-center justify-center gap-2 py-3 rounded-xl bg-black border border-white/10 text-xs font-bold text-white hover:bg-white/5 transition-colors text-center shadow-sm"
                                        onclick='window.openBHPreview({
                                            url: {{ json_encode(route("admin.cases.preview", $report)) }},
                                            downloadUrl: {{ json_encode(route("admin.cases.download", $report)) }},
                                            mime: {{ json_encode($report->mime_type) }},
                                            title: {{ json_encode($title) }},
                                            name: {{ json_encode($report->original_name) }},
                                            created: {{ json_encode($report->created_at->format("Y-m-d h:i A")) }}
                                        })'>
                                        View File
                                    </button>

                                    <a href="{{ route('admin.cases.download', $report) }}" 
                                    class="flex-1 flex items-center justify-center gap-2 py-3 rounded-xl bg-[#FACC15] border border-[#FACC15] text-xs font-black text-black hover:bg-[#FACC15]/90 transition-colors text-center shadow-sm">
                                        Save File
                                    </a>
                                </div>
                                <button type="button" 
                                    class="copy-link-btn w-full py-2 rounded-xl bg-white/5 border border-white/10 text-xs font-bold text-white hover:bg-white/10 transition-colors"
                                    data-report-id="{{ $report->id }}">
                                    Copy Link
                                </button>
                            </div>
                        </div>
                    @endforeach
                </div>
            @else
                <p class="text-gray-400 text-center py-8">No files available</p>
            @endif

        </div>

        <!-- Case Replies Tab Content -->
        <div data-tab-content="replies" class="tab-content hidden">
            <div class="flex flex-col sm:flex-row items-start sm:items-center justify-between gap-4 mb-8">
                <h3 class="text-xl font-bold text-white tracking-tight">Clinical Responses & Replies ({{ $caseReplies->count() }})</h3>
            </div>
            
            @if($caseReplies->count() > 0)
                <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-4 md:gap-6 mb-8">
                    @foreach ($caseReplies as $report)
                        <div class="bg-[#111111] rounded-[20px] border border-white/10 p-5 group flex flex-col justify-between hover:border-white/20 transition-all duration-300">
                            <div class="flex flex-col mb-6">
                                <div class="flex items-start justify-between mb-2">
                                    <p class="text-sm font-bold text-[#FACC15] leading-tight pr-4 break-words line-clamp-2" title="{{ $report->original_name }}">
                                        {{ $report->original_name }}
                                    </p>
                                    <span class="shrink-0 inline-flex items-center rounded-md bg-[#FACC15]/10 px-2 py-0.5 text-[10px] font-bold text-[#FACC15] border border-[#FACC15]/20 uppercase tracking-widest mt-0.5">
                                        {{ strtoupper(pathinfo($report->original_name, PATHINFO_EXTENSION)) }}
                                    </span>
                                </div>
                                <div class="space-y-1">
                                    <p class="text-[10px] font-black {{ $report->description === 'Automated PDF Summary' ? 'text-[#FACC15]' : 'text-white/40' }} uppercase tracking-widest">
                                        {{ $report->description === 'Automated PDF Summary' ? 'CLINICAL CASE SUMMARY' : str_replace('_', ' ', $report->case_type) . ' REPLY' }}
                                    </p>
                                    <p class="text-[10px] text-gray-500">
                                        Sent: {{ $report->created_at->format('Y-m-d h:i A') }}
                                    </p>
                                </div>
                            </div>

                            <div class="flex items-center gap-2">
                                <button type="button"
                                    class="flex-1 flex items-center justify-center gap-2 py-3 rounded-xl bg-black border border-white/10 text-xs font-bold text-white hover:bg-white/5 transition-colors text-center shadow-sm"
                                    onclick='window.openBHPreview({
                                        url: {{ json_encode(route("admin.cases.preview", $report)) }},
                                        downloadUrl: {{ json_encode(route("admin.cases.download", $report)) }},
                                        mime: {{ json_encode($report->mime_type) }},
                                        title: {{ json_encode("Reply - " . $title) }},
                                        name: {{ json_encode($report->original_name) }},
                                        created: {{ json_encode($report->created_at->format("Y-m-d h:i A")) }}
                                    })'>
                                    View File
                                </button>

                                <a href="{{ route('admin.cases.download', $report) }}" 
                                   class="flex-1 flex items-center justify-center gap-2 py-3 rounded-xl bg-white/5 border border-white/10 text-xs font-bold text-white hover:bg-white/10 transition-colors text-center shadow-sm">
                                    Save File
                                </a>
                            </div>
                        </div>
                    @endforeach
                </div>
            @else
                <div class="bg-white/[0.02] border border-dashed border-white/10 rounded-2xl p-12 text-center">
                    <p class="text-gray-500 text-sm font-medium">No clinical responses have been submitted for this case yet.</p>
                </div>
            @endif
        </div>

        <!-- Case Notes Tab Content -->
        <div data-tab-content="notes" class="tab-content hidden">
            <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
                <!-- Case Information Card -->
                <div class="lg:col-span-2 space-y-6">
                    <div class="bg-[#111111] rounded-xl border border-white/10 p-5 md:p-6">
                        <h3 class="text-xl font-bold mb-6 text-white tracking-tight">Case Information</h3>
                        
                        <div class="space-y-6">
                            <div>
                                <h4 class="text-sm font-bold text-gray-400 uppercase tracking-wider mb-2">Case Title</h4>
                                <p class="text-white text-lg font-medium">{{ $title }}</p>
                            </div>
                            
                            <div>
                                <h4 class="text-sm font-bold text-gray-400 uppercase tracking-wider mb-2">Description</h4>
                                @if($firstReport && $firstReport->description)
                                    <p class="text-white text-base leading-relaxed whitespace-pre-wrap">{{ $firstReport->description }}</p>
                                @else
                                    <p class="text-gray-500 italic">No description available</p>
                                @endif
                            </div>

                            <div class="grid grid-cols-2 gap-4 pt-4 border-t border-white/10">
                                <div>
                                    <h4 class="text-xs font-bold text-gray-400 uppercase tracking-wider mb-1">Status</h4>
                                    <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-bold border {{ \App\Models\Report::STATUSES[$firstReport->status] ?? 'border-slate-500/50 text-slate-400 bg-transparent' }}">
                                        {{ $firstReport->status }}
                                    </span>
                                </div>
                                <div>
                                    <h4 class="text-xs font-bold text-gray-400 uppercase tracking-wider mb-1">Files Count</h4>
                                    <p class="text-white text-sm font-medium">{{ $reports->count() }} file(s)</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Case Actions -->
                <div class="lg:col-span-1">
                    <div class="bg-[#111111] rounded-xl border border-white/10 p-5 md:p-6 sticky top-6">
                        <h3 class="text-lg font-bold mb-6 text-white tracking-tight">Case Actions</h3>
                        
                        <div class="relative inline-block w-full text-left" data-dropdown-container>
                            <button type="button" data-dropdown-toggle
                                class="w-full flex items-center justify-center gap-2 px-6 py-4 rounded-2xl bg-[#FACC15] border border-[#FACC15] text-sm font-black text-black hover:bg-[#FACC15]/90 hover:scale-[1.02] active:scale-[0.98] transition-all shadow-lg shadow-yellow-400/10">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                                </svg>
                                <span>Submit a case</span>
                                <svg class="ml-1 h-5 w-5 transition-transform duration-200" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor" data-dropdown-arrow>
                                    <path fill-rule="evenodd" d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.292a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z" clip-rule="evenodd" />
                                </svg>
                            </button>

                            <div data-dropdown-menu
                                class="hidden origin-top-right absolute right-0 mt-3 w-full rounded-2xl shadow-2xl bg-[#0c0c0c] border border-white/10 ring-1 ring-black ring-opacity-5 z-50 focus:outline-none overflow-hidden bh-page-animate">
                                <div class="py-2">
                                    <a href="{{ route('admin.cases.create', ['type' => 'full_arch', 'reply_to' => $batch_id, 'user_id' => $reports->first()->user_id]) }}"
                                        class="group flex items-center gap-4 px-5 py-4 text-sm text-slate-200 hover:bg-white/5 hover:text-[#FACC15] transition-all">
                                        <div class="h-10 w-10 rounded-xl bg-yellow-400/10 flex items-center justify-center border border-yellow-400/20 group-hover:bg-yellow-400/20">
                                            <svg class="w-5 h-5 text-[#FACC15]" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19.428 15.428a2 2 0 00-1.022-.547l-2.387-.477a6 6 0 00-3.86.517l-.318.158a6 6 0 01-3.86.517L6.05 15.21a2 2 0 00-1.806.547M8 4h8l-1 1v5.172a2 2 0 00.586 1.414l5 5c1.26 1.26.367 3.414-1.415 3.414H4.828c-1.782 0-2.674-2.154-1.414-3.414l5-5A2 2 0 009 10.172V5L8 4z"/></svg>
                                        </div>
                                        <div class="flex flex-col">
                                            <span class="font-bold text-base">Full Arch Case</span>
                                            <span class="text-[11px] text-slate-500 font-medium tracking-tight mt-0.5">Advanced surgical planning</span>
                                        </div>
                                    </a>
                                    <a href="{{ route('admin.cases.create', ['type' => 'single_implant', 'reply_to' => $batch_id, 'user_id' => $reports->first()->user_id]) }}"
                                        class="group flex items-center gap-4 px-5 py-4 text-sm text-slate-200 hover:bg-white/5 hover:text-[#FACC15] transition-all border-t border-white/5">
                                        <div class="h-10 w-10 rounded-xl bg-yellow-400/10 flex items-center justify-center border border-yellow-400/20 group-hover:bg-yellow-400/20">
                                            <svg class="w-5 h-5 text-[#FACC15]" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"/></svg>
                                        </div>
                                        <div class="flex flex-col">
                                            <span class="font-bold text-base">Single Implant Case</span>
                                            <span class="text-[11px] text-slate-500 font-medium tracking-tight mt-0.5">Rapid planning & guide</span>
                                        </div>
                                    </a>
                                </div>
                            </div>
                        </div>

                        <p class="mt-6 text-xs text-gray-500 text-center leading-relaxed">
                            Select a specialized case type to begin the planning process.
                        </p>
                    </div>
                </div>

            </div>
        </div>

        <!-- Client Chat Tab Content -->
        <div data-tab-content="chat" class="tab-content hidden">
            <div class="max-w-5xl mx-auto">
                <h3 class="text-xl font-bold mb-6 text-white tracking-tight">Case Discussion</h3>
                
                <div class="bg-[#111111] rounded-xl border border-white/10 overflow-hidden">
                    <div class="flex flex-col h-[500px] md:h-[700px]">
                        <!-- Messages Container -->
                        <div id="case-chat-messages" class="flex-1 overflow-y-auto p-4 md:p-6 space-y-4">
                            <!-- Messages will be loaded here by JavaScript -->
                        </div>

                        <!-- Message Input Form -->
                        <div class="border-t border-white/10 p-4 bg-[#0c0c0c]">
                            <form id="case-chat-form" class="flex gap-3">
                                <textarea 
                                    id="case-chat-input" 
                                    placeholder="Type your message..." 
                                    rows="2"
                                    class="flex-1 px-4 py-3 bg-[#111111] border border-white/10 rounded-xl text-white placeholder-gray-500 focus:outline-none focus:border-[#FACC15] resize-none"
                                ></textarea>
                                <button type="submit" class="px-8 py-3 rounded-xl bg-[#FACC15] border border-[#FACC15] text-sm font-black text-black hover:bg-[#FACC15]/90 transition-colors self-end">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 19l9 2-9-18-9 18 9-2zm0 0v-8"/>
                                    </svg>
                                </button>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Initialize CaseDetailTabs
            window.caseDetailTabs = new CaseDetailTabs('case-detail-tabs', 'files');
            
            // Initialize CaseFileUpload
            window.caseFileUpload = new CaseFileUpload('case-file-upload-form', '{{ $batch_id }}');
            
            // Initialize CaseChatManager
            window.caseChatManager = new CaseChatManager(
                '{{ $batch_id }}',
                '{{ route('case.chat.messages', $batch_id) }}',
                '{{ route('case.chat.send', $batch_id) }}'
            );

            // Handle Copy Case Link button
            const copyCaseLinkBtn = document.getElementById('copy-case-link-btn');
            if (copyCaseLinkBtn) {
                copyCaseLinkBtn.addEventListener('click', async function() {
                    try {
                        const response = await fetch('{{ route('reports.batch.generate-link', ['batchId' => $batch_id]) }}', {
                            method: 'POST',
                            headers: {
                                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                                'Content-Type': 'application/json',
                                'Accept': 'application/json'
                            }
                        });
                        const data = await response.json();
                        if (data.url) {
                            const success = await window.copyToClipboard(data.url);
                            if (success) {
                                window.showToast('Collection link copied to clipboard');
                            } else {
                                window.showToast('Copied to clipboard, but your browser might restricted it. Please copy manually: ' + data.url, 'error');
                            }
                        }
                    } catch (error) {
                        window.showToast('Failed to generate collection link', 'error');
                    }
                });
            }

            // Handle Copy Link buttons for individual files
            document.querySelectorAll('.copy-link-btn').forEach(button => {
                button.addEventListener('click', async function() {
                    const reportId = this.dataset.reportId;
                    try {
                        const response = await fetch(`/reports/${reportId}/generate-link`, {
                            method: 'POST',
                            headers: {
                                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                                'Content-Type': 'application/json',
                                'Accept': 'application/json'
                            }
                        });
                        const data = await response.json();
                        if (data.url) {
                            const success = await window.copyToClipboard(data.url);
                            if (success) {
                                window.showToast('File link copied to clipboard');
                            } else {
                                window.showToast('Failed to copy. URL: ' + data.url, 'error');
                            }
                        }
                    } catch (error) {
                        window.showToast('Failed to generate link', 'error');
                    }
                });
            });
        });
    </script>
@endsection
