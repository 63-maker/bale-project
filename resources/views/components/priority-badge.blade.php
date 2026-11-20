@props(['priority'])

@php
$classes = match(strtoupper($priority ?? 'MEDIUM')) {
    'CRITICAL' => 'bg-slate-900 text-white border-slate-900 font-bold tracking-wide',
    'HIGH' => 'bg-blue-600 text-white border-blue-600 font-semibold',
    'MEDIUM' => 'bg-blue-50 text-blue-700 border-blue-200 font-medium',
    'LOW' => 'bg-slate-100 text-slate-600 border-slate-200 font-medium',
    default => 'bg-slate-100 text-slate-600 border-slate-200 font-medium',
};
@endphp

<span {{ $attributes->merge(['class' => "inline-flex items-center px-2 py-0.5 rounded text-[11px] border {$classes}"]) }}>
    {{ strtoupper($priority ?? 'MEDIUM') }}
</span>
