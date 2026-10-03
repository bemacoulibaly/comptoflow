@props(['route', 'icon'])

@php
    $active = request()->routeIs($route) || request()->routeIs(str_replace('.index', '.*', $route));
@endphp

<a href="{{ route($route) }}"
   class="flex items-center gap-2 px-2 py-1.5 rounded-lg text-xs transition
          {{ $active
              ? 'bg-gray-100 text-gray-900 font-medium'
              : 'text-gray-500 hover:bg-gray-50 hover:text-gray-800' }}">
    <i class="ti {{ $icon }} text-sm flex-shrink-0"></i>
    {{ $slot }}
</a>
