@extends('layouts.app')

@section('title', 'Nouveau rôle')
@section('page-title', 'Créer un rôle')

@section('topbar-actions')
    <a href="{{ route('parametres.roles.index') }}" class="btn-secondary text-xs flex items-center gap-1">
        <i class="ti ti-arrow-left"></i> Annuler
    </a>
@endsection

@section('content')
<form method="POST" action="{{ route('parametres.roles.store') }}" class="max-w-2xl space-y-4">
    @csrf

    <div class="bg-white rounded-xl border border-gray-200 p-5 space-y-4">
        <div>
            <label class="block text-xs font-medium text-gray-700 mb-1">Nom du rôle</label>
            <input type="text" name="nom" value="{{ old('nom') }}" required
                   placeholder="Ex : Stagiaire, Comptable senior, Auditeur externe..."
                   class="w-full text-sm border border-gray-300 rounded-lg px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-100 focus:border-blue-400">
            @error('nom')<p class="text-xs text-red-500 mt-1">{{ $message }}</p>@enderror
        </div>

        <div>
            <label class="block text-xs font-medium text-gray-700 mb-2">Couleur du badge</label>
            <div class="flex gap-2">
                @foreach(['green' => 'bg-emerald-500', 'blue' => 'bg-blue-500', 'amber' => 'bg-amber-500', 'red' => 'bg-red-500', 'purple' => 'bg-purple-500', 'gray' => 'bg-gray-400'] as $val => $bg)
                <label class="cursor-pointer">
                    <input type="radio" name="couleur" value="{{ $val }}" class="sr-only peer" {{ old('couleur') === $val ? 'checked' : ($val === 'blue' && !old('couleur') ? 'checked' : '') }}>
                    <span class="w-7 h-7 rounded-full {{ $bg }} block ring-2 ring-offset-2 ring-transparent peer-checked:ring-gray-400 transition"></span>
                </label>
                @endforeach
            </div>
        </div>
    </div>

    <div class="bg-white rounded-xl border border-gray-200 overflow-hidden">
        <div class="px-5 py-3 border-b border-gray-100">
            <p class="text-xs font-medium text-gray-700">Pages accessibles</p>
            <p class="text-[11px] text-gray-400 mt-0.5">Décochez une page pour la verrouiller complètement à ce rôle.</p>
        </div>
        <div class="divide-y divide-gray-100">
            @foreach($pages as $key => $label)
            <label class="flex items-center gap-3 px-5 py-3 hover:bg-gray-50 cursor-pointer transition">
                <input type="checkbox" name="pages[]" value="{{ $key }}"
                       {{ in_array($key, old('pages', array_keys($pages))) ? 'checked' : '' }}
                       class="w-4 h-4 rounded border-gray-300 text-blue-600 focus:ring-blue-200">
                <span class="text-sm text-gray-800">{{ $label }}</span>
            </label>
            @endforeach
        </div>
    </div>

    <div class="flex justify-end gap-2">
        <a href="{{ route('parametres.roles.index') }}" class="btn-secondary text-sm">Annuler</a>
        <button type="submit" class="btn-primary text-sm flex items-center gap-1">
            <i class="ti ti-check"></i> Créer le rôle
        </button>
    </div>
</form>
@endsection
