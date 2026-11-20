<x-layouts.app :title="'Students Management'">
    <div class="space-y-6">
        <div>
            <h1 class="text-xl font-bold tracking-tight text-[#172B4D]">Registered Students</h1>
            <p class="text-xs text-slate-500">Directory of self-registered students, department affiliations, and account statuses</p>
        </div>

        <!-- Filter Bar -->
        <div class="bg-white border border-slate-200 rounded p-4">
            <form method="GET" action="{{ route('admin.students.index') }}" class="grid grid-cols-1 sm:grid-cols-4 gap-3">
                <div>
                    <label for="search" class="block text-[11px] font-semibold text-slate-600 mb-1">Search</label>
                    <input type="text" name="search" id="search" value="{{ request('search') }}" placeholder="Name, Register No, Email..."
                           class="w-full px-3 py-1.5 text-xs rounded border border-slate-300 focus:outline-none focus:border-[#2563EB]">
                </div>

                <div>
                    <label for="department_id" class="block text-[11px] font-semibold text-slate-600 mb-1">Department</label>
                    <select name="department_id" id="department_id" class="w-full px-3 py-1.5 text-xs rounded border border-slate-300 focus:outline-none focus:border-[#2563EB] bg-white">
                        <option value="">All Departments</option>
                        @foreach($departments as $dept)
                            <option value="{{ $dept->id }}" {{ request('department_id') == $dept->id ? 'selected' : '' }}>{{ $dept->name }}</option>
                        @endforeach
                    </select>
                </div>

                <div>
                    <label for="study_year" class="block text-[11px] font-semibold text-slate-600 mb-1">Study Year</label>
                    <select name="study_year" id="study_year" class="w-full px-3 py-1.5 text-xs rounded border border-slate-300 focus:outline-none focus:border-[#2563EB] bg-white">
                        <option value="">All Years</option>
                        <option value="1" {{ request('study_year') == '1' ? 'selected' : '' }}>1st Year</option>
                        <option value="2" {{ request('study_year') == '2' ? 'selected' : '' }}>2nd Year</option>
                        <option value="3" {{ request('study_year') == '3' ? 'selected' : '' }}>3rd Year</option>
                        <option value="4" {{ request('study_year') == '4' ? 'selected' : '' }}>4th Year</option>
                        <option value="5" {{ request('study_year') == '5' ? 'selected' : '' }}>5th Year</option>
                    </select>
                </div>

                <div class="flex items-end gap-2">
                    <button type="submit" class="px-4 py-1.5 rounded bg-[#172B4D] text-white text-xs font-semibold hover:bg-slate-900 transition">
                        Filter
                    </button>
                    @if(request()->hasAny(['search', 'department_id', 'study_year']))
                        <a href="{{ route('admin.students.index') }}" class="px-3 py-1.5 rounded border border-slate-300 text-slate-600 text-xs font-semibold hover:bg-slate-50 transition">
                            Reset
                        </a>
                    @endif
                </div>
            </form>
        </div>

        <div class="bg-white border border-slate-200 rounded overflow-hidden">
            <div class="overflow-x-auto">
                <table class="w-full text-left text-xs">
                    <thead class="bg-slate-50 border-b border-slate-200 text-slate-600 font-semibold uppercase text-[10px] tracking-wider">
                        <tr>
                            <th class="px-6 py-3">Student Name</th>
                            <th class="px-6 py-3">Register Number</th>
                            <th class="px-6 py-3">Email Address</th>
                            <th class="px-6 py-3">Department</th>
                            <th class="px-6 py-3">Year</th>
                            <th class="px-6 py-3">Complaints Filed</th>
                            <th class="px-6 py-3">Status</th>
                            <th class="px-6 py-3 text-right">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-200">
                        @forelse($students as $s)
                            <tr class="hover:bg-slate-50/60 transition">
                                <td class="px-6 py-3.5 font-medium text-slate-900 whitespace-nowrap">
                                    {{ $s->full_name }}
                                </td>
                                <td class="px-6 py-3.5 text-slate-700 whitespace-nowrap font-medium">
                                    {{ $s->register_number }}
                                </td>
                                <td class="px-6 py-3.5 text-slate-600 whitespace-nowrap">
                                    {{ $s->email }}
                                </td>
                                <td class="px-6 py-3.5 text-slate-600 whitespace-nowrap">
                                    {{ $s->department?->name ?? 'N/A' }}
                                </td>
                                <td class="px-6 py-3.5 text-slate-600 whitespace-nowrap">
                                    {{ $s->study_year }} Year
                                </td>
                                <td class="px-6 py-3.5 whitespace-nowrap font-semibold text-slate-800">
                                    {{ $s->complaints_count }}
                                </td>
                                <td class="px-6 py-3.5 whitespace-nowrap">
                                    @if($s->isActive())
                                        <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-emerald-50 text-emerald-700 border border-emerald-200">
                                            Active
                                        </span>
                                    @else
                                        <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-rose-50 text-rose-700 border border-rose-200">
                                            Suspended
                                        </span>
                                    @endif
                                </td>
                                <td class="px-6 py-3.5 text-right whitespace-nowrap">
                                    <form method="POST" action="{{ route('admin.students.status', $s->id) }}" class="inline-block">
                                        @csrf
                                        @method('PATCH')
                                        <button type="submit" class="px-2.5 py-1 rounded text-xs font-medium {{ $s->isActive() ? 'border border-rose-300 text-rose-700 hover:bg-rose-50' : 'border border-emerald-300 text-emerald-700 hover:bg-emerald-50' }}">
                                            {{ $s->isActive() ? 'Suspend' : 'Activate' }}
                                        </button>
                                    </form>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="8" class="px-6 py-12 text-center text-slate-500">
                                    <p class="font-medium text-slate-700">No students matching criteria.</p>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            @if($students->hasPages())
                <div class="px-6 py-4 border-t border-slate-200 bg-slate-50">
                    {{ $students->links() }}
                </div>
            @endif
        </div>
    </div>
</x-layouts.app>
