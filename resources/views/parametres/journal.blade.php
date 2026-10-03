@extends('layouts.app')

@section('title', "Journal d'activité")
@section('page-title', "Journal d'activité — toute l'équipe")

@section('topbar-actions')
    <a href="{{ route('parametres.index') }}" class="btn-secondary text-xs flex items-center gap-1">
        <i class="ti ti-arrow-left"></i> Paramètres
    </a>
@endsection

@section('content')
<div class="space-y-4 max-w-5xl">

    <div class="alert-info">
        <i class="ti ti-shield-lock"></i>
        Ce journal regroupe l'activité de tous les comptes de votre cabinet, y compris la vôtre. Seul un administrateur peut consulter cette page — les autres comptes n'ont accès qu'à leur propre activité.
    </div>

    {{-- Filtres --}}
    <form method="GET" class="bg-white rounded-xl border border-gray-200 p-4 flex items-end gap-3">
        <div class="flex-1">
            <label class="label">Filtrer par utilisateur</label>
            <select name="user_id" class="input text-xs">
                <option value="">Tous les comptes</option>
                @foreach($users as $u)
                <option value="{{ $u->id }}" @selected(request('user_id') == $u->id)>{{ $u->nom_complet }}</option>
                @endforeach
            </select>
        </div>
        <div class="flex-1">
            <label class="label">Filtrer par action</label>
            <input type="text" name="action" value="{{ request('action') }}" placeholder="ex: ecriture, facture, connexion..." class="input text-xs">
        </div>
        <button type="submit" class="btn-primary text-xs flex items-center gap-1">
            <i class="ti ti-filter"></i> Filtrer
        </button>
        @if(request('user_id') || request('action'))
        <a href="{{ route('parametres.journal') }}" class="btn-secondary text-xs">Réinitialiser</a>
        @endif
    </form>

    {{-- Liste --}}
    <div class="bg-white rounded-xl border border-gray-200 overflow-hidden">
        <div class="px-4 py-3 border-b border-gray-100 flex items-center gap-2">
            <i class="ti ti-list-details text-gray-400"></i>
            <span class="text-sm font-semibold text-gray-800 flex-1">Activité récente</span>
            <span class="badge-gray">{{ $logs->total() }} entrée(s)</span>
        </div>
        <div class="divide-y divide-gray-100">
            @forelse($logs as $log)
            <div class="px-4 py-3 flex items-start gap-3 hover:bg-gray-50 transition">
                <div class="w-7 h-7 rounded-full bg-blue-100 flex items-center justify-center text-blue-700 text-[10px] font-semibold flex-shrink-0">
                    {{ $log->user?->initiales ?? '?' }}
                </div>
                <div class="flex-1 min-w-0">
                    <p class="text-xs text-gray-800">
                        <span class="font-medium">{{ $log->user_nom_snapshot ?? 'Compte supprimé' }}</span>
                        {{ $log->description }}
                    </p>
                    <p class="text-[11px] text-gray-400 mt-0.5">
                        {{ $log->created_at->format('d/m/Y à H:i') }}
                        @if($log->ip_address) · {{ $log->ip_address }} @endif
                    </p>
                </div>
                <span class="text-[10px] font-mono text-gray-400 flex-shrink-0">{{ $log->action }}</span>
                @if($log->user)
                <a href="{{ route('parametres.utilisateurs.activite', $log->user) }}" class="text-gray-400 hover:text-blue-600 flex-shrink-0">
                    <i class="ti ti-arrow-up-right"></i>
                </a>
                @endif
            </div>
            @empty
            <div class="px-4 py-8 text-center text-xs text-gray-400">
                <i class="ti ti-inbox text-2xl block mb-2"></i> Aucune activité ne correspond à ces filtres.
            </div>
            @endforelse
        </div>
        @if($logs->hasPages())
        <div class="px-4 py-3 border-t border-gray-100">
            {{ $logs->withQueryString()->links() }}
        </div>
        @endif
    </div>

</div>
@endsection
