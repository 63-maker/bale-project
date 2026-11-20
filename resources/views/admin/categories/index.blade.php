<x-layouts.app :title="'Complaint Categories'">
    <div class="space-y-6" x-data="{ createModal: false }">
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
            <div>
                <h1 class="text-xl font-bold tracking-tight text-[#172B4D]">Complaint Categories</h1>
                <p class="text-xs text-slate-500">Configure grievance classifications and their default routing departments</p>
            </div>
            <button type="button" @click="createModal = true" class="inline-flex items-center gap-2 px-4 py-2 rounded bg-[#172B4D] text-white text-xs font-semibold hover:bg-slate-900 transition">
                <i data-lucide="plus" class="w-4 h-4"></i>
                <span>Add Category</span>
            </button>
        </div>

        <div class="bg-white border border-slate-200 rounded overflow-hidden">
            <div class="overflow-x-auto">
                <table class="w-full text-left text-xs">
                    <thead class="bg-slate-50 border-b border-slate-200 text-slate-600 font-semibold uppercase text-[10px] tracking-wider">
                        <tr>
                            <th class="px-6 py-3">Category Name</th>
                            <th class="px-6 py-3">Description</th>
                            <th class="px-6 py-3">Default Department</th>
                            <th class="px-6 py-3">Complaints Filed</th>
                            <th class="px-6 py-3">Status</th>
                            <th class="px-6 py-3 text-right">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-200">
                        @foreach($categories as $cat)
                            <tr class="hover:bg-slate-50/60 transition" x-data="{ editMode: false }">
                                <td class="px-6 py-3.5 font-medium text-slate-900 whitespace-nowrap">
                                    <template x-if="!editMode">
                                        <span>{{ $cat->name }}</span>
                                    </template>
                                    <template x-if="editMode">
                                        <form id="edit-cat-{{ $cat->id }}" method="POST" action="{{ route('admin.categories.update', $cat->id) }}" class="inline">
                                            @csrf
                                            @method('PUT')
                                            <input type="text" name="name" value="{{ $cat->name }}" required class="px-2 py-1 text-xs rounded border border-slate-300">
                                            <select name="department_id" class="px-2 py-1 text-xs rounded border border-slate-300 ml-1">
                                                <option value="">No Department</option>
                                                @foreach($departments as $d)
                                                    <option value="{{ $d->id }}" {{ $cat->department_id === $d->id ? 'selected' : '' }}>{{ $d->name }}</option>
                                                @endforeach
                                            </select>
                                        </form>
                                    </template>
                                </td>
                                <td class="px-6 py-3.5 text-slate-600 max-w-xs truncate">
                                    {{ $cat->description ?: 'N/A' }}
                                </td>
                                <td class="px-6 py-3.5 text-slate-600 whitespace-nowrap">
                                    {{ $cat->department?->name ?? 'None' }}
                                </td>
                                <td class="px-6 py-3.5 text-slate-800 whitespace-nowrap font-semibold">
                                    {{ $cat->complaints_count }}
                                </td>
                                <td class="px-6 py-3.5 whitespace-nowrap">
                                    <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium {{ $cat->is_active ? 'bg-emerald-50 text-emerald-700 border border-emerald-200' : 'bg-slate-100 text-slate-600 border border-slate-200' }}">
                                        {{ $cat->is_active ? 'Active' : 'Inactive' }}
                                    </span>
                                </td>
                                <td class="px-6 py-3.5 text-right whitespace-nowrap space-x-2">
                                    <template x-if="!editMode">
                                        <div class="inline-flex items-center gap-2">
                                            <button type="button" @click="editMode = true" class="text-xs text-[#2563EB] hover:underline font-medium">Edit</button>
                                            <form method="POST" action="{{ route('admin.categories.status', $cat->id) }}" class="inline">
                                                @csrf
                                                @method('PATCH')
                                                <button type="submit" class="text-xs {{ $cat->is_active ? 'text-amber-700 hover:underline' : 'text-emerald-700 hover:underline' }}">
                                                    {{ $cat->is_active ? 'Deactivate' : 'Activate' }}
                                                </button>
                                            </form>
                                        </div>
                                    </template>
                                    <template x-if="editMode">
                                        <div class="inline-flex items-center gap-2">
                                            <button type="submit" form="edit-cat-{{ $cat->id }}" class="px-2 py-1 rounded bg-[#2563EB] text-white text-xs font-semibold">Save</button>
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

        <!-- Create Category Modal -->
        <div x-show="createModal" class="fixed inset-0 z-50 overflow-y-auto bg-slate-900/50 flex items-center justify-center p-4" style="display: none;">
            <div class="bg-white rounded border border-slate-200 max-w-md w-full p-6" @click.outside="createModal = false">
                <div class="flex items-center justify-between mb-4">
                    <h3 class="text-sm font-bold text-[#172B4D]">Add Complaint Category</h3>
                    <button @click="createModal = false" class="text-slate-400 hover:text-slate-600">&times;</button>
                </div>
                <form method="POST" action="{{ route('admin.categories.store') }}" class="space-y-4">
                    @csrf
                    <div>
                        <label for="cat_name" class="block text-xs font-semibold text-slate-700 mb-1">Category Name <span class="text-red-500">*</span></label>
                        <input type="text" name="name" id="cat_name" required placeholder="e.g. Examinations & Grading"
                               class="w-full px-3 py-2 text-xs rounded border border-slate-300 focus:outline-none focus:border-[#2563EB]">
                    </div>
                    <div>
                        <label for="cat_desc" class="block text-xs font-semibold text-slate-700 mb-1">Description</label>
                        <input type="text" name="description" id="cat_desc" placeholder="Scope and examples of issues covered..."
                               class="w-full px-3 py-2 text-xs rounded border border-slate-300 focus:outline-none focus:border-[#2563EB]">
                    </div>
                    <div>
                        <label for="cat_dept" class="block text-xs font-semibold text-slate-700 mb-1">Default Department</label>
                        <select name="department_id" id="cat_dept" class="w-full px-3 py-2 text-xs rounded border border-slate-300 focus:outline-none focus:border-[#2563EB] bg-white">
                            <option value="">Select Department (Optional)</option>
                            @foreach($departments as $dept)
                                <option value="{{ $dept->id }}">{{ $dept->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="flex items-center justify-end gap-2 pt-2 border-t border-slate-200">
                        <button type="button" @click="createModal = false" class="px-3 py-1.5 rounded border border-slate-300 text-xs text-slate-600 hover:bg-slate-50">Cancel</button>
                        <button type="submit" class="px-4 py-1.5 rounded bg-[#172B4D] text-white text-xs font-semibold hover:bg-slate-900">Create Category</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</x-layouts.app>
