@extends('layouts.app')

@section('title', 'Activité — ' . $user->nom_complet)
@section('page-title', 'Activité de ' . $user->nom_complet)

@section('topbar-actions')
    <a href="{{ route('parametres.utilisateurs.show', $user) }}" class="btn-secondary text-xs flex items-center gap-1">
        <i class="ti ti-arrow-left"></i> Retour au profil
    </a>
@endsection

@section('content')
<div class="space-y-6 max-w-5xl">

    <div class="alert-info">
        <i class="ti ti-shield-lock"></i>
        Cette vue complète n'est visible que par les administrateurs. {{ $user->prenom }} ne peut pas voir l'activité d'autres comptes, y compris celle de l'administrateur.
    </div>

    {{-- Journal d'activité textuel --}}
    <div class="bg-white rounded-xl border border-gray-200 overflow-hidden">
        <div class="px-4 py-3 border-b border-gray-100 flex items-center gap-2">
            <i class="ti ti-list-details text-gray-400"></i>
            <span class="text-sm font-semibold text-gray-800 flex-1">Journal d'activité</span>
            <span class="badge-gray">{{ $logs->total() }} entrée(s)</span>
        </div>
        <div class="divide-y divide-gray-100">
            @forelse($logs as $log)
            <div class="px-4 py-3 flex items-start gap-3">
                <div class="w-2 h-2 rounded-full bg-blue-400 mt-1.5 flex-shrink-0"></div>
                <div class="flex-1 min-w-0">
                    <p class="text-xs text-gray-800">{{ $log->description }}</p>
                    <p class="text-[11px] text-gray-400 mt-0.5">
                        {{ $log->created_at->format('d/m/Y à H:i') }}
                        @if($log->ip_address) · {{ $log->ip_address }} @endif
                    </p>
                </div>
                <span class="text-[10px] font-mono text-gray-400 flex-shrink-0">{{ $log->action }}</span>
            </div>
            @empty
            <div class="px-4 py-8 text-center text-xs text-gray-400">
                <i class="ti ti-inbox text-2xl block mb-2"></i> Aucune activité enregistrée.
            </div>
            @endforelse
        </div>
        @if($logs->hasPages())
        <div class="px-4 py-3 border-t border-gray-100">
            {{ $logs->links() }}
        </div>
        @endif
    </div>

    {{-- Historique avant/après par enregistrement --}}
    <div class="bg-white rounded-xl border border-gray-200 overflow-hidden">
        <div class="px-4 py-3 border-b border-gray-100 flex items-center gap-2">
            <i class="ti ti-history text-gray-400"></i>
            <span class="text-sm font-semibold text-gray-800 flex-1">Historique des modifications</span>
            <span class="badge-gray">{{ $audits->total() }} changement(s)</span>
        </div>
        <div class="divide-y divide-gray-100">
            @forelse($audits as $audit)
            <div class="px-4 py-3">
                <div class="flex items-center gap-2 mb-2">
                    @if($audit->evenement === 'cree') <span class="badge-green">Création</span>
                    @elseif($audit->evenement === 'modifie') <span class="badge-blue">Modification</span>
                    @else <span class="badge-red">Suppression</span>
                    @endif
                    <span class="text-xs text-gray-600">
                        {{ class_basename($audit->sujet_type) }} #{{ $audit->sujet_id }}
                    </span>
                    <span class="text-[11px] text-gray-400 ml-auto">{{ $audit->created_at->format('d/m/Y à H:i') }}</span>
                </div>

                @if($audit->evenement === 'modifie' && count($audit->changements) > 0)
                <div class="bg-gray-50 rounded-lg p-3 space-y-1.5">
                    @foreach($audit->changements as $champ => $valeurs)
                    <div class="flex items-center gap-2 text-[11px]">
                        <span class="font-medium text-gray-600 w-32 flex-shrink-0">{{ $champ }}</span>
                        <span class="text-red-500 line-through truncate max-w-[140px]">{{ $valeurs['avant'] ?? '—' }}</span>
                        <i class="ti ti-arrow-right text-gray-300 flex-shrink-0"></i>
                        <span class="text-emerald-600 truncate max-w-[140px]">{{ $valeurs['apres'] ?? '—' }}</span>
                    </div>
                    @endforeach
                </div>
                @endif
            </div>
            @empty
            <div class="px-4 py-8 text-center text-xs text-gray-400">
                <i class="ti ti-history-off text-2xl block mb-2"></i> Aucune modification enregistrée.
            </div>
            @endforelse
        </div>
        @if($audits->hasPages())
        <div class="px-4 py-3 border-t border-gray-100">
            {{ $audits->links() }}
        </div>
        @endif
    </div>

</div>
@endsection
