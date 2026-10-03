@props(['label', 'value', 'icon' => null, 'trend' => null, 'trendUp' => true, 'color' => 'default'])

<div class="bg-white rounded-xl border border-gray-200 p-4">
    <p class="text-xs text-gray-400 mb-1.5 flex items-center gap-1">
        @if($icon)<i class="ti {{ $icon }}"></i>@endif
        {{ $label }}
    </p>
    <p class="text-xl font-semibold font-mono
        {{ $color === 'green' ? 'text-emerald-600' : ($color === 'red' ? 'text-red-500' : 'text-gray-900') }}">
        {{ $value }}
    </p>
    @if($trend)
    <p class="text-xs mt-1 {{ $trendUp ? 'text-emerald-600' : 'text-red-500' }}">
        <i class="ti {{ $trendUp ? 'ti-arrow-up' : 'ti-arrow-down' }}"></i> {{ $trend }}
    </p>
    @endif
</div>
