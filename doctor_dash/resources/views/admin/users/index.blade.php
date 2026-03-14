@extends('layouts.admin')

@section('title', 'All Users')
@section('header', 'All Users')

@section('content')
    <div class="space-y-6">
        <div class="flex flex-col md:flex-row md:items-end justify-between gap-6 mb-2 px-2">
            <div>
                <h2 class="text-2xl font-bold text-white tracking-tight">Total Users</h2>
                <p class="text-sm text-gray-400 mt-1">Manage roles, cases, and filter active members across the platform.</p>
            </div>
        </div>

        <div class="mb-4"></div>

        <div class="bh-table-transparent rounded-xl border border-white/10 bg-[#0c0c0c]">
            <div class="w-full overflow-x-auto">
                <table class="w-full text-left text-sm text-gray-300 table-users overflow-visible border-collapse">
                    <thead class="sticky top-0 z-20 shadow-md">
                        <tr class="border-b border-white/10">
                            <th class="px-4 py-3 font-bold text-gray-400 uppercase tracking-widest text-[10px]">Name</th>
                            <th class="px-4 py-3 font-bold text-gray-400 uppercase tracking-widest text-[10px]">Email</th>
                            <th class="px-4 py-3 font-bold text-gray-400 uppercase tracking-widest text-[10px]">Address</th>
                            <th class="px-4 py-3 font-bold text-gray-400 uppercase tracking-widest text-[10px]">Phone</th>
                            <th class="px-4 py-3 font-bold text-gray-400 uppercase tracking-widest text-[10px]">Role</th>
                            <th class="px-4 py-3 font-bold text-gray-400 uppercase tracking-widest text-[10px] text-center">Case Status (Latest)</th>
                            <th class="px-4 py-3 font-bold text-gray-400 uppercase tracking-widest text-[10px] text-center">Case Actions</th>
                        </tr>
                    </thead>
                    <tbody class="divide-none">
                        @foreach ($users as $user)
                            <tr class="transition-colors group hover:bg-white/[0.03]">
                                <td class="px-4 py-2.5 text-[#FACC15] font-bold search-target text-[13px] border-b border-white/10">
                                    <a href="{{ route('admin.users.reports', $user) }}" class="hover:underline">
                                        {{ $user->name }}
                                    </a>
                                </td>
                                <td class="px-4 py-2.5 text-gray-300 search-target text-[13px] border-b border-white/10">{{ $user->email }}</td>
                                <td class="px-4 py-2.5 text-gray-400 text-[13px] border-b border-white/10">{{ $user->address ?? '-' }}</td>
                                <td class="px-4 py-2.5 text-gray-400 search-target text-[13px] border-b border-white/10">{{ $user->phone ?? '-' }}</td>
                                <td class="px-4 py-2.5 border-b border-white/10">
                                    @if (auth()->id() === $user->id)
                                        <span class="inline-flex items-center rounded-md border border-white/10 bg-black/40 px-3 py-1 text-[10px] font-bold uppercase tracking-widest text-white">
                                            Admin (you)
                                        </span>
                                    @else
                                        <form method="POST" action="{{ route('admin.users.updateRole', $user) }}" class="m-0">
                                            @csrf
                                            @method('PATCH')
                                            <div class="relative inline-block">
                                                <select name="role" onchange="this.form.submit()"
                                                    class="appearance-none rounded-lg border border-white/10 bg-[#0c0c0c] pl-3 pr-8 py-1.5 text-[10px] font-bold uppercase tracking-widest text-white focus:border-[#FACC15] outline-none transition-all cursor-pointer hover:border-white/20">
                                                    <option value="user" @selected($user->role === 'user')>User</option>
                                                    <option value="assistant" @selected($user->role === 'assistant')>Assistant</option>
                                                    <option value="admin" @selected($user->role === 'admin')>Admin</option>
                                                </select>
                                                <div class="pointer-events-none absolute inset-y-0 right-0 flex items-center px-2 text-gray-500">
                                                    <svg class="h-3 w-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path></svg>
                                                </div>
                                            </div>
                                        </form>
                                    @endif
                                </td>
                                <td class="px-4 py-2.5 text-center border-b border-white/10">
                                    @if($user->latestReport)
                                        <span id="badge-{{ $user->latestReport->id }}" class="bh-badge scale-90 {{ \App\Models\Report::STATUSES[$user->latestReport->status] ?? '' }}">
                                            {{ $user->latestReport->status }}
                                        </span>
                                    @else
                                        <span class="text-gray-600"> No Cases</span>

                                    @endif
                                </td>
                                <td class="px-4 py-2.5 text-center border-b border-white/10">
                                    @if($user->latestReport)
                                        <div class="relative inline-block w-full max-w-[140px] scale-90">
                                            <select onchange="updateReportStatus(this, {{ $user->latestReport->id }})" class="bh-badge w-full appearance-none pr-6 cursor-pointer outline-none hover:opacity-80 transition-opacity {{ \App\Models\Report::STATUSES[$user->latestReport->status] ?? 'border-white/10 bg-[#0c0c0c] text-white' }}">
                                                @foreach(\App\Models\Report::STATUSES as $statusName => $statusClass)
                                                    <option value="{{ $statusName }}" class="bg-[#0c0c0c] text-white tracking-normal font-medium normal-case" @selected($user->latestReport->status === $statusName)>{{ $statusName }}</option>
                                                @endforeach
                                            </select>
                                            <div class="pointer-events-none absolute inset-y-0 right-0 flex items-center px-2 text-current opacity-70">
                                                <svg class="h-3 w-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path></svg>
                                            </div>
                                        </div>
                                    @else
                                        <span class="text-gray-600"> No Cases</span>
                                    @endif
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
               
            <div class="pt-3 px-6 pb-6">
                {{ $users->links('vendor.pagination.custom') }}
             </div>
        </div>
    </div>

    <script>
        (function () {
            const input = document.getElementById('usersSearch');
            const table = document.querySelector('.table-users');
            if (!input || !table) return;

            const rows = Array.from(table.querySelectorAll('tbody tr'));

            function normalize(value) {
                return (value || '').toLowerCase().replace(/\s+/g, ' ').trim();
            }

            function applyFilter() {
                const q = normalize(input.value);
                rows.forEach((row) => {
                    const targets = row.querySelectorAll('.search-target');
                    let text = '';
                    targets.forEach(t => { text += ' ' + t.innerText; });
                    text = normalize(text);
                    
                    row.style.display = !q || text.includes(q) ? '' : 'none';
                });
            }

            input.addEventListener('input', applyFilter);
        })();

        async function updateReportStatus(selectEl, reportId) {
            const newStatus = selectEl.value;
            const originalValue = selectEl.getAttribute('data-original-value') || selectEl.querySelector('option[selected]')?.value;
            
            selectEl.disabled = true;

            try {
                const response = await fetch(`/admin/cases/${reportId}/status`, {
                    method: 'PATCH',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': '{{ csrf_token() }}'
                    },
                    body: JSON.stringify({ status: newStatus })
                });

                if (response.ok) {
                    const data = await response.json();
                    
                    const badge = document.getElementById(`badge-${reportId}`);
                    if (badge && data.class) {
                        badge.className = `bh-badge ${data.class}`;
                        badge.innerText = newStatus;
                    }
                    
                    if (data.class) {
                        const defaultClasses = 'bh-badge w-full appearance-none pr-6 cursor-pointer outline-none hover:opacity-80 transition-opacity';
                        selectEl.className = `${defaultClasses} ${data.class}`;
                    }
                    
                    selectEl.setAttribute('data-original-value', newStatus);

                    if (window.showToast) {
                        window.showToast('SUCCESS', 'User has been updated successfully', 'success');
                    }
                } else {
                    alert('Failed to update status');
                    selectEl.value = originalValue; 
                }
            } catch (error) {
                console.error(error);
                alert('An error occurred');
                selectEl.value = originalValue;
            } finally {
                selectEl.disabled = false;
            }
        }
    </script>
@endsection
