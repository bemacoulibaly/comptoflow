@props(['color' => 'gray'])

@php
$colors = [
    'green' => 'bg-emerald-100 text-emerald-700',
    'red'   => 'bg-red-100 text-red-600',
    'amber' => 'bg-amber-100 text-amber-700',
    'blue'  => 'bg-blue-100 text-blue-700',
    'gray'  => 'bg-gray-100 text-gray-600',
];
$cls = $colors[$color] ?? $colors['gray'];
@endphp

<span {{ $attributes->merge(['class' => "inline-flex items-center px-2 py-0.5 rounded-full text-[10px] font-medium $cls"]) }}>
    {{ $slot }}
</span>
