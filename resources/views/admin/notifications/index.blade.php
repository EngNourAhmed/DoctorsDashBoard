@extends('layouts.admin')

@section('title', 'Notifications')

@section('content')
    <div class="space-y-6">
        <div class="flex items-center justify-between">
            <h1 class="text-xl font-semibold text-slate-100">Notifications</h1>
        </div>

        <div class="grid gap-6 md:grid-cols-2">
            <div class="rounded-2xl bg-slate-900/80 border border-slate-700/70 p-4">
                <h2 class="text-sm font-semibold text-slate-200 mb-3">Messages from users</h2>

                @forelse ($userNotifications as $user)
                    <a href="{{ route('admin.chats.users.show', $user->id) }}"
                        class="flex items-start gap-3 rounded-xl border border-slate-700/70 bg-slate-900/70 px-3 py-2 mb-2 hover:border-amber-400/80 hover:bg-slate-900 transition-colors">
                        <div
                            class="h-8 w-8 flex items-center justify-center rounded-full bg-amber-400/20 text-[11px] font-semibold text-amber-200">
                            {{ strtoupper(substr($user->name, 0, 2)) }}
                        </div>
                        <div class="flex-1 min-w-0">
                            <div class="flex items-center justify-between gap-2">
                                <p class="text-xs font-semibold text-slate-100 truncate">{{ $user->name }}</p>
                                @if ($user->last_message_at)
                                    <span class="text-[10px] text-slate-400">
                                        {{ $user->last_message_at->format('g:i A') }}
                                    </span>
                                @endif
                            </div>
                            <p class="mt-0.5 text-[11px] text-slate-400 truncate">
                                {{ \Illuminate\Support\Str::limit($user->last_message, 80) }}
                            </p>
                        </div>
                        <span
                            class="ml-2 inline-flex items-center justify-center rounded-full bg-red-500 text-white text-[10px] leading-none px-1.5 py-0.5">
                            1
                        </span>
                    </a>
                @empty
                    <p class="text-xs text-slate-500">No new messages from users.</p>
                @endforelse
            </div>

            <div class="rounded-2xl bg-slate-900/80 border border-slate-700/70 p-4">
                <h2 class="text-sm font-semibold text-slate-200 mb-3">Messages from staff</h2>

                @forelse ($staffNotifications as $staff)
                    <a href="{{ route('admin.chats.assistants.show', $staff->id) }}"
                        class="flex items-start gap-3 rounded-xl border border-slate-700/70 bg-slate-900/70 px-3 py-2 mb-2 hover:border-amber-400/80 hover:bg-slate-900 transition-colors">
                        <div
                            class="h-8 w-8 flex items-center justify-center rounded-full bg-sky-500/20 text-[11px] font-semibold text-sky-200">
                            {{ strtoupper(substr($staff->name, 0, 2)) }}
                        </div>
                        <div class="flex-1 min-w-0">
                            <div class="flex items-center justify-between gap-2">
                                <p class="text-xs font-semibold text-slate-100 truncate">{{ $staff->name }}</p>
                                @if ($staff->last_message_at)
                                    <span class="text-[10px] text-slate-400">
                                        {{ $staff->last_message_at->format('g:i A') }}
                                    </span>
                                @endif
                            </div>
                            <p class="mt-0.5 text-[11px] text-slate-400 truncate">
                                {{ \Illuminate\Support\Str::limit($staff->last_message, 80) }}
                            </p>
                        </div>
                        <span
                            class="ml-2 inline-flex items-center justify-center rounded-full bg-red-500 text-white text-[10px] leading-none px-1.5 py-0.5">
                            1
                        </span>
                    </a>
                @empty
                    <p class="text-xs text-slate-500">No new messages from assistants or admins.</p>
                @endforelse
            </div>
        </div>

        <div class="rounded-2xl bg-slate-900/80 border border-slate-700/70 p-4 mt-6">
            <h2 class="text-sm font-semibold text-slate-200 mb-3">System Activity</h2>

            @forelse ($databaseNotifications as $notification)
                @php $data = $notification->data; @endphp
                <div class="flex items-start gap-3 rounded-xl border border-slate-700/70 bg-slate-900/70 px-4 py-3 mb-2">
                    <div class="h-8 w-8 flex items-center justify-center rounded-full {{ ($data['updated_by_role'] ?? '') === 'admin' ? 'bg-red-500/20 text-red-200' : 'bg-sky-500/20 text-sky-200' }} text-[11px] font-bold shrink-0">
                        {{ strtoupper(substr($data['updated_by_name'] ?? 'SY', 0, 2)) }}
                    </div>
                    <div class="flex-1 min-w-0">
                        <div class="flex items-center justify-between gap-2">
                            <p class="text-xs font-semibold text-slate-100">
                                {{ $data['updated_by_name'] ?? 'System' }} 
                                <span class="font-normal text-slate-400">changed status of</span> 
                                Case #{{ $data['report_id'] ?? '?' }}
                            </p>
                            <span class="text-[10px] text-slate-400">
                                {{ $notification->created_at->diffForHumans() }}
                            </span>
                        </div>
                        <div class="mt-1 flex items-center gap-2 flex-wrap text-[11px]">
                            <span class="px-2 py-0.5 rounded-full border border-white/10 bg-white/5 text-slate-400 line-through">{{ $data['old_status'] ?? 'N/A' }}</span>
                            <svg class="h-3 w-3 text-slate-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3"></path></svg>
                            <span class="px-2 py-0.5 rounded-full {{ \App\Models\Report::STATUSES[$data['new_status']] ?? 'border-white/10 text-white bg-white/5' }}">{{ $data['new_status'] ?? 'N/A' }}</span>
                        </div>
                    </div>
                </div>
            @empty
                <p class="text-xs text-slate-500">No recent activity found.</p>
            @endforelse
        </div>
    </div>
@endsection
