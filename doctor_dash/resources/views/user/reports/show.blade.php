@extends('layouts.user')

@section('title', 'Case Details - ' . $title)
@section('header', $title)

@section('content')
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <meta name="user-id" content="{{ auth()->id() }}">
    
    <div class="mb-5 flex items-center justify-between px-1">
        <a href="{{ route('user.reports.index') }}" class="text-sm text-gray-400 hover:text-white flex items-center gap-2 font-medium transition-colors">
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
                <p class="text-gray-400 text-[10px] md:text-[11px] font-medium tracking-wide mt-2">Uploaded on {{ $reports->first()->created_at->format('Y-m-d h:i A') }}</p>
            </div>
            <div class="flex flex-row md:flex-col items-center md:items-end justify-between md:justify-start gap-4 shrink-0 border-t md:border-t-0 border-white/5 pt-4 md:pt-0">
                <span class="inline-flex items-center px-4 py-1.5 rounded-full text-[10px] md:text-[11px] font-bold border {{ \App\Models\Report::STATUSES[$firstReport->status] ?? 'border-slate-500/50 text-slate-400 bg-transparent' }}">
                    {{ $firstReport->status }}
                </span>
                <a href="{{ route('user.reports.downloadBatch', $reports->first()->batch_id) }}" 
                   class="inline-flex items-center gap-2 px-6 py-2.5 rounded-xl bg-white/5 border border-white/10 text-xs font-black text-[#FACC15] hover:bg-white/10 hover:border-[#FACC15] transition-all shadow-lg">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a2 2 0 002 2h12a2 2 0 002-2v-1m-4-4l-4 4m0 0l-4-4m4 4V4" />
                    </svg>
                    Save All
                </a>
            </div>
        </div>
    </div>

    <!-- Tabbed Interface Container -->
    <div id="case-detail-tabs" class="bg-[#0c0c0c] rounded-[24px] border border-white/5 p-5 md:p-8" style="box-shadow: 0 10px 40px -10px rgba(0,0,0,0.5);">
        <!-- Tab Navigation -->
        <div class="flex gap-2 mb-8 border-b border-white/10 overflow-x-auto no-scrollbar scroll-smooth">
            <style>
                .tab-button.active {
                    color: white;
                    border-bottom-color: #FACC15;
                }
            </style>
            @php 
                $summaryReplies = $adminReplies->where('description', 'Automated PDF Summary');
            @endphp
            <button data-tab="files" class="tab-button px-6 py-3 text-sm font-bold text-gray-400 hover:text-white transition-colors border-b-2 border-transparent whitespace-nowrap">
                Case Files
            </button>
            <button data-tab="admin-replies" class="tab-button px-6 py-3 text-sm font-bold text-gray-400 hover:text-white transition-colors border-b-2 border-transparent whitespace-nowrap">
                Admin Reply ({{ $summaryReplies->count() + $adminMessages->count() }})
            </button>
            <button data-tab="notes" class="tab-button px-6 py-3 text-sm font-bold text-gray-400 hover:text-white transition-colors border-b-2 border-transparent whitespace-nowrap">
                Case Notes
            </button>
            <button data-tab="chat" class="tab-button px-6 py-3 text-sm font-bold text-gray-400 hover:text-white transition-colors border-b-2 border-transparent whitespace-nowrap relative">
                Client Talk
                <span id="chat-notification-indicator" class="hidden absolute top-2 right-2 w-2 h-2 bg-red-500 rounded-full"></span>
            </button>
        </div>

        <!-- Case Files Tab Content -->
        <div data-tab-content="files" class="tab-content hidden">
            <div class="flex flex-col sm:flex-row items-start sm:items-center justify-between gap-4 mb-8">
                <h3 class="text-xl font-bold text-white tracking-tight">Files in this collection ({{ $reports->count() }})</h3>
            </div>
            
            @if($reports->count() > 0)
                <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-4 md:gap-6 mb-8">
                    @foreach ($reports as $report)
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
                                            url: {{ json_encode(route("user.reports.preview", $report->id)) }},
                                            downloadUrl: {{ json_encode(route("user.reports.download", $report)) }},
                                            mime: {{ json_encode($report->mime_type) }},
                                            title: {{ json_encode($report->original_name) }},
                                            created: {{ json_encode($report->created_at->format("Y-m-d h:i A")) }}
                                        })'>
                                        View File
                                    </button>

                                    <a href="{{ route('user.reports.download', $report) }}" 
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

        <!-- Admin Reply Tab Content -->
        <div data-tab-content="admin-replies" class="tab-content hidden">
            <div class="flex flex-col sm:flex-row items-start sm:items-center justify-between gap-4 mb-8">
                <h3 class="text-xl font-bold text-white tracking-tight">Staff Replies and Plans ({{ $summaryReplies->count() + $adminMessages->count() }})</h3>
            </div>

            @if($adminMessages->count() > 0)
                <div class="space-y-4 mb-8">
                    <h4 class="text-xs font-bold text-gray-500 uppercase tracking-widest px-1">Staff Messages & Guidance</h4>
                    @foreach($adminMessages as $message)
                        <div class="bg-[#1a1a1a] rounded-2xl border border-white/5 p-5 relative overflow-hidden group">
                           <div class="absolute top-0 right-0 p-3">
                               <span class="text-[9px] font-bold text-gray-500 uppercase tracking-tighter">{{ $message->created_at->format('Y-m-d h:i A') }}</span>
                           </div>
                           <div class="flex items-start gap-4">
                               <div class="h-10 w-10 rounded-full bg-[#FACC15]/10 flex items-center justify-center border border-[#FACC15]/20 shrink-0">
                                   <span class="text-[#FACC15] font-bold text-xs">{{ substr($message->sender->name, 0, 1) }}</span>
                               </div>
                               <div class="space-y-1 pr-16">
                                   <p class="text-[11px] font-bold text-[#FACC15] uppercase tracking-wide">{{ $message->sender->name }}</p>
                                   <p class="text-white text-sm leading-relaxed whitespace-pre-wrap">{{ $message->body }}</p>
                               </div>
                           </div>
                        </div>
                    @endforeach
                </div>
            @endif
            
            @if($summaryReplies->count() > 0)
                <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-4 md:gap-6">
                    @foreach ($summaryReplies as $report)
                        <div class="bg-[#111111] rounded-[20px] border border-white/10 p-5 group flex flex-col justify-between hover:border-white/20 transition-all duration-300">
                            <div class="flex flex-col mb-6">
                                <div class="flex items-start justify-between mb-2">
                                    <p class="text-sm font-bold text-white leading-tight pr-4 break-words line-clamp-2" title="{{ $report->original_name }}">
                                        {{ $report->original_name }}
                                    </p>
                                    @php $isSummary = ($report->description === 'Automated PDF Summary'); @endphp
                                    <span class="shrink-0 inline-flex items-center rounded-md {{ $isSummary ? 'bg-[#FACC15]/20 text-[#FACC15] border-[#FACC15]/30' : 'bg-emerald-500/10 text-emerald-400 border-emerald-500/20' }} px-2 py-0.5 text-[10px] font-bold uppercase tracking-widest mt-0.5 border">
                                        {{ $isSummary ? 'CASE SUMMARY' : str_replace('_', ' ', $report->case_type) . ' • ' . strtoupper(pathinfo($report->original_name, PATHINFO_EXTENSION)) }}
                                    </span>
                                </div>
                                <p class="text-[10px] font-bold text-gray-400 uppercase tracking-widest">
                                    {{ $report->mime_type }} • {{ round($report->size / 1024, 1) }} KB
                                </p>
                                <p class="text-[10px] text-gray-500 mt-1">
                                    Replied: {{ $report->created_at->format('Y-m-d h:i A') }}
                                </p>
                            </div>

                            <div class="flex items-center gap-2">
                                <button type="button"
                                    class="flex-1 flex items-center justify-center gap-2 py-3 rounded-xl bg-black border border-white/10 text-xs font-bold text-white hover:bg-white/5 transition-colors text-center shadow-sm"
                                    onclick='window.openBHPreview({
                                        url: {{ json_encode(route("user.reports.preview", $report->id)) }},
                                        downloadUrl: {{ json_encode(route("user.reports.download", $report)) }},
                                        mime: {{ json_encode($report->mime_type) }},
                                        title: {{ json_encode($report->original_name) }},
                                        created: {{ json_encode($report->created_at->format("Y-m-d h:i A")) }}
                                    })'>
                                    View File
                                </button>

                                <a href="{{ route('user.reports.download', $report) }}" 
                                   class="flex-1 flex items-center justify-center gap-2 py-3 rounded-xl bg-emerald-500 border border-emerald-500 text-xs font-black text-black hover:bg-emerald-400 transition-colors text-center shadow-sm">
                                    Save File
                                </a>
                            </div>
                        </div>
                    @endforeach
                </div>
            @elseif($adminMessages->count() === 0)
                <div class="flex flex-col items-center justify-center py-16 px-4 text-center bg-white/5 rounded-3xl border border-dashed border-white/10">
                    <div class="h-16 w-16 rounded-2xl bg-white/5 flex items-center justify-center border border-white/10 mb-4">
                        <svg class="h-8 w-8 text-white/20" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M20 13V6a2 2 0 00-2-2H6a2 2 0 00-2 2v7m16 0v5a2 2 0 01-2 2H6a2 2 0 01-2-2v-5m16 0h-2.586a1 1 0 00-.707.293l-2.414 2.414a1 1 0 01-.707.293h-3.172a1 1 0 01-.707-.293l-2.414-2.414A1 1 0 006.586 13H4" />
                        </svg>
                    </div>
                    <p class="text-white font-bold">No replies yet</p>
                    <p class="text-gray-400 text-sm mt-1">Your planning results will appear here once ready.</p>
                </div>
            @endif
        </div>

        <!-- Case Notes Tab Content -->
        <div data-tab-content="notes" class="tab-content hidden">
            <div class="bg-[#111111] rounded-xl border border-white/10 p-5 md:p-6">
                <div class="flex items-center justify-between mb-6">
                    <h3 class="text-xl font-bold text-white tracking-tight">Case Information</h3>
                    <a href="{{ route('user.reports.create') }}" 
                       class="inline-flex items-center justify-center rounded-full px-4 py-2 text-xs font-semibold text-black bg-[#FACC15] hover:bg-[#FACC15]/90 transition-colors">
                        Submit a Case
                    </a>
                </div>
                
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

        <div data-tab-content="chat" class="tab-content hidden">
            <div class="w-full mx-auto">
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

