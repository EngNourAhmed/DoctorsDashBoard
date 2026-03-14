@extends('layouts.admin')

@section('title', 'Dashboard')
@section('header', 'Overview')

@section('content')
    <div class="mb-6 flex flex-col sm:flex-row sm:items-center justify-between gap-4">
        <h2 class="text-sm md:text-base font-semibold text-slate-200">Overview</h2>
        <a href="{{ route('admin.export.dashboard') }}" id="export-btn"
            class="btn btn-yellow relative overflow-hidden group min-w-[140px]">
            <span class="btn-text">Export as PDF</span>
            <div class="btn-spinner hidden absolute inset-0 flex items-center justify-center bg-[#FACC15]">
                <div class="premium-spinner text-black"></div>
            </div>
        </a>
    </div>

    <div class="grid gap-6 md:grid-cols-2 xl:grid-cols-3">
        {{-- Total Users Card --}}
        <div class="bh-card p-6">
            <div class="flex items-start justify-between">
                <div>
                    <p class="text-xs md:text-sm uppercase tracking-[0.22em] text-slate-400">Total Users</p>
                    <p class="mt-4 text-3xl md:text-4xl font-semibold">{{ $totalUsers }}</p>
                </div>
                <div class="bh-card-icon h-10 w-10 flex items-center justify-center">
                    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor" class="h-5 w-5">
                        <path d="M7.5 7.5a3 3 0 116 0 3 3 0 01-6 0z" />
                        <path d="M4.5 18a4.5 4.5 0 019 0v.75a.75.75 0 01-.75.75h-7.5a.75.75 0 01-.75-.75V18z" />
                        <path d="M15.75 8.25a2.25 2.25 0 112.25 2.25 2.25 2.25 0 01-2.25-2.25z" />
                        <path d="M15.004 18.75a3.751 3.751 0 013.746-3.5 3.75 3.75 0 013.75 3.75v.75a.75.75 0 01-.75.75h-5.996" />
                    </svg>
                </div>
            </div>
            <div class="mt-4 flex gap-3 text-xs">
                <a href="{{ route('admin.users.index') }}"
                    class="inline-flex items-center justify-center rounded-full border border-slate-600/70 px-3 py-1.5 text-xs font-semibold text-slate-200 hover:border-amber-400 hover:text-amber-300 transition-colors">
                    View users
                </a>
            </div>
        </div>

        {{-- Pending Cases Card --}}
        <div class="bh-card p-6 border-amber-500/30 bg-amber-500/5">
            <div class="flex items-start justify-between">
                <div>
                    <p class="text-xs md:text-sm uppercase tracking-[0.22em] text-amber-500/80 font-bold">Pending Cases</p>
                    <p class="mt-4 text-3xl md:text-4xl font-semibold text-amber-400">{{ $pendingCasesCount }}</p>
                </div>
                <div class="bh-card-icon h-10 w-10 flex items-center justify-center border-amber-500/30 text-amber-400">
                    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor" class="h-5 w-5">
                        <path fill-rule="evenodd" d="M12 2.25c-5.385 0-9.75 4.365-9.75 9.75s4.365 9.75 9.75 9.75 9.75-4.365 9.75-9.75S17.385 2.25 12 2.25zM12.75 6a.75.75 0 00-1.5 0v6c0 .414.336.75.75.75h4.5a.75.75 0 000-1.5h-3.75V6z" clip-rule="evenodd" />
                    </svg>
                </div>
            </div>
            <div class="mt-4 flex gap-3 text-xs">
                <a href="{{ route('admin.cases.index', ['status' => 'Pending']) }}"
                    class="inline-flex items-center justify-center rounded-full border border-amber-500/40 bg-amber-500/10 px-3 py-1.5 text-xs font-semibold text-amber-300 hover:bg-amber-500/20 hover:border-amber-400 transition-all">
                    Go to Pending
                </a>
            </div>
        </div>

        {{-- All Other Cases Card --}}
        <div class="bh-card p-6 border-emerald-500/30 bg-emerald-500/5">
            <div class="flex items-start justify-between">
                <div>
                    <p class="text-xs md:text-sm uppercase tracking-[0.22em] text-emerald-500/80 font-bold">Other Status Cases</p>
                    <p class="mt-4 text-3xl md:text-4xl font-semibold text-emerald-400">{{ $otherCasesCount }}</p>
                </div>
                <div class="bh-card-icon h-10 w-10 flex items-center justify-center border-emerald-500/30 text-emerald-400">
                    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor" class="h-5 w-5">
                        <path fill-rule="evenodd" d="M2.25 12c0-5.385 4.365-9.75 9.75-9.75s9.75 4.365 9.75 9.75-4.365 9.75-9.75 9.75S2.25 17.385 2.25 12zm13.36-1.814a.75.75 0 10-1.22-.872l-3.236 4.53L9.53 12.22a.75.75 0 00-1.06 1.06l2.25 2.25a.75.75 0 001.14-.094l3.75-5.25z" clip-rule="evenodd" />
                    </svg>
                </div>
            </div>
            <div class="mt-4 flex gap-3 text-xs">
                <a href="{{ route('admin.cases.index', ['status' => 'Other']) }}"
                    class="inline-flex items-center justify-center rounded-full border border-emerald-500/40 bg-emerald-500/10 px-3 py-1.5 text-xs font-semibold text-emerald-300 hover:bg-emerald-500/20 hover:border-emerald-400 transition-all">
                    View other cases
                </a>
            </div>
        </div>

        {{-- Admins Card --}}
        <div class="bh-card p-6">
            <div class="flex items-start justify-between">
                <div>
                    <p class="text-xs md:text-sm uppercase tracking-[0.22em] text-slate-400">Admins</p>
                    <p class="mt-4 text-3xl md:text-4xl font-semibold">{{ $totalAdmins }}</p>
                </div>
                <div class="bh-card-icon h-10 w-10 flex items-center justify-center">
                    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor" class="h-5 w-5">
                        <path d="M12 2.25a.75.75 0 01.673.418l1.89 3.78 4.176.607a.75.75 0 01.416 1.279L16.5 11.25l.714 4.164a.75.75 0 01-1.088.791L12 14.708l-4.126 2.197a.75.75 0 01-1.088-.79L7.5 11.25 4.845 8.334a.75.75 0 01.416-1.28l4.176-.606 1.89-3.78A.75.75 0 0112 2.25z" />
                    </svg>
                </div>
            </div>
            <div class="mt-4 flex gap-3 text-xs">
                <a href="{{ route('admin.users.index', ['role' => 'admin']) }}"
                    class="inline-flex items-center justify-center rounded-full border border-slate-600/70 px-3 py-1.5 text-xs font-semibold text-slate-200 hover:border-amber-400 hover:text-amber-300 transition-colors">
                    View admins
                </a>
            </div>
        </div>


         {{-- Assistants Card --}}
        <div class="bh-card p-6">
            <div class="flex items-start justify-between">
                <div>
                    <p class="text-xs md:text-sm uppercase tracking-[0.22em] text-slate-400">Assistants</p>
                    <p class="mt-4 text-3xl md:text-4xl font-semibold">{{ $totalAssistants }}</p>
                </div>
                <div class="bh-card-icon h-10 w-10 flex items-center justify-center">
                    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor" class="h-5 w-5">
                        <path d="M18 8.25A3.75 3.75 0 1114.25 12 3.75 3.75 0 0118 8.25z" />
                        <path d="M6 8.25A3.75 3.75 0 112.25 12 3.75 3.75 0 016 8.25z" />
                        <path d="M6 13.5a4.5 4.5 0 00-4.5 4.5v1.25A1.25 1.25 0 002.75 20.5h6.5A1.25 1.25 0 0010.5 19.25V18a4.5 4.5 0 00-4.5-4.5z" />
                        <path d="M18 13.5a4.5 4.5 0 00-4.5 4.5v1.25a1.25 1.25 0 001.25 1.25h6.5a1.25 1.25 0 001.25-1.25V18A4.5 4.5 0 0018 13.5z" />
                    </svg>
                </div>
            </div>
            <div class="mt-4 flex gap-3 text-xs">
                <a href="{{ route('admin.assistants.index') }}"
                    class="inline-flex items-center justify-center rounded-full border border-slate-600/70 px-3 py-1.5 text-xs font-semibold text-slate-200 hover:border-amber-400 hover:text-amber-300 transition-colors">
                    View assistants
                </a>
            </div>
        </div>

        {{-- Visits Card --}}
        <div class="bh-card p-6">
            <div class="flex items-start justify-between">
                <div>
                    <p class="text-xs md:text-sm uppercase tracking-[0.22em] text-slate-400">Visits (Today)</p>
                    <p class="mt-4 text-3xl md:text-4xl font-semibold">{{ $todayVisits }}</p>
                    <p class="mt-1 text-[11px] text-slate-400">Total: {{ $totalVisits }}</p>
                </div>
                <div class="bh-card-icon h-10 w-10 flex items-center justify-center">
                    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor" class="h-5 w-5">
                        <path d="M12 4.5a7.5 7.5 0 107.5 7.5A7.509 7.509 0 0012 4.5zm0 1.5a6 6 0 11-6 6 6.007 6.007 0 016-6zm-.75 2.25a.75.75 0 011.5 0v3.19l2.28 2.28a.75.75 0 11-1.06 1.06l-2.47-2.47A.75.75 0 0111.25 12z" />
                    </svg>
                </div>
            </div>
            <div class="mt-4 flex gap-3 text-xs">
                <a href="{{ route('admin.stats') }}"
                    class="inline-flex items-center justify-center rounded-full border border-slate-600/70 px-3 py-1.5 text-xs font-semibold text-slate-200 hover:border-amber-400 hover:text-amber-300 transition-colors">
                    View analytics
                </a>
            </div>
        </div>
    </div>

    <!-- Recent Notifications Section -->
    <div class="mt-8">
        <div class="flex items-center justify-between mb-6">
            <h2 class="text-sm md:text-base font-semibold text-slate-200">Recent Notifications</h2>
            <a href="{{ route('admin.notifications.index') }}" class="text-xs text-amber-300 hover:text-amber-200 transition-colors">View all</a>
        </div>
        
        <div class="grid gap-6 lg:grid-cols-2">
            <!-- Recent Notifications -->
            <div class="rounded-2xl bg-slate-900/80 border border-slate-700/70 p-5">
                <div class="flex items-center justify-between mb-4">
                    <h3 class="text-sm font-semibold">Latest Notifications</h3>
                    <span class="text-xs text-slate-400">Last 5</span>
                </div>

                @php
                    $latestNotifications = auth()->user()->notifications()->latest()->take(5)->get();
                @endphp

                @forelse($latestNotifications as $notification)
                    @php $data = $notification->data; @endphp
                    <div class="flex items-start gap-3 rounded-xl border border-slate-700/50 bg-slate-900/40 px-3 py-2.5 mb-3 {{ !$notification->read_at ? 'bg-amber-400/5 border-amber-400/20' : '' }}">
                        <div class="h-7 w-7 flex items-center justify-center rounded-full {{ !$notification->read_at ? 'bg-amber-400/20 border border-amber-400/30 text-amber-400' : 'bg-amber-400/10 border border-amber-400/20 text-amber-400' }}">
                            @if(isset($data['type']) && str_contains($data['type'], 'message'))
                                <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                    <path d="M21 15a2 2 0 0 1-2 2H7l-4 4V5a2 2 0 0 1 2-2h14a2 2 0 0 1 2 2z"/>
                                </svg>
                            @elseif(isset($data['type']) && str_contains($data['type'], 'response'))
                                <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                    <polyline points="9 17 4 12 9 7"/>
                                    <path d="M20 18v-2a4 4 0 0 0-4-4H4"/>
                                </svg>
                            @elseif(isset($data['type']) && str_contains($data['type'], 'status'))
                                <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                    <polyline points="22 12 18 12 15 21 9 3 6 12 2 12"/>
                                </svg>
                            @else
                                <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                    <path d="M6 8a6 6 0 0 1 12 0c0 7 3 9 3 9H3s3-2 3-9"/>
                                </svg>
                            @endif
                        </div>
                        <div class="flex-1 min-w-0">
                            <div class="flex items-center justify-between gap-2">
                                <p class="text-xs font-bold text-slate-100 {{ !$notification->read_at ? 'text-white' : '' }}">{{ $data['title'] ?? 'System Update' }}</p>
                                <span class="text-[11px] text-slate-500">{{ $notification->created_at->diffForHumans() }}</span>
                            </div>
                            <p class="mt-0.5 text-[11px] text-slate-400 line-clamp-2 {{ !$notification->read_at ? 'text-slate-300 font-medium' : '' }}">{!! strip_tags($data['message'] ?? '') !!}</p>
                            @if(isset($data['batch_id']) && $data['batch_id'])
                                <a href="{{ route('admin.cases.batch', $data['batch_id']) }}" class="inline-flex items-center gap-1 mt-1 text-[10px] text-amber-400 hover:text-amber-300 transition-colors">
                                    <svg xmlns="http://www.w3.org/2000/svg" width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                        <path d="M18 13v6a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2V8a2 2 0 0 1 2-2h6"/>
                                        <polyline points="15,3 21,3 21,9"/>
                                        <line x1="10" y1="14" x2="21" y2="3"/>
                                    </svg>
                                    View Case
                                </a>
                            @endif
                        </div>
                        @if(!$notification->read_at)
                            <div class="h-2 w-2 rounded-full bg-amber-400 shrink-0 mt-1"></div>
                        @endif
                    </div>
                @empty
                    <p class="text-slate-500 text-xs py-4 text-center">No notifications yet.</p>
                @endforelse
            </div>

            <!-- Quick Actions -->
            <div class="rounded-2xl bg-slate-900/80 border border-slate-700/70 p-5">
                <div class="flex items-center justify-between mb-4">
                    <h3 class="text-sm font-semibold">Quick Actions</h3>
                </div>
                
                <div class="space-y-3">
                    <a href="{{ route('admin.cases.index', ['status' => 'Pending']) }}" class="flex items-center gap-3 p-3 rounded-xl border border-slate-700/50 bg-slate-900/40 hover:bg-slate-800/60 transition-colors">
                        <div class="h-8 w-8 rounded-full bg-amber-400/20 flex items-center justify-center text-amber-400">
                            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                <circle cx="12" cy="12" r="10"/>
                                <polyline points="12,6 12,12 16,14"/>
                            </svg>
                        </div>
                        <div>
                            <p class="text-sm font-semibold text-white">Review Pending Cases</p>
                            <p class="text-xs text-slate-400">{{ $pendingCasesCount }} cases waiting</p>
                        </div>
                    </a>

                    <a href="{{ route('admin.users.index') }}" class="flex items-center gap-3 p-3 rounded-xl border border-slate-700/50 bg-slate-900/40 hover:bg-slate-800/60 transition-colors">
                        <div class="h-8 w-8 rounded-full bg-blue-400/20 flex items-center justify-center text-blue-400">
                            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                <path d="M16 21v-2a4 4 0 0 0-4-4H6a4 4 0 0 0-4 4v2"/>
                                <circle cx="9" cy="7" r="4"/>
                                <path d="M22 21v-2a4 4 0 0 0-3-3.87"/>
                                <path d="M16 3.13a4 4 0 0 1 0 7.75"/>
                            </svg>
                        </div>
                        <div>
                            <p class="text-sm font-semibold text-white">Manage Users</p>
                            <p class="text-xs text-slate-400">{{ $totalUsers }} total users</p>
                        </div>
                    </a>

                    <a href="{{ route('admin.stats') }}" class="flex items-center gap-3 p-3 rounded-xl border border-slate-700/50 bg-slate-900/40 hover:bg-slate-800/60 transition-colors">
                        <div class="h-8 w-8 rounded-full bg-emerald-400/20 flex items-center justify-center text-emerald-400">
                            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                <polyline points="22 12 18 12 15 21 9 3 6 12 2 12"/>
                            </svg>
                        </div>
                        <div>
                            <p class="text-sm font-semibold text-white">View Analytics</p>
                            <p class="text-xs text-slate-400">System statistics</p>
                        </div>
                    </a>
                </div>
            </div>
        </div>
    </div>

    @push('scripts')
    <script>
        document.getElementById('export-btn').addEventListener('click', async function(e) {
            e.preventDefault();
            
            const btn = this;
            const text = btn.querySelector('.btn-text');
            const spinner = btn.querySelector('.btn-spinner');
            
            // Show spinner
            text.classList.add('invisible');
            spinner.classList.remove('hidden');
            
            try {
                const response = await fetch(btn.href, {
                    method: 'GET',
                    headers: { 'X-Requested-With': 'XMLHttpRequest' }
                });
                
                if (!response.ok) throw new Error('Download failed');
                
                // Get filename from header if possible
                let filename = 'dashboard_report.pdf';
                const disposition = response.headers.get('Content-Disposition');
                if (disposition && disposition.indexOf('attachment') !== -1) {
                    const filenameRegex = /filename[^;=\n]*=((['"]).*?\2|[^;\n]*)/;
                    const matches = filenameRegex.exec(disposition);
                    if (matches != null && matches[1]) { 
                        filename = matches[1].replace(/['"]/g, '');
                    }
                }
                
                const blob = await response.blob();
                const url = window.URL.createObjectURL(blob);
                const a = document.createElement('a');
                a.href = url;
                a.download = filename;
                document.body.appendChild(a);
                a.click();
                a.remove();
                window.URL.revokeObjectURL(url);
            } catch (error) {
                console.error(error);
                alert('Export failed. Please try again later.');
            } finally {
                // Hide spinner
                text.classList.remove('invisible');
                spinner.classList.add('hidden');
            }
        });
    </script>
    @endpush
@endsection
