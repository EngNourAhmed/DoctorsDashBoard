@extends('layouts.user')

@section('title', 'My Cases')
@section('header', 'My Cases')

@section('content')
    <div class="flex flex-col md:flex-row md:items-center justify-between gap-4 mb-6">
        <div>
            <h2 class="text-xl md:text-2xl font-bold text-slate-100 tracking-tight">Cases</h2>
            <p class="text-xs text-slate-400 mt-1 font-medium italic">Track and manage your uploaded case collections</p>
        </div>
        <div class="flex flex-wrap items-center gap-3">
            <form action="{{ route('user.reports.index') }}" method="GET" class="flex items-center gap-2">
                <select name="filter" onchange="this.form.submit()" class="bg-slate-900 border border-slate-700 text-slate-200 text-xs rounded-lg px-3 py-2 outline-none focus:border-amber-400/50 transition-colors">
                    <optgroup label="Special Filters" class="bg-slate-900 text-slate-400 text-[10px] uppercase tracking-wider font-bold">
                        <option value="all" {{ request('filter') == 'all' ? 'selected' : '' }} class="bg-slate-900 text-slate-200 text-xs">All Cases</option>
                        <option value="pending" {{ request('filter') == 'pending' ? 'selected' : '' }} class="bg-slate-900 text-slate-200 text-xs">Pending</option>
                        <option value="reviewed" {{ request('filter') == 'reviewed' ? 'selected' : '' }} class="bg-slate-900 text-slate-200 text-xs">Reviewed</option>
                    </optgroup>
                    <optgroup label="By Status" class="bg-slate-900 text-slate-400 text-[10px] uppercase tracking-wider font-bold">
                        @foreach($statuses as $status => $class)
                            <option value="{{ $status }}" {{ request('filter') == $status ? 'selected' : '' }} class="bg-slate-900 text-slate-200 text-xs">{{ $status }}</option>
                        @endforeach
                    </optgroup>
                </select>
            </form>
            <a href="{{ route('user.reports.create') }}"
                class="btn btn-yellow px-6 py-2.5 shadow-lg shadow-yellow-400/10 hover:scale-[1.02] active:scale-[0.98] transition-all">
                <span class="flex items-center gap-2 text-xs font-bold">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                    </svg>
                    Upload new case
                </span>
            </a>
        </div>
    </div>

    <div class="rounded-2xl bg-slate-900/80 border border-slate-700/70 p-4 font-medium">
        @if ($reports->isEmpty())
            <p class="text-slate-400 text-sm">You have not uploaded any cases yet.</p>
        @else
            <div class="overflow-x-auto min-h-[200px]">
                <table class="min-w-full text-sm text-slate-200">
                    <thead class="bg-slate-900/80 text-slate-400 border-b border-slate-700/70">
                        <tr>
                            <th class="text-left py-3 px-4">Patient Name</th>
                            <th class="text-left py-3 px-4">Uploaded at</th>
                            <th class="text-left py-3 px-4">Status</th>
                            <th class="text-left py-3 px-4">Files</th>
                            <th class="text-left py-3 px-4">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($reports as $report)
                            <tr class="border-b border-slate-800/70 last:border-0">
                                <td class="py-3 px-4 text-amber-300 font-semibold">{{ $report->title }}</td>
                                <td class="py-3 px-4 text-slate-400">{{ \Carbon\Carbon::parse($report->created_at)->format('Y-m-d H:i') }}</td>
                                <td class="py-3 px-4">
                                    <div class="flex flex-col">
                                        <span class="bh-status-pill {{ \App\Models\Report::STATUSES[$report->status] ?? 'border-slate-500/50 text-slate-400 bg-slate-500/10' }}">
                                            {{ $report->status }}
                                        </span>
                                    </div>
                                </td>
                                <td class="py-3 px-4">
                                    @if (($report->files_count ?? 1) > 1)
                                        <div class="flex flex-col gap-1">
                                            <span class="text-[10px] font-bold text-amber-500 uppercase tracking-wider flex items-center gap-1">
                                                <svg class="h-3 w-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 7v10a2 2 0 002 2h14a2 2 0 002-2V9a2 2 0 00-2-2h-6l-2-2H5a2 2 0 00-2 2z" /></svg>
                                                Collection
                                            </span>
                                            <a href="{{ route('user.reports.show', $report->batch_id) }}"
                                                class="inline-flex items-center gap-2 text-sm text-amber-300 hover:text-amber-200 underline group">
                                                <div class="p-1 rounded-lg bg-amber-400/10 border border-amber-400/20 group-hover:bg-amber-400/20 transition-colors">
                                                    <svg class="h-3.5 w-3.5 text-amber-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 7v10a2 2 0 002 2h14a2 2 0 002-2V9a2 2 0 00-2-2h-6l-2-2H5a2 2 0 00-2 2z" />
                                                    </svg>
                                                </div>
                                                <span class="text-xs">View Collection ({{ $report->files_count }})</span>
                                            </a>
                                        </div>
                                    @else
                                        <div class="flex flex-col gap-1">
                                            <span class="text-[10px] font-bold text-slate-500 uppercase tracking-wider flex items-center gap-1">
                                                <svg class="h-3 w-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" /></svg>
                                                Single File
                                            </span>
                                            <button type="button"
                                                class="inline-flex items-center gap-2 text-sm text-amber-300 hover:text-amber-200 underline group"
                                                onclick="window.openBHPreview({
                                                    url: '{{ asset('storage/' . $report->file_path) }}',
                                                    downloadUrl: '{{ route('user.reports.download', $report->id) }}',
                                                    mime: '{{ $report->mime_type }}',
                                                    title: '{{ addslashes($report->title) }}',
                                                    name: '{{ addslashes($report->original_name) }}',
                                                    description: '{{ addslashes($report->description) }}',
                                                    created: '{{ \Carbon\Carbon::parse($report->created_at)->format('Y-m-d H:i') }}'
                                                })">
                                                <div class="p-1 rounded-lg bg-slate-800 border border-slate-700 group-hover:bg-slate-700 transition-colors">
                                                    <svg class="h-3.5 w-3.5 text-slate-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                                                    </svg>
                                                </div>
                                                <span class="text-xs">View File</span>
                                            </button>

                                        </div>
                                    @endif
                                </td>
                                <td class="py-3 px-4">
                                    <div class="relative inline-block text-left" data-dropdown-container>
                                        <button type="button" data-dropdown-toggle
                                            class="inline-flex justify-center w-full rounded-lg border border-slate-700 bg-slate-800 px-3 py-1.5 text-xs font-semibold text-white hover:bg-slate-700 focus:outline-none transition-all">
                                            Actions
                                            <svg class="-mr-1 ml-2 h-4 w-4" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor">
                                                <path fill-rule="evenodd" d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.292a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z" clip-rule="evenodd" />
                                            </svg>
                                        </button>

                                        <div data-dropdown-menu
                                            class="hidden origin-top-right absolute right-0 mt-2 w-40 rounded-xl shadow-2xl bg-slate-800 border border-slate-700 ring-1 ring-black ring-opacity-5 z-20 focus:outline-none">
                                            <div class="py-1">
                                                <a href="{{ route('user.reports.show', $report->batch_id ?? $report->id) }}"
                                                    class="block px-4 py-2 text-sm text-slate-200 hover:bg-slate-700 hover:text-white transition-colors">Details</a>
                                                <a href="{{ route('user.reports.edit', $report->id) }}"
                                                    class="block px-4 py-2 text-sm text-amber-300 hover:bg-slate-700 hover:text-white transition-colors">Edit</a>
                                                <form action="{{ route('user.reports.destroy', $report->id) }}" method="POST"
                                                    onsubmit="return confirm('Delete this case?');">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit"
                                                        class="w-full text-left block px-4 py-2 text-sm text-red-400 hover:bg-slate-700 hover:text-white transition-colors">Delete</button>
                                                </form>
                                            </div>
                                        </div>
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            <div class="mt-4">
                {{ $reports->links('vendor.pagination.custom') }}
            </div>
        @endif
    </div>

@endsection

@push('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Dropdown handler
            document.addEventListener('click', function(e) {
                var toggle = e.target.closest('[data-dropdown-toggle]');
                var allMenus = document.querySelectorAll('[data-dropdown-menu]');

                if (toggle) {
                    var container = toggle.closest('[data-dropdown-container]');
                    var menu = container.querySelector('[data-dropdown-menu]');
                    var isHidden = menu.classList.contains('hidden');

                    allMenus.forEach(function(m) { m.classList.add('hidden'); });

                    if (isHidden) {
                        menu.classList.remove('hidden');
                    }
                } else {
                    allMenus.forEach(function(m) { m.classList.add('hidden'); });
                }
            });
        });
    </script>
@endpush


