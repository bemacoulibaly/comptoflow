@extends('layouts.app')

@section('title', 'Comptes utilisateurs')
@section('page-title', 'Comptes utilisateurs')

@section('topbar-actions')
    <a href="{{ route('parametres.roles.index') }}" class="btn-secondary text-xs flex items-center gap-1">
        <i class="ti ti-shield"></i> Gérer les rôles
    </a>
    <a href="{{ route('parametres.utilisateurs.create') }}" class="btn-primary text-xs flex items-center gap-1">
        <i class="ti ti-user-plus"></i> Créer un compte
    </a>
    <a href="{{ route('parametres.index') }}" class="btn-secondary text-xs flex items-center gap-1">
        <i class="ti ti-arrow-left"></i> Paramètres
    </a>
@endsection

@section('content')
<div class="bg-white rounded-xl border border-gray-200 overflow-hidden">
    <table class="w-full text-xs">
        <thead class="bg-gray-50 border-b border-gray-200">
            <tr>
                <th class="th">Utilisateur</th>
                <th class="th">Email</th>
                <th class="th">Rôle</th>
                <th class="th">Statut</th>
                <th class="th text-right">Écritures</th>
                <th class="th text-right">Factures</th>
                <th class="th"></th>
            </tr>
        </thead>
        <tbody class="divide-y divide-gray-100">
            @foreach($users as $u)
            <tr class="hover:bg-gray-50 transition {{ !$u->actif ? 'opacity-50' : '' }}">
                <td class="td">
                    <div class="flex items-center gap-2">
                        <div class="w-7 h-7 rounded-full bg-blue-100 flex items-center justify-center text-blue-700 text-[10px] font-semibold flex-shrink-0">
                            {{ $u->initiales }}
                        </div>
                        <span class="font-medium text-gray-900">{{ $u->nom_complet }}</span>
                        @if($u->id === auth()->id())
                        <span class="text-[10px] text-gray-400">(vous)</span>
                        @endif
                    </div>
                </td>
                <td class="td text-gray-500">{{ $u->email }}</td>
                <td class="td">
                    @php($couleur = $u->roleAssigne->couleur ?? ($u->role === 'admin' ? 'green' : ($u->role === 'editeur' ? 'blue' : 'gray')))
                    <span class="badge-{{ $couleur }}">{{ $u->nom_role_affichage }}</span>
                </td>
                <td class="td">
                    @if($u->actif)
                        <span class="badge-green">Actif</span>
                    @else
                        <span class="badge-red">Désactivé</span>
                    @endif
                </td>
                <td class="td text-right font-mono">{{ $u->ecritures_count }}</td>
                <td class="td text-right font-mono">{{ $u->factures_count }}</td>
                <td class="td">
                    <a href="{{ route('parametres.utilisateurs.show', $u) }}" class="text-gray-400 hover:text-blue-600">
                        <i class="ti ti-eye"></i>
                    </a>
                </td>
            </tr>
            @endforeach
        </tbody>
    </table>
</div>
@endsection
