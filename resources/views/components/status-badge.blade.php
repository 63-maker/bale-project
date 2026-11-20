@props(['status'])

@php
$classes = match($status) {
    'SUBMITTED' => 'bg-slate-100 text-slate-700 border-slate-300',
    'UNDER_REVIEW' => 'bg-sky-50 text-sky-700 border-sky-300',
    'ASSIGNED' => 'bg-blue-50 text-blue-700 border-blue-300',
    'IN_PROGRESS' => 'bg-indigo-50 text-indigo-700 border-indigo-300',
    'RESOLUTION_SUBMITTED' => 'bg-amber-50 text-amber-700 border-amber-300',
    'VERIFICATION_REJECTED' => 'bg-orange-50 text-orange-700 border-orange-300',
    'VERIFIED_FOR_CLOSURE' => 'bg-emerald-50 text-emerald-700 border-emerald-300',
    'CLOSED' => 'bg-emerald-100 text-emerald-800 border-emerald-400',
    'REOPENED' => 'bg-purple-50 text-purple-700 border-purple-300',
    'REJECTED' => 'bg-rose-50 text-rose-700 border-rose-300',
    default => 'bg-slate-100 text-slate-700 border-slate-300',
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

<span {{ $attributes->merge(['class' => "inline-flex items-center px-2.5 py-0.5 rounded text-xs font-medium border {$classes}"]) }}>
    {{ $label }}
</span>
