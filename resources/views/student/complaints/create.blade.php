<x-layouts.app :title="'Submit Complaint'">
    <div class="max-w-3xl mx-auto">
        <div class="mb-6 flex items-center justify-between">
            <div>
                <h1 class="text-xl font-bold tracking-tight text-slate-900">Submit Complaint</h1>
                <p class="text-xs text-slate-500">File a formal institutional complaint with supporting documents</p>
            </div>
            <a href="{{ route('student.complaints.index') }}" class="text-xs font-medium text-slate-600 hover:text-slate-900">
                &larr; Back to Complaints
            </a>
        </div>

        <div class="bg-white border border-slate-200 rounded p-6 sm:p-8" x-data="{ filesCount: 0, filesList: [] }">
            <form method="POST" action="{{ route('student.complaints.store') }}" enctype="multipart/form-data" class="space-y-5">
                @csrf

                <div>
                    <label for="subject" class="block text-xs font-semibold text-slate-700 mb-1">
                        Complaint Subject <span class="text-red-500">*</span>
                    </label>
                    <input type="text" 
                           name="subject" 
                           id="subject" 
                           value="{{ old('subject') }}" 
                           required 
                           minlength="5"
                           maxlength="255"
                           placeholder="Concise summary of the grievance (e.g. Non-functioning lab equipment in CS Lab 3)"
                           class="w-full px-3 py-2 text-xs rounded border border-slate-300 focus:outline-none focus:border-blue-600 focus:ring-1 focus:ring-blue-600">
                    <p class="text-[11px] text-slate-400 mt-1">Brief summary of the issue. Minimum 5 characters.</p>
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <div>
                        <label for="department_id" class="block text-xs font-semibold text-slate-700 mb-1">
                            Responsible Department <span class="text-red-500">*</span>
                        </label>
                        <select name="department_id" 
                                id="department_id" 
                                required 
                                class="w-full px-3 py-2 text-xs rounded border border-slate-300 focus:outline-none focus:border-blue-600 focus:ring-1 focus:ring-blue-600 bg-white">
                            <option value="">Select Department</option>
                            @foreach($departments as $dept)
                                <option value="{{ $dept->id }}" {{ old('department_id') == $dept->id ? 'selected' : '' }}>
                                    {{ $dept->name }} ({{ $dept->code }})
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <div>
                        <label for="category_id" class="block text-xs font-semibold text-slate-700 mb-1">
                            Complaint Category <span class="text-red-500">*</span>
                        </label>
                        <select name="category_id" 
                                id="category_id" 
                                required 
                                class="w-full px-3 py-2 text-xs rounded border border-slate-300 focus:outline-none focus:border-blue-600 focus:ring-1 focus:ring-blue-600 bg-white">
                            <option value="">Select Category</option>
                            @foreach($categories as $cat)
                                <option value="{{ $cat->id }}" {{ old('category_id') == $cat->id ? 'selected' : '' }}>
                                    {{ $cat->name }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                </div>

                <div>
                    <label for="priority" class="block text-xs font-semibold text-slate-700 mb-1">
                        Urgency Level
                    </label>
                    <select name="priority" 
                            id="priority" 
                            class="w-full px-3 py-2 text-xs rounded border border-slate-300 focus:outline-none focus:border-blue-600 focus:ring-1 focus:ring-blue-600 bg-white">
                        <option value="LOW" {{ old('priority') == 'LOW' ? 'selected' : '' }}>Low (Minor inconvenience / general query)</option>
                        <option value="MEDIUM" {{ old('priority', 'MEDIUM') == 'MEDIUM' ? 'selected' : '' }}>Medium (Standard operational issue)</option>
                        <option value="HIGH" {{ old('priority') == 'HIGH' ? 'selected' : '' }}>High (Impacting coursework or hostel living)</option>
                        <option value="CRITICAL" {{ old('priority') == 'CRITICAL' ? 'selected' : '' }}>Critical (Urgent safety, health or exam emergency)</option>
                    </select>
                </div>

                <div>
                    <label for="description" class="block text-xs font-semibold text-slate-700 mb-1">
                        Detailed Description <span class="text-red-500">*</span>
                    </label>
                    <textarea name="description" 
                              id="description" 
                              rows="5" 
                              required 
                              minlength="10"
                              maxlength="5000"
                              placeholder="Please provide exact details, date/time observed, location/room number, and any previous communications..."
                              class="w-full px-3 py-2 text-xs rounded border border-slate-300 focus:outline-none focus:border-blue-600 focus:ring-1 focus:ring-blue-600 leading-relaxed">{{ old('description') }}</textarea>
                    <p class="text-[11px] text-slate-400 mt-1">Provide clear factual details to assist Management investigation.</p>
                </div>

                <!-- Attachment Upload Area -->
                <div>
                    <label class="block text-xs font-semibold text-slate-700 mb-1">
                        Supporting Attachments (Optional)
                    </label>
                    <div class="border-2 border-dashed border-slate-300 rounded p-6 text-center hover:border-blue-600 transition bg-slate-50/50 relative">
                        <input type="file" 
                               name="attachments[]" 
                               id="attachments" 
                               multiple 
                               accept=".jpg,.jpeg,.png,.webp,.pdf"
                               @change="
                                   filesList = Array.from($event.target.files).map(f => `${f.name} (${(f.size/1024/1024).toFixed(2)} MB)`);
                                   filesCount = filesList.length;
                               "
                               class="absolute inset-0 w-full h-full opacity-0 cursor-pointer">
                        <i data-lucide="upload-cloud" class="w-8 h-8 text-slate-400 mx-auto mb-2"></i>
                        <p class="text-xs font-medium text-slate-700">Click to browse or drag and drop supporting files</p>
                        <p class="text-[11px] text-slate-500 mt-1">Allowed formats: JPG, PNG, WebP, PDF &bull; Max 5 MB per file &bull; Up to 5 files</p>
                    </div>

                    <!-- File List Preview -->
                    <template x-if="filesCount > 0">
                        <div class="mt-3 p-3 bg-slate-50 border border-slate-200 rounded">
                            <span class="text-xs font-semibold text-slate-700 block mb-1">Selected files (<span x-text="filesCount"></span>):</span>
                            <ul class="text-xs text-slate-600 list-disc list-inside space-y-0.5">
                                <template x-for="name in filesList" :key="name">
                                    <li x-text="name"></li>
                                </template>
                            </ul>
                        </div>
                    </template>
                </div>

                <div class="pt-3 border-t border-slate-200 flex items-center justify-end gap-3">
                    <a href="{{ route('student.complaints.index') }}" class="px-4 py-2 rounded border border-slate-300 text-slate-600 text-xs font-semibold hover:bg-slate-50 transition">
                        Cancel
                    </a>
                    <button type="submit" class="px-5 py-2 rounded bg-blue-600 text-white text-xs font-semibold hover:bg-blue-700 transition">
                        Submit Complaint
                    </button>
                </div>
            </form>
        </div>
    </div>
</x-layouts.app>
