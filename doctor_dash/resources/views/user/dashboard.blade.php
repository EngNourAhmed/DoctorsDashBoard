@extends('layouts.user')

@section('title', 'User Dashboard')
@section('header', 'My Dashboard')

@section('content')
    <div class="space-y-6">
        <div class="grid gap-6 md:grid-cols-2 xl:grid-cols-4">
            <div class="bh-card p-6">
                <p class="text-xs md:text-sm uppercase tracking-[0.22em] text-slate-400">Total Cases</p>
                <p class="mt-4 text-3xl md:text-4xl font-semibold">{{ $reportsCount }}</p>
                <div class="mt-4 flex gap-3 text-xs">
                    <a href="{{ route('user.reports.index') }}"
                        class="inline-flex items-center justify-center rounded-full border border-slate-600/70 px-3 py-1.5 text-xs font-semibold text-slate-200 hover:border-amber-400 hover:text-amber-300 transition-colors">View
                        all</a>
                    <a href="{{ route('user.reports.create') }}"
                        class="inline-flex items-center justify-center rounded-full px-3 py-1.5 text-xs font-semibold text-black btn btn-yellow">Upload
                        case</a>
                </div>
            </div>

            <div class="bh-card p-6">
                <p class="text-xs md:text-sm uppercase tracking-[0.22em] text-slate-400">Pending Review</p>
                <p class="mt-4 text-3xl md:text-4xl font-semibold text-amber-300">{{ $pendingCasesCount ?? 0 }}</p>
                <div class="mt-4">
                    <a href="{{ route('user.reports.index') }}?filter=pending"
                        class="inline-flex items-center justify-center rounded-full border border-slate-600/70 px-3 py-1.5 text-xs font-semibold text-slate-200 hover:border-amber-400 hover:text-amber-300 transition-colors">
                        View pending
                    </a>
                </div>
            </div>

            <div class="bh-card p-6">
                <p class="text-xs md:text-sm uppercase tracking-[0.22em] text-slate-400">Reviewed</p>
                <p class="mt-4 text-3xl md:text-4xl font-semibold text-emerald-300">{{ $reviewedCasesCount ?? 0 }}</p>
                <div class="mt-4">
                    <a href="{{ route('user.reports.index') }}?filter=reviewed"
                        class="inline-flex items-center justify-center rounded-full border border-slate-600/70 px-3 py-1.5 text-xs font-semibold text-slate-200 hover:border-amber-400 hover:text-amber-300 transition-colors">
                        View reviewed
                    </a>
                </div>
            </div>

            <div class="bh-card p-6">
                <p class="text-xs md:text-sm uppercase tracking-[0.22em] text-slate-400">Unread Messages</p>
                <p class="mt-4 text-3xl md:text-4xl font-semibold">{{ $unreadMessagesCount ?? 0 }}</p>
                <div class="mt-4 flex gap-3 text-xs">
                    <a href="{{ route('user.notifications.index') }}"
                        class="inline-flex items-center justify-center rounded-full border border-slate-600/70 px-3 py-1.5 text-xs font-semibold text-slate-200 hover:border-amber-400 hover:text-amber-300 transition-colors">View
                        messages</a>
                </div>
            </div>

        </div>

        <div class="grid gap-6 lg:grid-cols-2">
            <div class="rounded-2xl bg-slate-900/80 border border-slate-700/70 p-5 text-xs">
                <div class="flex items-center justify-between mb-4">
                    <h2 class="text-sm font-semibold">Recent Cases</h2>
                    <a href="{{ route('user.reports.index') }}" class="text-[11px] text-amber-300 hover:text-amber-200">View all</a>
                </div>

                @if (empty($recentCases) || $recentCases->isEmpty())
                    <p class="text-slate-400 text-xs">No cases yet.</p>
                @else
                    <div class="overflow-x-auto">
                        <table class="min-w-full text-left text-xs">
                            <thead class="border-b border-slate-800 text-slate-400">
                                <tr>
                                    <th class="py-2 pr-4">Patient Name</th>
                                    <th class="py-2 pr-4">Status</th>
                                    <th class="py-2 pr-4">Uploaded</th>
                                    <th class="py-2 pr-4 text-right">Action</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-slate-800">
                                @foreach ($recentCases as $case)
                                    <tr>
                                        <td class="py-2 pr-4 text-slate-100">{{ $case->title }}</td>
                                        <td class="py-2 pr-4">
                                            <div class="flex flex-col">
                                                <span class="inline-flex items-center w-fit rounded-full border {{ \App\Models\Report::STATUSES[$case->status] ?? 'border-slate-500/50 text-slate-400 bg-slate-500/10' }} px-2 py-0.5 text-[9px] font-semibold">
                                                    {{ $case->status }}
                                                </span>
                                               
                                            </div>
                                        </td>
                                        <td class="py-2 pr-4 text-slate-300">{{ $case->created_at->format('M j, Y g:i A') }}</td>
                                        <td class="py-2 pr-4 text-right">
                                            <a href="{{ route('user.reports.edit', $case) }}" class="text-[11px] text-amber-300 hover:text-amber-200">Edit</a>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                @endif
            </div>

            <div class="rounded-2xl bg-slate-900/80 border border-slate-700/70 p-5 text-xs">
                <div class="flex items-center justify-between mb-4">
                    <h2 class="text-sm font-semibold">Notifications & Messages</h2>
                    <a href="{{ route('user.notifications.index') }}" class="text-[11px] text-amber-300 hover:text-amber-200">View all</a>
                </div>

                <div class="space-y-3">
                    @php
                        $latestNotifications = auth()->user()->notifications()->latest()->take(3)->get();
                    @endphp

                    @foreach ($latestNotifications as $notification)
                        @php $data = $notification->data; @endphp
                        <div class="flex items-start gap-3 rounded-xl border border-slate-700/50 bg-slate-900/40 px-3 py-2.5 {{ !$notification->read_at ? 'bg-amber-400/5 border-amber-400/20' : '' }}">
                            <div class="h-7 w-7 flex items-center justify-center rounded-full {{ !$notification->read_at ? 'bg-amber-400/20 border border-amber-400/30 text-amber-400' : 'bg-amber-400/10 border border-amber-400/20 text-amber-400' }}">
                                @if(isset($data['type']) && str_contains($data['type'], 'message'))
                                    <i data-lucide="message-circle" class="w-3.5 h-3.5"></i>
                                @elseif(isset($data['type']) && str_contains($data['type'], 'response'))
                                    <i data-lucide="reply" class="w-3.5 h-3.5"></i>
                                @elseif(isset($data['type']) && str_contains($data['type'], 'status'))
                                    <i data-lucide="activity" class="w-3.5 h-3.5"></i>
                                @else
                                    <i data-lucide="bell" class="w-3.5 h-3.5"></i>
                                @endif
                            </div>
                            <div class="flex-1 min-w-0">
                                <div class="flex items-center justify-between gap-2">
                                    <p class="text-xs font-bold text-slate-100 {{ !$notification->read_at ? 'text-white' : '' }}">{{ $data['title'] ?? 'System Update' }}</p>
                                    <span class="text-[11px] text-slate-500">{{ $notification->created_at->diffForHumans() }}</span>
                                </div>
                                <p class="mt-0.5 text-[11px] text-slate-400 line-clamp-2 {{ !$notification->read_at ? 'text-slate-300 font-medium' : '' }}">{!! strip_tags($data['message'] ?? '') !!}</p>
                                @if(isset($data['batch_id']) && $data['batch_id'])
                                    <a href="{{ route('user.reports.show', $data['batch_id']) }}" class="inline-flex items-center gap-1 mt-1 text-[10px] text-amber-400 hover:text-amber-300 transition-colors">
                                        <i data-lucide="external-link" class="w-3 h-3"></i>
                                        View Case
                                    </a>
                                @endif
                            </div>
                            @if(!$notification->read_at)
                                <div class="h-2 w-2 rounded-full bg-amber-400 shrink-0 mt-1"></div>
                            @endif
                        </div>
                    @endforeach

                    @if (empty($doctorNotifications) || $doctorNotifications->isEmpty())
                        @if ($latestNotifications->isEmpty())
                            <p class="text-slate-500 text-xs py-4 text-center">No new notifications or messages.</p>
                        @endif
                    @else
                        @foreach ($doctorNotifications->take(2) as $doctor)
                            <a href="{{ route('user.chats.doctors.show', $doctor->id) }}"
                                class="flex items-start gap-4 rounded-xl border border-slate-700/70 bg-slate-900/70 px-3 py-2.5 hover:border-amber-400/80 hover:bg-slate-900/90 transition-all group">
                                <div class="h-8 w-8 flex items-center justify-center rounded-full bg-amber-400/20 text-[11px] font-semibold text-amber-200 group-hover:bg-amber-400/30">
                                    {{ strtoupper(substr($doctor->name, 0, 2)) }}
                                </div>
                                <div class="flex-1 min-w-0">
                                    <div class="flex items-center justify-between gap-2">
                                        <p class="text-sm font-semibold text-slate-100 truncate group-hover:text-amber-200">{{ $doctor->name }}</p>
                                        @if ($doctor->last_message_at)
                                            <span class="text-xs text-slate-400">
                                                {{ $doctor->last_message_at->diffForHumans() }}
                                            </span>
                                        @endif
                                    </div>
                                    <p class="mt-0.5 text-xs text-slate-400 truncate">
                                        {{ \Illuminate\Support\Str::limit($doctor->last_message, 80) }}
                                    </p>
                                </div>
                                <span class="ml-2 inline-flex items-center justify-center rounded-full bg-red-500 text-white text-[10px] leading-none px-1.5 py-0.5">1</span>
                            </a>
                        @endforeach
                    @endif
                </div>

                <div class="mt-4 pt-4 border-t border-slate-800/80">
                    <a href="{{ route('user.notifications.index') }}" 
                       class="flex items-center justify-center gap-2 w-full py-2 rounded-lg bg-white/5 border border-white/10 text-xs font-medium text-slate-100 hover:bg-white/10 transition-colors">
                        <i data-lucide="arrow-left" class="w-3.5 h-3.5"></i>
                        Go to Notifications Page
                    </a>
                </div>
            </div>

        </div>
    </div>
@endsection
