<x-layouts.app :title="'Departments'">
    <div class="space-y-6" x-data="{ createModal: false }">
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
            <div>
                <h1 class="text-xl font-bold tracking-tight text-[#172B4D]">College Departments</h1>
                <p class="text-xs text-slate-500">Configure academic and operational departments for complaint routing</p>
            </div>
            <button type="button" @click="createModal = true" class="inline-flex items-center gap-2 px-4 py-2 rounded bg-[#172B4D] text-white text-xs font-semibold hover:bg-slate-900 transition">
                <i data-lucide="plus" class="w-4 h-4"></i>
                <span>Add Department</span>
            </button>
        </div>

        <div class="bg-white border border-slate-200 rounded overflow-hidden">
            <div class="overflow-x-auto">
                <table class="w-full text-left text-xs">
                    <thead class="bg-slate-50 border-b border-slate-200 text-slate-600 font-semibold uppercase text-[10px] tracking-wider">
                        <tr>
                            <th class="px-6 py-3">Department Name</th>
                            <th class="px-6 py-3">Code</th>
                            <th class="px-6 py-3">Associated Profiles</th>
                            <th class="px-6 py-3">Total Complaints</th>
                            <th class="px-6 py-3">Status</th>
                            <th class="px-6 py-3 text-right">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-200">
                        @foreach($departments as $dept)
                            <tr class="hover:bg-slate-50/60 transition" x-data="{ editMode: false }">
                                <td class="px-6 py-3.5 font-medium text-slate-900 whitespace-nowrap">
                                    <template x-if="!editMode">
                                        <span>{{ $dept->name }}</span>
                                    </template>
                                    <template x-if="editMode">
                                        <form id="edit-dept-{{ $dept->id }}" method="POST" action="{{ route('admin.departments.update', $dept->id) }}" class="inline">
                                            @csrf
                                            @method('PUT')
                                            <input type="text" name="name" value="{{ $dept->name }}" required class="px-2 py-1 text-xs rounded border border-slate-300">
                                            <input type="text" name="code" value="{{ $dept->code }}" required class="w-20 px-2 py-1 text-xs rounded border border-slate-300 ml-1">
                                        </form>
                                    </template>
                                </td>
                                <td class="px-6 py-3.5 font-semibold text-slate-700 whitespace-nowrap">
                                    {{ $dept->code }}
                                </td>
                                <td class="px-6 py-3.5 text-slate-600 whitespace-nowrap">
                                    {{ $dept->profiles_count }}
                                </td>
                                <td class="px-6 py-3.5 text-slate-600 whitespace-nowrap font-semibold">
                                    {{ $dept->complaints_count }}
                                </td>
                                <td class="px-6 py-3.5 whitespace-nowrap">
                                    <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium {{ $dept->is_active ? 'bg-emerald-50 text-emerald-700 border border-emerald-200' : 'bg-slate-100 text-slate-600 border border-slate-200' }}">
                                        {{ $dept->is_active ? 'Active' : 'Inactive' }}
                                    </span>
                                </td>
                                <td class="px-6 py-3.5 text-right whitespace-nowrap space-x-2">
                                    <template x-if="!editMode">
                                        <div class="inline-flex items-center gap-2">
                                            <button type="button" @click="editMode = true" class="text-xs text-[#2563EB] hover:underline font-medium">Edit</button>
                                            <form method="POST" action="{{ route('admin.departments.status', $dept->id) }}" class="inline">
                                                @csrf
                                                @method('PATCH')
                                                <button type="submit" class="text-xs {{ $dept->is_active ? 'text-amber-700 hover:underline' : 'text-emerald-700 hover:underline' }}">
                                                    {{ $dept->is_active ? 'Deactivate' : 'Activate' }}
                                                </button>
                                            </form>
                                        </div>
                                    </template>
                                    <template x-if="editMode">
                                        <div class="inline-flex items-center gap-2">
                                            <button type="submit" form="edit-dept-{{ $dept->id }}" class="px-2 py-1 rounded bg-[#2563EB] text-white text-xs font-semibold">Save</button>
                                            <button type="button" @click="editMode = false" class="px-2 py-1 rounded border border-slate-300 text-xs text-slate-600">Cancel</button>
                                        </div>
                                    </template>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>

        <!-- Create Department Modal -->
        <div x-show="createModal" class="fixed inset-0 z-50 overflow-y-auto bg-slate-900/50 flex items-center justify-center p-4" style="display: none;">
            <div class="bg-white rounded border border-slate-200 max-w-md w-full p-6" @click.outside="createModal = false">
                <div class="flex items-center justify-between mb-4">
                    <h3 class="text-sm font-bold text-[#172B4D]">Add Department</h3>
                    <button @click="createModal = false" class="text-slate-400 hover:text-slate-600">&times;</button>
                </div>
                <form method="POST" action="{{ route('admin.departments.store') }}" class="space-y-4">
                    @csrf
                    <div>
                        <label for="dept_name" class="block text-xs font-semibold text-slate-700 mb-1">Department Name <span class="text-red-500">*</span></label>
                        <input type="text" name="name" id="dept_name" required placeholder="e.g. Electrical & Electronics Engineering"
                               class="w-full px-3 py-2 text-xs rounded border border-slate-300 focus:outline-none focus:border-[#2563EB]">
                    </div>
                    <div>
                        <label for="dept_code" class="block text-xs font-semibold text-slate-700 mb-1">Department Code <span class="text-red-500">*</span></label>
                        <input type="text" name="code" id="dept_code" required placeholder="e.g. EEE"
                               class="w-full px-3 py-2 text-xs rounded border border-slate-300 focus:outline-none focus:border-[#2563EB]">
                    </div>
                    <div class="flex items-center justify-end gap-2 pt-2 border-t border-slate-200">
                        <button type="button" @click="createModal = false" class="px-3 py-1.5 rounded border border-slate-300 text-xs text-slate-600 hover:bg-slate-50">Cancel</button>
                        <button type="submit" class="px-4 py-1.5 rounded bg-[#172B4D] text-white text-xs font-semibold hover:bg-slate-900">Create Department</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</x-layouts.app>
