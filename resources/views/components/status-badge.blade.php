@props(['status'])

@php
$classes = match($status) {
    'SUBMITTED' => 'bg-slate-100 text-slate-700 border-slate-200',
    'UNDER_REVIEW' => 'bg-slate-100 text-slate-800 border-slate-300 font-medium',
    'ASSIGNED' => 'bg-blue-50 text-blue-700 border-blue-200',
    'IN_PROGRESS' => 'bg-blue-50 text-blue-800 border-blue-300 font-medium',
    'RESOLUTION_SUBMITTED' => 'bg-slate-900 text-white border-slate-900 font-medium',
    'VERIFICATION_REJECTED' => 'bg-slate-800 text-slate-100 border-slate-700 font-medium',
    'VERIFIED_FOR_CLOSURE' => 'bg-blue-600 text-white border-blue-600 font-medium',
    'CLOSED' => 'bg-slate-900 text-white border-slate-900 font-semibold',
    'REOPENED' => 'bg-blue-100 text-blue-900 border-blue-300 font-semibold',
    'REJECTED' => 'bg-slate-50 text-slate-500 border-slate-200',
    default => 'bg-slate-100 text-slate-700 border-slate-200',
};

$label = match($status) {
    'SUBMITTED' => 'Submitted',
    'UNDER_REVIEW' => 'Under Review',
    'ASSIGNED' => 'Assigned',
    'IN_PROGRESS' => 'In Progress',
    'RESOLUTION_SUBMITTED' => 'Resolution Submitted',
    'VERIFICATION_REJECTED' => 'Verification Rejected',
    'VERIFIED_FOR_CLOSURE' => 'Verified for Closure',
    'CLOSED' => 'Closed',
    'REOPENED' => 'Reopened',
    'REJECTED' => 'Rejected',
    default => $status,
};
@endphp

<span {{ $attributes->merge(['class' => "inline-flex items-center px-2.5 py-0.5 rounded text-[11px] border {$classes}"]) }}>
    {{ $label }}
</span>
