@props(['title' => null, 'icon' => null])

<div {{ $attributes->merge(['class' => 'bg-white rounded-xl border border-gray-200 overflow-hidden']) }}>
    @if($title)
    <div class="px-4 py-3 border-b border-gray-100 flex items-center gap-2">
        @if($icon)<i class="ti {{ $icon }} text-gray-400 text-sm"></i>@endif
        <span class="text-sm font-medium text-gray-800">{{ $title }}</span>
        @isset($actions)
        <div class="ml-auto flex items-center gap-2">{{ $actions }}</div>
        @endisset
    </div>
    @endif
    {{ $slot }}
</div>
