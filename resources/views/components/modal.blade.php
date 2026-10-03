@props(['id', 'title', 'size' => 'md'])

@php
$sizes = [
    'sm' => 'max-w-md',
    'md' => 'max-w-lg',
    'lg' => 'max-w-2xl',
    'xl' => 'max-w-4xl',
];
$maxW = $sizes[$size] ?? $sizes['md'];
@endphp

<div id="{{ $id }}"
     class="fixed inset-0 z-50 hidden items-center justify-center bg-black/40 backdrop-blur-sm"
     onclick="if(event.target===this) closeModal('{{ $id }}')">
    <div class="bg-white rounded-xl shadow-xl w-full {{ $maxW }} mx-4 overflow-hidden">
        <div class="flex items-center justify-between px-5 py-4 border-b border-gray-100">
            <h3 class="text-sm font-semibold text-gray-900">{{ $title }}</h3>
            <button onclick="closeModal('{{ $id }}')" class="text-gray-400 hover:text-gray-600">
                <i class="ti ti-x"></i>
            </button>
        </div>
        <div class="px-5 py-4">{{ $slot }}</div>
        @isset($footer)
        <div class="px-5 py-3 border-t border-gray-100 flex justify-end gap-2">{{ $footer }}</div>
        @endisset
    </div>
</div>

@once
@push('scripts')
<script>
function openModal(id)  { document.getElementById(id).classList.replace('hidden','flex'); }
function closeModal(id) { document.getElementById(id).classList.replace('flex','hidden'); }
</script>
@endpush
@endonce
