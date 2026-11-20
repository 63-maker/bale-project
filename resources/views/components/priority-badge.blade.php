@props(['priority'])

@php
$classes = match(strtoupper($priority ?? 'MEDIUM')) {
    'LOW' => 'bg-slate-50 text-slate-600 border-slate-200',
    'MEDIUM' => 'bg-blue-50 text-blue-700 border-blue-200',
    'HIGH' => 'bg-amber-50 text-amber-700 border-amber-200',
    'CRITICAL' => 'bg-red-50 text-red-700 border-red-200',
    default => 'bg-slate-50 text-slate-600 border-slate-200',
};
@endphp

<span {{ $attributes->merge(['class' => "inline-flex items-center px-2 py-0.5 rounded text-xs font-medium border {$classes}"]) }}>
    {{ strtoupper($priority ?? 'MEDIUM') }}
</span>
