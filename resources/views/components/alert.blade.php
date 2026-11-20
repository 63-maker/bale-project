@if (session('success'))
    <div class="mb-4 p-4 rounded bg-emerald-50 border border-emerald-200 text-emerald-800 text-sm flex items-start gap-3">
        <i data-lucide="check-circle" class="w-5 h-5 text-emerald-600 shrink-0 mt-0.5"></i>
        <div class="flex-1">{{ session('success') }}</div>
    </div>
@endif

@if (session('error'))
    <div class="mb-4 p-4 rounded bg-rose-50 border border-rose-200 text-rose-800 text-sm flex items-start gap-3">
        <i data-lucide="alert-circle" class="w-5 h-5 text-rose-600 shrink-0 mt-0.5"></i>
        <div class="flex-1">{{ session('error') }}</div>
    </div>
@endif

@if (session('warning'))
    <div class="mb-4 p-4 rounded bg-amber-50 border border-amber-200 text-amber-800 text-sm flex items-start gap-3">
        <i data-lucide="alert-triangle" class="w-5 h-5 text-amber-600 shrink-0 mt-0.5"></i>
        <div class="flex-1">{{ session('warning') }}</div>
    </div>
@endif

@if (session('info'))
    <div class="mb-4 p-4 rounded bg-blue-50 border border-blue-200 text-blue-800 text-sm flex items-start gap-3">
        <i data-lucide="info" class="w-5 h-5 text-blue-600 shrink-0 mt-0.5"></i>
        <div class="flex-1">{{ session('info') }}</div>
    </div>
@endif

@if ($errors->any())
    <div class="mb-4 p-4 rounded bg-rose-50 border border-rose-200 text-rose-800 text-sm">
        <div class="flex items-center gap-2 font-medium mb-1">
            <i data-lucide="alert-circle" class="w-4 h-4 text-rose-600"></i>
            <span>Please correct the errors below:</span>
        </div>
        <ul class="list-disc list-inside text-xs space-y-0.5 mt-1 ml-1 text-rose-700">
            @foreach ($errors->all() as $error)
                <li>{{ $error }}</li>
            @endforeach
        </ul>
    </div>
@endif
