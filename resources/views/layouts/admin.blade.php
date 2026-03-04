<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>@yield('title', 'Admin Dashboard') | BoneHard</title>
    <link
        href="https://fonts.googleapis.com/css2?family=IBM+Plex+Sans:ital,wght@0,300;0,400;0,500;0,600;0,700&family=IBM+Plex+Sans+Arabic:wght@300;400;500;600;700&display=swap"
        rel="stylesheet">
    <link rel="icon" type="image/png" href="{{ asset('images/favicon.png') }}">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <style>
        body {
            font-family: "IBM Plex Sans", "IBM Plex Sans Arabic", system-ui, -apple-system, BlinkMacSystemFont, "Segoe UI", sans-serif;
            background-color: #060606;
            color: #f5f5f5;
        }

        :root {
            --bh-bg: #060606;
            --bh-surface: #0c0c0c;
            --bh-surface-2: #111111;
            --bh-border: rgba(255, 255, 255, 0.14);
            --bh-border-strong: rgba(255, 255, 255, 0.22);
            --bh-text: #f5f5f5;
            --bh-muted: rgba(245, 245, 245, 0.68);
            --bh-accent: rgba(255, 255, 255, 0.92);
            --bh-accent-soft: rgba(255, 255, 255, 0.12);
            --bh-accent-soft-hover: rgba(255, 255, 255, 0.18);
        }

        .bh-nav-link {
            position: relative;
        }

        .bh-nav-link-active {
            background: linear-gradient(90deg, rgba(255, 255, 255, 0.14), rgba(255, 255, 255, 0.06), transparent);
            color: #ffffff !important;
            border: 1px solid rgba(255, 255, 255, 0.18);
        }

        .bh-nav-link-active::before {
            content: '';
            position: absolute;
            left: 10px;
            top: 50%;
            transform: translateY(-50%);
            width: 6px;
            height: 6px;
            border-radius: 9999px;
            background: rgba(255, 255, 255, 0.9);
            box-shadow: 0 0 0 4px rgba(255, 255, 255, 0.08);
        }

        .bh-card {
            position: relative;
            border-radius: 1.5rem;
            border: 1px solid rgba(255, 255, 255, 0.14);
            background: linear-gradient(180deg, rgba(255,255,255,0.06), rgba(255,255,255,0.02));
            box-shadow:
                0 18px 55px rgba(0, 0, 0, 0.55),
                inset 0 1px 0 rgba(255, 255, 255, 0.06);
            transition:
                transform 160ms ease,
                border-color 160ms ease,
                box-shadow 160ms ease;
        }

        .bh-card::before {
            content: '';
            position: absolute;
            inset: 0;
            border-radius: inherit;
            pointer-events: none;
            background: radial-gradient(600px 220px at 20% 0%, rgba(255,255,255,0.12), transparent 60%);
            opacity: 0.8;
        }

        .bh-card:hover {
            transform: translateY(-2px);
            border-color: rgba(255, 255, 255, 0.24);
            box-shadow:
                0 26px 80px rgba(0, 0, 0, 0.65),
                inset 0 1px 0 rgba(255, 255, 255, 0.08);
        }

        .bh-card-icon {
            border-radius: 0.9rem;
            border: 1px solid rgba(255, 255, 255, 0.16);
            background: rgba(0, 0, 0, 0.35);
            color: rgba(255, 255, 255, 0.92);
            box-shadow: inset 0 1px 0 rgba(255, 255, 255, 0.06);
        }

        .bh-badge {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            text-align: center;
            border-radius: 9999px;
            padding: 0.25rem 1rem;
            font-size: 10px;
            line-height: 1.3;
            font-weight: 700;
            letter-spacing: 0.05em;
            text-transform: uppercase;
            border: 1px solid rgba(255, 255, 255, 0.14);
            white-space: nowrap;
        }

        .bh-badge--pending {
            color: #ffffff !important;
            background: rgba(245, 158, 11, 0.16) !important;
            border-color: rgba(245, 158, 11, 0.35) !important;
        }

        .bh-badge--reviewed {
            color: rgba(209, 250, 229, 0.95) !important;
            background: rgba(16, 185, 129, 0.14) !important;
            border-color: rgba(16, 185, 129, 0.30) !important;
        }

        .bh-btn {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            border-radius: 9999px;
            padding: 0.25rem 0.85rem;
            font-size: 10px;
            font-weight: 700;
            border: 1px solid rgba(255, 255, 255, 0.16);
            color: rgba(255, 255, 255, 0.9);
            background: rgba(0, 0, 0, 0.35);
            transition: background-color 160ms ease, border-color 160ms ease, transform 160ms ease;
        }

        .bh-btn:hover {
            background: rgba(255, 255, 255, 0.08);
            border-color: rgba(255, 255, 255, 0.28);
            transform: translateY(-1px);
        }

        .bh-btn--review {
            border-color: rgba(16, 185, 129, 0.30);
        }

        .bh-btn--unreview {
            border-color: rgba(245, 158, 11, 0.35);
        }

        [class*="hover:text-amber"]:hover { color: #ffffff !important; }
        [class*="hover:text-blue"]:hover { color: #ffffff !important; }
        [class*="hover:text-emerald"]:hover { color: #ffffff !important; }
        [class*="hover:text-red"]:hover { color: #ffffff !important; }

        [class*="hover:border-amber"]:hover { border-color: rgba(255, 255, 255, 0.6) !important; }
        [class*="hover:border-blue"]:hover { border-color: rgba(255, 255, 255, 0.6) !important; }
        [class*="hover:border-emerald"]:hover { border-color: rgba(255, 255, 255, 0.6) !important; }
        [class*="hover:border-red"]:hover { border-color: rgba(255, 255, 255, 0.6) !important; }

        [class*="hover:bg-amber"]:hover { background-color: var(--bh-accent-soft-hover) !important; }
        [class*="hover:bg-blue"]:hover { background-color: var(--bh-accent-soft-hover) !important; }
        [class*="hover:bg-emerald"]:hover { background-color: var(--bh-accent-soft-hover) !important; }
        [class*="hover:bg-red"]:hover { background-color: var(--bh-accent-soft-hover) !important; }

        [class*="bg-slate-"] { background-color: var(--bh-surface) !important; }
        [class*="bg-gray-"] { background-color: var(--bh-surface) !important; }
        [class*="text-slate-"] { color: var(--bh-text) !important; }
        [class*="text-gray-"] { color: var(--bh-text) !important; }
        [class*="border-slate-"] { border-color: var(--bh-border) !important; }
        [class*="border-gray-"] { border-color: var(--bh-border) !important; }

        @keyframes bhFadeIn {
            from { opacity: 0; transform: translateY(8px); }
            to { opacity: 1; transform: translateY(0); }
        }

        .bh-page-animate {
            animation: bhFadeIn 0.4s cubic-bezier(0.2, 0.8, 0.2, 1) forwards;
        }

        /* Sidebar Hover Dot */
        .bh-nav-link:hover::before,
        .bh-nav-link-active::before {
            content: '';
            position: absolute;
            left: 10px;
            top: 50%;
            transform: translateY(-50%);
            width: 4px;
            height: 4px;
            border-radius: 9999px;
            background: rgba(255, 255, 255, 0.9);
            box-shadow: 0 0 0 4px rgba(255, 255, 255, 0.08);
            z-index: 10;
        }

        .bh-nav-link {
            position: relative;
        }

        /* Pagination Styling */
        .pagination {
            display: flex !important;
            gap: 0.5rem !important;
            align-items: center !important;
            list-style: none !important;
            padding: 0 !important;
            margin: 2rem 0 !important;
            justify-content: center !important;
        }

        /* Target specific pagination links by their structure/content */
        .pagination a, 
        .pagination span {
            display: flex !important;
            align-items: center !important;
            justify-content: center !important;
            min-width: 38px !important;
            height: 38px !important;
            padding: 0 12px !important;
            border-radius: 12px !important;
            background: rgba(255, 255, 255, 0.03) !important;
            border: 1px solid rgba(255, 255, 255, 0.05) !important;
            color: #94a3b8 !important;
            font-size: 0.875rem !important;
            font-weight: 600 !important;
            transition: all 0.2s ease !important;
            text-decoration: none !important;
        }

        /* Force active state to be yellow */
        .pagination span[class*="bg-amber"],
        .pagination span[class*="bg-yellow"],
        .pagination .active span,
        .pagination span:not([href]) {
             background-color: #FACC15 !important;
             color: #000 !important;
             border-color: #FACC15 !important;
             box-shadow: 0 4px 12px rgba(250, 204, 21, 0.3) !important;
        }

        .pagination a:hover {
            background: rgba(250, 204, 21, 0.1) !important;
            border-color: rgba(250, 204, 21, 0.3) !important;
            color: #FACC15 !important;
            transform: translateY(-1px) !important;
        }

        .pagination .disabled span {
            opacity: 0.4 !important;
            cursor: not-allowed !important;
        }

        /* Black Table Styling */
        .bh-table-black {
            background-color: #000000 !important;
            border-radius: 1.25rem !important;
            border: 1px solid rgba(255, 255, 255, 0.08) !important;
            overflow: hidden !important;
        }

        .bh-table-black table {
            border-collapse: separate !important;
            border-spacing: 0 !important;
        }

        .bh-table-black tbody tr td {
            border-bottom: 1px solid rgba(255, 255, 255, 0.08) !important;
        }

        .bh-table-black tbody tr:last-child td {
            border-bottom: none !important;
        }

        .bh-table-black th {
            border: none !important;
        }

        .bh-table-black thead th {
            background-color: rgba(255, 255, 255, 0.02) !important;
            padding-top: 1rem !important;
            padding-bottom: 1rem !important;
        }

        .bh-table-black tbody tr:hover {
            background-color: rgba(255, 255, 255, 0.03) !important;
        }

        /* Premium Spinner */
        .premium-spinner {
            width: 24px;
            height: 24px;
            border: 3px solid rgba(0, 0, 0, 0.1);
            border-top: 3px solid currentColor;
            border-radius: 50%;
            animation: spin 0.8s linear infinite;
        }

        @keyframes spin {
            0% { transform: rotate(0deg); }
            100% { transform: rotate(360deg); }
        }

    </style>
</head>

<body class="min-h-screen text-slate-100" style="background: radial-gradient(1200px 600px at 18% 0%, rgba(255,255,255,0.06), transparent 60%), radial-gradient(900px 500px at 85% 20%, rgba(255,255,255,0.04), transparent 65%), var(--bh-bg);">
    <div class="min-h-screen flex">
        <div id="bh-admin-sidebar-overlay" class="hidden fixed inset-0 z-40 md:hidden" style="background: rgba(0,0,0,0.72);"></div>
        <aside id="bh-admin-sidebar" class="fixed inset-y-0 left-0 z-50 w-80 -translate-x-full md:translate-x-0 md:static md:z-auto md:flex flex-col border-r border-slate-800/80 bg-slate-950/95 transition-transform duration-200" style="background-color: var(--bh-surface-2); min-width: 320px !important; width: 320px !important;">
            <div class="h-16 flex items-center px-6 border-b border-slate-800/80 from-slate-950 via-slate-900 to-slate-950">
                <div class="flex items-center gap-3 w-full">
                    {{-- <div
                        class="h-10 w-10 rounded-2xl bg-slate-900/80 border border-amber-400/70 flex items-center justify-center overflow-hidden">
                        <img src="{{ asset('images/favicon.png') }}" alt="BoneHard Admin"
                            class="h-20 w-20 object-contain" />
                    </div> --}}
                    <div>
                        <p class="text-xl md:text-2xl font-semibold tracking-wide">BoneHard Admin</p>
                    </div>
                    <button id="bh-admin-sidebar-close" type="button" class="btn btn-black btn-sm ml-auto md:hidden">
                        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="h-5 w-5">
                            <path d="M18 6 6 18" />
                            <path d="M6 6l12 12" />
                        </svg>
                    </button>
                </div>
            </div>
            <nav class="flex-1 px-4 py-4 space-y-1 text-base md:text-lg">
                <a href="{{ route('admin.dashboard') }}"
                    class="bh-nav-link block rounded-lg px-3 py-2 pl-7 text-sm md:text-base font-medium {{ request()->routeIs('admin.dashboard') ? 'bh-nav-link-active' : 'text-slate-300 hover:bg-slate-900 hover:text-white border border-transparent' }}">Dashboard</a>
                <a href="{{ route('admin.cases.index') }}"
                    class="bh-nav-link block rounded-lg px-3 py-2 pl-7 text-sm md:text-base font-medium {{ request()->routeIs('admin.cases.*') ? 'bh-nav-link-active' : 'text-slate-300 hover:bg-slate-900 hover:text-white border border-transparent' }}">Cases</a>
                <a href="{{ route('admin.notifications.index') }}"
                    class="bh-nav-link block rounded-lg px-3 py-2 pl-7 text-sm md:text-base font-medium {{ request()->routeIs('admin.notifications.*') ? 'bh-nav-link-active' : 'text-slate-300 hover:bg-slate-900 hover:text-white border border-transparent' }}">Notifications</a>
                <a href="{{ route('admin.users.index') }}"
                    class="bh-nav-link block rounded-lg px-3 py-2 pl-7 text-sm md:text-base font-medium {{ request()->routeIs('admin.users.*') ? 'bh-nav-link-active' : 'text-slate-300 hover:bg-slate-900 hover:text-white border border-transparent' }}">Users</a>
                <a href="{{ route('admin.assistants.index') }}"
                    class="bh-nav-link block rounded-lg px-3 py-2 pl-7 text-sm md:text-base font-medium {{ request()->routeIs('admin.assistants.*') ? 'bh-nav-link-active' : 'text-slate-300 hover:bg-slate-900 hover:text-white border border-transparent' }}">Admin
                    Assistants</a>
                <a href="{{ route('admin.stats') }}"
                    class="bh-nav-link block rounded-lg px-3 py-2 pl-7 text-sm md:text-base font-medium {{ request()->routeIs('admin.stats') ? 'bh-nav-link-active' : 'text-slate-300 hover:bg-slate-900 hover:text-white border border-transparent' }}">Analytics</a>
                <div
                    class="pt-4 mt-4 border-t border-slate-800 text-[13px] font-semibold text-slate-400 uppercase tracking-[0.18em]">
                    Chats</div>
                <a href="{{ route('admin.chats.assistants') }}"
                    class="bh-nav-link block rounded-lg px-3 py-2 pl-7 {{ request()->routeIs('admin.chats.assistants*') ? 'bh-nav-link-active' : 'text-slate-300 hover:bg-slate-900 hover:text-white border border-transparent' }}">With
                    Assistants</a>
                <a href="{{ route('admin.chats.users') }}"
                    class="bh-nav-link block rounded-lg px-3 py-2 pl-7 {{ request()->routeIs('admin.chats.users*') ? 'bh-nav-link-active' : 'text-slate-300 hover:bg-slate-900 hover:text-white border border-transparent' }}">With
                    Users</a>
                <a href="{{ route('admin.chats.groups') }}"
                    class="bh-nav-link flex items-center justify-between gap-3 rounded-lg px-3 py-2 pl-7 {{ request()->routeIs('admin.chats.groups*') ? 'bh-nav-link-active' : 'text-slate-300 hover:bg-slate-900 hover:text-white border border-transparent' }}">
                    <span>User Group Chats</span>
                    @if (!empty($bhGroupChatHasNew))
                        <span class="inline-flex items-center justify-center rounded-full border border-white/20 bg-white/10 px-2 py-0.5 text-[10px] font-semibold text-white">New</span>
                    @endif
                </a>
            </nav>
        </aside>

        <div class="flex-1 flex flex-col">
            <header
                class="h-16 border-b border-slate-800/80 flex items-center justify-between px-4 md:px-8 bg-black/70 backdrop-blur-xl" style="background-color: rgba(12,12,12,0.78);">
                <div class="flex items-center gap-2 md:hidden">
                    <button id="bh-admin-sidebar-toggle" type="button" class="btn btn-black btn-sm">
                        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="h-5 w-5">
                            <path d="M4 6h16" />
                            <path d="M4 12h16" />
                            <path d="M4 18h16" />
                        </svg>
                    </button>
                    <div
                        class="h-8 w-8 rounded-xl bg-slate-900/80 border border-amber-400/70 flex items-center justify-center overflow-hidden">
                        <img src="{{ asset('images/favicon.png') }}" alt="BoneHard Admin"
                            class="h-7 w-7 object-contain" />
                    </div>
                    <span class="text-sm font-semibold">Admin</span>
                </div>
                <div class="hidden md:flex flex-1 items-center justify-between ml-4">
                    <h1 class="text-base md:text-lg font-semibold tracking-wide text-slate-200">@yield('header', 'Dashboard')</h1>
                </div>
                <div class="flex items-center gap-3 text-sm md:text-base ml-auto flex-nowrap whitespace-nowrap">
                    @auth
                        @php
                            $__unreadAdminMessages = 0;
                            $__currentAdmin = auth()->user();
                            $__adminConversations = \App\Models\Conversation::whereIn('type', ['admin_assistant', 'admin_user'])
                                ->where(function ($q) use ($__currentAdmin) {
                                    $q->where('admin_id', $__currentAdmin->id)
                                        ->orWhere('participant_id', $__currentAdmin->id);
                                })
                                ->with(['messages' => function ($q) {
                                    $q->latest()->limit(1);
                                }])
                                ->get();

                            $__unreadAdminMessages = $__adminConversations
                                ->filter(function ($conversation) use ($__currentAdmin) {
                                    $last = $conversation->messages->first();

                                    return $last && $last->sender_id !== $__currentAdmin->id;
                                })
                                ->count();
                        @endphp

                        @if ($__unreadAdminMessages > 0)
                            <a href="{{ route('admin.notifications.index') }}"
                                class="relative rounded-full border border-amber-400/70 px-3 py-1.5 text-xs md:text-sm font-semibold text-amber-300 hover:bg-amber-400/10 transition-colors shrink-0">
                                Messages
                                <span
                                    class="ml-2 inline-flex items-center justify-center rounded-full bg-red-500 text-white text-[10px] leading-none px-1.5 py-0.5">
                                    {{ $__unreadAdminMessages }}
                                </span>
                            </a>
                        @endif

                        <span class="hidden sm:inline text-slate-300 text-sm font-medium mr-2">{{ auth()->user()->name }}</span>
                        <a href="{{ url('/') }}" target="_blank"
                            class="rounded-lg border border-white/20 bg-white/5 px-4 py-2 text-xs font-semibold text-white hover:bg-white/10 hover:border-white/40 transition-all outline-none shrink-0 flex items-center justify-center">Back
                            to site</a>
                        <form action="{{ route('logout') }}" method="POST" class="m-0 p-0 inline-flex shrink-0">
                            @csrf
                            <button type="submit"
                                class="rounded-lg border border-white/20 bg-white/5 px-4 py-2 text-xs font-semibold text-white hover:bg-white/10 hover:border-white/40 transition-all outline-none flex items-center justify-center">Logout</button>
                        </form>
                    @endauth
                </div>
            </header>

            <main class="flex-1 p-4 md:p-8 bh-page-animate" style="background: transparent;">
                @if (session('status'))
                    <div
                        class="mb-4 rounded-lg border border-emerald-500/40 bg-emerald-500/10 px-4 py-3 text-xs text-emerald-200">
                        {{ session('status') }}
                    </div>
                @endif

                @yield('content')
            </main>
        </div>
    </div>

    <!-- Premium File Preview Modal -->
    <div id="bh-preview-modal" class="fixed inset-0 z-[100] hidden items-center justify-center p-4">
        <div id="bh-modal-backdrop" class="absolute inset-0 bg-black/80 backdrop-blur-md opacity-0 transition-opacity duration-500"></div>
        <div id="bh-modal-container" class="relative w-full flex flex-col bg-[#0c0c0c] rounded-3xl border border-white/10 shadow-2xl overflow-hidden pointer-events-none scale-95 opacity-0 transition-all duration-500 ease-out" style="max-width: 1000px; height: 85vh;">
            <div class="flex items-center justify-between p-5 border-b border-white/5 shrink-0 bg-black/40">
                <div class="flex items-center gap-4 min-w-0">
                    <div class="h-11 w-11 shrink-0 rounded-2xl bg-white/5 border border-white/10 flex items-center justify-center text-amber-500 shadow-inner">
                        <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-file-text"><path d="M15 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V7Z"/><path d="M14 2v4a2 2 0 0 0 2 2h4"/><path d="M10 9H8"/><path d="M16 13H8"/><path d="M16 17H8"/></svg>
                    </div>
                    <div class="min-w-0">
                        <h3 id="bh-modal-title" class="text-sm font-bold text-white truncate uppercase tracking-widest leading-tight">File Preview</h3>
                        <p id="bh-modal-meta" class="text-[11px] text-slate-400 truncate mt-1"></p>
                    </div>
                </div>
                <div class="flex items-center gap-3 ml-4">
                    <a id="bh-modal-download" href="#" class="inline-flex items-center gap-2.5 px-4 py-2 rounded-xl bg-white text-[12px] font-bold text-black hover:bg-amber-400 transition-all duration-300 shadow-lg hover:shadow-amber-400/20 active:scale-95">
                        <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-download text-black"><path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"/><polyline points="7 10 12 15 17 10"/><line x1="12" x2="12" y1="15" y2="3"/></svg>
                        Download
                    </a>
                    <button id="bh-modal-close" class="h-10 w-10 flex items-center justify-center text-slate-400 hover:text-white hover:bg-white/10 rounded-xl transition-all duration-200">
                        <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-x"><path d="M18 6 6 18"/><path d="M6 6l12 12"/></svg>
                    </button>
                </div>
            </div>
            <div id="bh-modal-content" class="flex-1 min-h-0 overflow-auto bg-black/40 flex items-center justify-center">
                <!-- Content will be injected here -->
            </div>
            <div id="bh-modal-footer" class="p-4 bg-white/5 border-t border-white/5 shrink-0">
                <p id="bh-modal-description" class="text-xs text-slate-400 leading-relaxed"></p>
            </div>
        </div>
    </div>
    <script>
        (function () {
            var sidebar = document.getElementById('bh-admin-sidebar');
            var overlay = document.getElementById('bh-admin-sidebar-overlay');
            var toggleBtn = document.getElementById('bh-admin-sidebar-toggle');
            var closeBtn = document.getElementById('bh-admin-sidebar-close');

            if (!sidebar || !overlay || !toggleBtn || !closeBtn) return;

            function openSidebar() {
                sidebar.classList.remove('-translate-x-full');
                sidebar.classList.add('translate-x-0');
                overlay.classList.remove('hidden');
                document.documentElement.classList.add('overflow-hidden');
            }

            function closeSidebar() {
                sidebar.classList.add('-translate-x-full');
                sidebar.classList.remove('translate-x-0');
                overlay.classList.add('hidden');
                document.documentElement.classList.remove('overflow-hidden');
            }

            toggleBtn.addEventListener('click', openSidebar);
            closeBtn.addEventListener('click', closeSidebar);
            overlay.addEventListener('click', closeSidebar);
            window.addEventListener('keydown', function (e) {
                if (e.key === 'Escape') closeSidebar();
            });

            sidebar.querySelectorAll('a').forEach(function (a) {
                a.addEventListener('click', closeSidebar);
            });

            window.addEventListener('resize', function () {
                if (window.innerWidth >= 768) closeSidebar();
            });

            // Global Modal Logic
            const modal = document.getElementById('bh-preview-modal');
            const backdrop = document.getElementById('bh-modal-backdrop');
            const container = document.getElementById('bh-modal-container');
            const modalCloseBtn = document.getElementById('bh-modal-close');
            const content = document.getElementById('bh-modal-content');
            const title = document.getElementById('bh-modal-title');
            const meta = document.getElementById('bh-modal-meta');
            const desc = document.getElementById('bh-modal-description');
            const download = document.getElementById('bh-modal-download');

            if (modal) {
                window.openBHPreview = function(data) {
                    content.innerHTML = '<div class="animate-pulse text-slate-500 text-xs text-center">Loading...</div>';
                    title.textContent = data.title || 'File Preview';
                    meta.textContent = (data.name ? data.name + ' • ' : '') + (data.created || '');
                    desc.textContent = data.description || '';
                    
                    if (data.downloadUrl) {
                        download.href = data.downloadUrl;
                        download.style.display = 'inline-flex';
                    } else {
                        download.style.display = 'none';
                    }

                    modal.classList.remove('hidden');
                    modal.classList.add('flex');
                    
                    setTimeout(() => {
                        backdrop.classList.add('opacity-100');
                        container.classList.remove('scale-95', 'opacity-0', 'pointer-events-none');
                        container.classList.add('scale-100', 'opacity-100', 'pointer-events-auto');
                    }, 10);

                    const url = data.url;
                    const mime = data.mime || '';
                    const lowerUrl = url.toLowerCase();

                    content.innerHTML = '';
                    if (mime.startsWith('image/') || /\.(png|jpe?g|gif|webp)$/i.test(lowerUrl)) {
                        const img = document.createElement('img');
                        img.src = url;
                        img.className = 'w-full h-full object-contain';
                        content.appendChild(img);
                    } else if (mime === 'application/pdf' || lowerUrl.endsWith('.pdf')) {
                        const embed = document.createElement('embed');
                        embed.src = url;
                        embed.type = 'application/pdf';
                        embed.className = 'w-full h-full';
                        content.appendChild(embed);
                    } else {
                        content.innerHTML = `
                            <div class="text-center p-8">
                                <svg xmlns="http://www.w3.org/2000/svg" width="48" height="48" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="text-slate-500 mx-auto mb-4 lucide lucide-file-warning"><path d="M15 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V7Z"/><path d="M14 2v4a2 2 0 0 0 2 2h4"/><path d="m9 15 2 2 4-4"/></svg>
                                <p class="text-sm text-slate-400">Preview not available for this file type.</p>
                                <a href="${data.downloadUrl || url}" class="inline-flex items-center gap-2 px-4 py-2 mt-4 rounded-lg bg-amber-400 text-black font-bold text-sm hover:bg-amber-500 transition-colors">Download to View</a>
                            </div>
                        `;
                    }
                };

                function closeModal() {
                    backdrop.classList.remove('opacity-100');
                    container.classList.add('scale-95', 'opacity-0', 'pointer-events-none');
                    container.classList.remove('scale-100', 'opacity-100', 'pointer-events-auto');
                    setTimeout(() => {
                        modal.classList.add('hidden');
                        modal.classList.remove('flex');
                        content.innerHTML = '';
                    }, 500);
                }

                modalCloseBtn.addEventListener('click', closeModal);
                backdrop.addEventListener('click', closeModal);
                window.addEventListener('keydown', (e) => {
                    if (e.key === 'Escape' && !modal.classList.contains('hidden')) closeModal();
                });
            }
        })();
    </script>
    @if (session('welcome'))
        <div id="bh-welcome-toast" class="fixed top-5 right-5 z-[9999] w-[min(92vw,360px)]">
            <div class="rounded-2xl border border-amber-400/35 bg-black/80 backdrop-blur-xl px-4 py-3 shadow-2xl">
                <div class="flex items-start gap-3">
                    <div class="mt-0.5 h-9 w-9 rounded-xl bg-amber-400/15 border border-amber-400/35 flex items-center justify-center text-amber-200 font-bold text-sm">BH</div>
                    <div class="flex-1 min-w-0">
                        <p class="text-[11px] uppercase tracking-[0.18em] text-slate-400">Welcome</p>
                        <p class="mt-0.5 text-sm font-semibold text-slate-100">{{ session('welcome') }}</p>
                    </div>
                    <button type="button" id="bh-welcome-toast-close" class="text-slate-400 hover:text-amber-200 text-xs">✕</button>
                </div>
                <div class="mt-3 h-1.5 w-full rounded-full bg-white/10 overflow-hidden">
                    <div id="bh-welcome-toast-bar" class="h-full w-full bg-amber-400/70"></div>
                </div>
            </div>
        </div>
        <script>
            (function () {
                var toast = document.getElementById('bh-welcome-toast');
                var closeBtn = document.getElementById('bh-welcome-toast-close');
                var bar = document.getElementById('bh-welcome-toast-bar');
                if (!toast || !closeBtn || !bar) return;

                var duration = 4500;
                var started = Date.now();
                var raf;

                function tick() {
                    var elapsed = Date.now() - started;
                    var left = Math.max(0, 1 - (elapsed / duration));
                    bar.style.width = (left * 100) + '%';
                    if (elapsed >= duration) {
                        toast.remove();
                        return;
                    }
                    raf = requestAnimationFrame(tick);
                }

                function close() {
                    if (raf) cancelAnimationFrame(raf);
                    toast.remove();
                }

                closeBtn.addEventListener('click', close);
                tick();
            })();
        </script>
    @endif
    <script>
        // Loading overlays and button spinners are intentionally disabled
        // to keep navigation instant and avoid visual delays.
        (function () {
            document.addEventListener('submit', function () {
                // no-op
            }, true);

            document.addEventListener('click', function () {
                // no-op
            }, true);
        })();
    </script>
    <script>
        function showToast(title, message, type = 'success') {
            // If only one argument is provided, treat it as message
            if (arguments.length === 1) {
                message = title;
                title = type.toUpperCase();
            }
            if (arguments.length === 2 && (message === 'success' || message === 'error' || message === 'info' || message === 'warning')) {
                type = message;
                message = title;
                title = type.toUpperCase();
            }

            const toastId = 'toast-' + Date.now();
            const colors = {
                success: 'border-emerald-500/35 bg-black/80 text-emerald-200',
                error: 'border-red-500/35 bg-black/80 text-red-200',
                info: 'border-sky-500/35 bg-black/80 text-sky-200',
                warning: 'border-amber-500/35 bg-black/80 text-amber-200'
            };

            const toastHtml = `
                <div id="${toastId}" class="fixed top-5 right-5 z-[9999] w-[min(92vw,360px)] bh-page-animate">
                    <div class="rounded-2xl border ${colors[type] || colors.success} backdrop-blur-xl px-4 py-3 shadow-2xl">
                        <div class="flex items-start gap-3">
                            <div class="mt-0.5 h-9 w-9 rounded-xl bg-white/5 border border-white/10 flex items-center justify-center font-bold text-sm">
                                ${type === 'success' ? '✓' : 'ℹ'}
                            </div>
                            <div class="flex-1 min-w-0">
                                <p class="text-[11px] uppercase tracking-[0.18em] opacity-60">${title}</p>
                                <p class="mt-0.5 text-sm font-semibold text-slate-100">${message}</p>
                            </div>
                            <button type="button" onclick="document.getElementById('${toastId}').remove()" class="text-slate-400 hover:text-white text-xs">✕</button>
                        </div>
                        <div class="mt-3 h-1 w-full rounded-full bg-white/10 overflow-hidden">
                            <div id="${toastId}-bar" class="h-full w-full ${type === 'success' ? 'bg-emerald-500/70' : 'bg-amber-500/70'}"></div>
                        </div>
                    </div>
                </div>
            `;
            document.body.insertAdjacentHTML('beforeend', toastHtml);

            const toastEl = document.getElementById(toastId);
            const barEl = document.getElementById(`${toastId}-bar`);
            const duration = 4000;
            const start = Date.now();

            function frame() {
                const elapsed = Date.now() - start;
                const progress = Math.max(0, 1 - (elapsed / duration));
                if (barEl) barEl.style.width = (progress * 100) + '%';
                if (elapsed >= duration) {
                    if (toastEl) toastEl.remove();
                    return;
                }
                requestAnimationFrame(frame);
            }
            requestAnimationFrame(frame);
        }
    </script>
    @stack('scripts')
</body>

</html>
