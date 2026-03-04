@extends('layouts.admin')

@section('title', 'All Cases')
@section('header', 'System Cases')

@section('content')
    <div class="space-y-6 px-2">
        <div class="flex flex-col md:flex-row md:items-end justify-between gap-6 mb-2">
            <div>
                <h2 class="text-3xl font-bold text-white tracking-tight">Cases</h2>
                <p class="text-sm text-gray-400 mt-1">Track and manage your uploaded case collections</p>
            </div>
            
            <div class="flex flex-col sm:flex-row items-center gap-4 w-full md:w-auto">
                <form method="GET" action="{{ route('admin.cases.index') }}" class="flex items-center gap-3 w-full sm:w-auto">
                    <div class="relative w-full sm:w-64">
                        <select name="status" onchange="this.form.submit()"
                            class="w-full rounded-lg border border-white/10 bg-[#0c0c0c] px-4 py-2.5 text-sm text-white focus:border-[#FACC15] outline-none transition-all cursor-pointer appearance-none pr-10">
                            <option value="" @selected(empty($statusFilter))>All Cases</option>
                            @foreach($statuses as $name => $classes)
                                <option value="{{ $name }}" @selected(($statusFilter ?? null) === $name)>{{ $name }}</option>
                            @endforeach
                        </select>
                        <div class="pointer-events-none absolute inset-y-0 right-0 flex items-center px-3 text-gray-500">
                            <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path></svg>
                        </div>
                    </div>
                </form>
            </div>
        </div>

        <div class="mb-4"></div>

        <div class="bh-table-transparent rounded-xl border border-white/10 bg-[#0c0c0c]">
            <!-- Table -->
            @if ($reports->isEmpty())
                <div class="py-20 text-center">
                    <p class="text-gray-500 text-sm">No cases found in the system matching your criteria.</p>
                </div>
            @else
                <div class="overflow-y-visible pb-4">
                    <table class="w-full text-left text-gray-300 whitespace-nowrap relative border-collapse">
                        <thead class="sticky top-0 z-20 shadow-md">
                            <tr class="border-b border-white/10">
                                <th class="px-6 py-4 font-bold text-gray-400 uppercase tracking-widest text-[10px]">Patient Name</th>
                                <th class="px-6 py-4 font-bold text-gray-400 uppercase tracking-widest text-[10px]">Updated By</th>
                                <th class="px-6 py-4 font-bold text-gray-400 uppercase tracking-widest text-[10px] text-center">Status</th>
                                <th class="px-6 py-4 font-bold text-gray-400 uppercase tracking-widest text-[10px]">Files</th>
                                <th class="px-6 py-4 font-bold text-gray-400 uppercase tracking-widest text-[10px] text-center">Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($reports as $report)
                                <tr class="transition-colors group hover:bg-white/[0.03]">
                                    <td class="px-6 py-5 border-b border-white/10">
                                        <div class="text-[#FACC15] font-bold text-sm tracking-tight">
                                            {{ $report->title }}
                                        </div>
                                    </td>
                                    <td class="px-6 py-5 border-b border-white/10">
                                        @if($report->updatedBy)
                                            <div class="flex flex-col">
                                                <span class="text-white text-[15px]">{{ $report->updatedBy->name }}</span>
                                                <span class="text-[10px] text-[#FACC15] uppercase font-bold tracking-widest font-black mt-1">
                                                    {{ str_replace('_', ' ', $report->updatedBy->role) }}
                                                </span>
                                            </div>
                                        @else
                                            <span class="text-gray-500 text-[15px]">System</span>
                                        @endif
                                    </td>
                                    <td class="px-6 py-5 text-center border-b border-white/10">
                                        <div class="bh-badge {{ \App\Models\Report::STATUSES[$report->status] ?? '' }}">
                                            {{ $report->status }}
                                        </div>
                                    </td>
                                    <td class="px-6 py-5 border-b border-white/10">
                                        @if ($report->files_count > 1)
                                            <div class="flex flex-col gap-1">
                                                <div class="flex items-center gap-2 text-white/90 text-[15px] tracking-wider">
                                                    <svg class="h-5 w-5 text-white/60" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 7v10a2 2 0 002 2h14a2 2 0 002-2V9a2 2 0 00-2-2h-6l-2-2H5a2 2 0 00-2 2z" /></svg>
                                                        collection
                                                </div>
                                                <a href="{{ route('admin.cases.batch', $report->batch_id) }}" class="text-[#FACC15] underline text-[11px] font-black ml-5">
                                                    View Collection ({{ $report->files_count }})
                                                </a>
                                            </div>
                                        @else
                                            <div class="flex flex-col gap-1">
                                                <div class="flex items-center gap-2 text-white/90 text-[14px] tracking-wider">
                                                    <svg class="h-5 w-5 text-white/60" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" /></svg>
                                                    Single File
                                                </div>
                                                <button type="button" 
                                                    onclick="window.openBHPreview({
                                                        url: '{{ route('admin.cases.preview', $report) }}',
                                                        downloadUrl: '{{ route('admin.cases.download', $report) }}',
                                                        mime: '{{ $report->mime_type }}',
                                                        title: '{{ addslashes($report->title) }}',
                                                        name: '{{ addslashes($report->original_name) }}',
                                                        created: '{{ $report->created_at->format('Y-m-d H:i') }}'
                                                    })" 
                                                    class="text-[#FACC15] underline text-[11px] font-black ml-5 text-left">
                                                    View File
                                                </button>
                                            </div>
                                        @endif
                                    </td>
                                        <td class="px-6 py-5 text-center static border-b border-white/10">
                                            <div class="relative inline-block w-[250px] scale-90 text-left dropdown-container">
                                                <button type="button" onclick="toggleDropdown(this)" 
                                                    class="w-full flex items-center justify-between gap-2 rounded-lg border border-white/10 bg-[#0c0c0c] px-4 py-2 text-[10px] font-bold uppercase tracking-widest text-white hover:border-[#FACC15] transition-all dropdown-btn">
                                                    <span>Actions</span>
                                                    <svg class="h-3 w-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path></svg>
                                                </button>
                                                <div class="hidden dropdown-menu absolute right-0 top-full z-[9999] mt-2 w-65 flex flex-col rounded-xl border border-white/10 bg-[#0c0c0c] shadow-2xl backdrop-blur-xl max-h-[250px] overflow-y-auto overflow-x-hidden origin-top-right">
                                                    @foreach(\App\Models\Report::STATUSES as $statusName => $statusClass)
                                                        <button onclick="updateReportStatusManually({{ $report->id }}, '{{ $statusName }}', this)" 
                                                            class="w-full px-5 py-2.5 text-left group/item transition-colors hover:bg-white/5">
                                                            <span class="text-xs font-semibold text-gray-300 group-hover/item:text-white uppercase tracking-wider">
                                                                {{ $statusName }}
                                                            </span>
                                                        </button>
                                                    @endforeach
                                                </div>
                                            </div>
                                        </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>

            <div class="pt-3 px-6 pb-6">
                    {{ $reports->links('vendor.pagination.custom') }}
                </div>
            @endif
        </div>
    </div>

    <script>
        function toggleDropdown(btn) {
            const menu = btn.nextElementSibling;
            const isHidden = menu.classList.contains('hidden');
            const container = btn.closest('.dropdown-container');
            
            // Close all other dropdowns
            document.querySelectorAll('.dropdown-menu').forEach(m => {
                if (m !== menu) {
                    m.classList.add('hidden');
                    if (m.closest('.dropdown-container')) {
                        m.closest('.dropdown-container').style.zIndex = '10';
                    }
                }
            });
            
            if (isHidden) {
                if (container) container.style.zIndex = '9999';
                menu.classList.remove('hidden');
                
                // Optional: Adjust position if it goes off screen
                const rect = menu.getBoundingClientRect();
                if (rect.bottom > window.innerHeight) {
                    menu.style.bottom = '100%';
                    menu.style.top = 'auto';
                    menu.style.marginBottom = '0.5rem';
                    menu.style.marginTop = '0';
                    menu.classList.remove('origin-top-right');
                    menu.classList.add('origin-bottom-right');
                } else {
                    menu.style.bottom = 'auto';
                    menu.style.top = '100%';
                    menu.style.marginTop = '0.5rem';
                    menu.style.marginBottom = '0';
                    menu.classList.remove('origin-bottom-right');
                    menu.classList.add('origin-top-right');
                }
            } else {
                menu.classList.add('hidden');
                if (container) container.style.zIndex = '10';
            }
        }

        // Close dropdowns when clicking outside
        document.addEventListener('click', function(e) {
            if (!e.target.closest('.dropdown-container')) {
                document.querySelectorAll('.dropdown-menu').forEach(m => {
                    m.classList.add('hidden');
                    if (m.closest('.dropdown-container')) {
                        m.closest('.dropdown-container').style.zIndex = '10';
                    }
                });
            }
        });

        async function updateReportStatusManually(reportId, newStatus, btnEl) {
            const dropdown = btnEl.closest('.absolute');
            dropdown.classList.add('hidden');
            
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
                    
                    // Show success toast
                    if (window.showToast) {
                        window.showToast('Status Updated', `Case #${reportId} updated to ${newStatus}`, 'success');
                    }
                    
                    // Update the badge in the UI immediately for better feel
                    const row = btnEl.closest('tr');
                    const badge = row.querySelector('.bh-badge');
                    if (badge) {
                        badge.innerText = newStatus;
                        // We'd need the class mapping here too, but a reload is safer for now.
                    }

                    // Refresh after a short delay so user can see the toast
                    setTimeout(() => {
                        window.location.reload();
                    }, 1200);
                } else {
                    alert('Failed to update status');
                }
            } catch (error) {
                console.error(error);
                alert('An error occurred');
            }
        }
    </script>
@endsection
