 @extends('layouts.admin')

@section('title', 'Case Collection - ' . $title)
@section('header', 'Case Collection')

@section('content')
    <div class="mb-5 flex items-center justify-between px-1">
        <a href="{{ route('admin.cases.index') }}" class="text-sm text-gray-400 hover:text-white flex items-center gap-2 font-medium transition-colors">
            <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18" />
            </svg>
            Back to All Cases
        </a>
    </div>

    <!-- Main Header Card -->
    <div class="bg-[#0c0c0c] rounded-[24px] border border-white/5 p-6 md:p-8 mb-8" style="box-shadow: 0 10px 40px -10px rgba(0,0,0,0.5);">
        <div class="flex flex-col md:flex-row md:items-start justify-between gap-6">
            <div class="space-y-3">
                <h2 class="text-xl md:text-2xl font-bold text-[#FACC15] tracking-tight">{{ $title }}</h2>
                @php $firstReport = $reports->first(); @endphp
                @if($firstReport && $firstReport->description)
                    <p class="text-white text-sm font-medium leading-relaxed max-w-2xl">{{ $firstReport->description }}</p>
                @endif
                <p class="text-gray-400 text-[11px] font-medium tracking-wide mt-2">
                    Collection #{{ $batch_id }} • Uploaded on {{ $reports->first()->created_at->format('Y-m-d H:i') }}
                </p>
            </div>
            <div class="flex flex-col items-end gap-3 shrink-0">
                <span class="inline-flex items-center px-4 py-1.5 rounded-full text-[11px] font-bold border {{ \App\Models\Report::STATUSES[$firstReport->status] ?? 'border-slate-500/50 text-slate-400 bg-transparent' }}">
                    {{ $firstReport->status }}
                </span>
            </div>
        </div>
    </div>

    <!-- Files Grid Container -->
    <div class="bg-[#0c0c0c] rounded-[24px] border border-white/5 p-6 md:p-8" style="box-shadow: 0 10px 40px -10px rgba(0,0,0,0.5);">
        <h3 class="text-xl font-bold mb-8 text-white tracking-tight">Files in this collection ({{ $reports->count() }})</h3>
        
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-6">
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
                    </div>

                    <div class="flex items-center gap-3">
                        <button type="button"
                            class="flex-1 flex items-center justify-center gap-2 py-3 rounded-xl bg-black border border-white/10 text-xs font-bold text-white hover:bg-white/5 transition-colors text-center shadow-sm"
                            onclick="window.openBHPreview({
                                url: '{{ route('admin.cases.preview', $report) }}',
                                downloadUrl: '{{ route('admin.cases.download', $report) }}',
                                mime: '{{ $report->mime_type }}',
                                title: '{{ addslashes($title) }}',
                                name: '{{ addslashes($report->original_name) }}',
                                created: '{{ $report->created_at->format('Y-m-d H:i') }}'
                            })">
                            Preview
                        </button>

                        <a href="{{ route('admin.cases.download', $report) }}" 
                           class="flex-1 flex items-center justify-center gap-2 py-3 rounded-xl bg-[#FACC15] border border-[#FACC15] text-xs font-black text-black hover:bg-[#FACC15]/90 transition-colors text-center shadow-sm">
                            Download
                        </a>
                    </div>
                </div>
            @endforeach
        </div>
    </div>
@endsection
