@extends('layouts.app')

@section('title', 'Rôles')
@section('page-title', 'Rôles & permissions')

@section('topbar-actions')
    <a href="{{ route('parametres.roles.create') }}" class="btn-primary text-xs flex items-center gap-1">
        <i class="ti ti-plus"></i> Nouveau rôle
    </a>
    <a href="{{ route('parametres.utilisateurs.index') }}" class="btn-secondary text-xs flex items-center gap-1">
        <i class="ti ti-arrow-left"></i> Comptes
    </a>
@endsection

@section('content')
<div class="bg-white rounded-xl border border-gray-200 overflow-hidden">
    <table class="w-full text-xs">
        <thead class="bg-gray-50 border-b border-gray-200">
            <tr>
                <th class="th">Rôle</th>
                <th class="th">Type</th>
                <th class="th text-right">Comptes assignés</th>
                <th class="th"></th>
            </tr>
        </thead>
        <tbody class="divide-y divide-gray-100">
            @foreach($roles as $role)
            <tr class="hover:bg-gray-50 transition">
                <td class="td">
                    <span class="badge-{{ $role->couleur }}">{{ $role->nom }}</span>
                </td>
                <td class="td text-gray-500">
                    {{ $role->est_systeme ? 'Système (protégé)' : 'Personnalisé' }}
                </td>
                <td class="td text-right font-mono">{{ $role->users_count }}</td>
                <td class="td">
                    <div class="flex items-center justify-end gap-2">
                        @if(!$role->est_systeme)
                        <a href="{{ route('parametres.roles.edit', $role) }}" class="text-gray-400 hover:text-blue-600">
                            <i class="ti ti-pencil"></i>
                        </a>
                        <form method="POST" action="{{ route('parametres.roles.destroy', $role) }}"
                              onsubmit="return confirm('Supprimer le rôle « {{ $role->nom }} » ?');">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="text-gray-400 hover:text-red-600">
                                <i class="ti ti-trash"></i>
                            </button>
                        </form>
                        @else
                        <span class="text-gray-300" title="Rôle système, non modifiable">
                            <i class="ti ti-lock"></i>
                        </span>
                        @endif
                    </div>
                </td>
            </tr>
            @endforeach
        </tbody>
    </table>
</div>

<div class="mt-4 bg-amber-50 border border-amber-200 rounded-xl p-4 flex gap-3">
    <i class="ti ti-info-circle text-amber-500 text-lg flex-shrink-0"></i>
    <p class="text-xs text-amber-800">
        L'administrateur a toujours accès à toutes les pages de l'application, quelle que soit la configuration des rôles.
        Les restrictions de page ne s'appliquent qu'aux comptes Éditeur, Lecteur, ou aux rôles personnalisés créés ici.
    </p>
</div>
@endsection
