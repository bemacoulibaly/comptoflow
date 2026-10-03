@extends('layouts.app')
@section('title','Calendrier')
@section('page-title','Calendrier & Échéances')

@section('topbar-actions')
    <button onclick="openModal('modal-evenement')" class="btn-primary text-xs flex items-center gap-1">
        <i class="ti ti-plus"></i> Nouvelle échéance
    </button>
@endsection

@section('content')
<div class="grid grid-cols-3 gap-4">

    {{-- Calendrier mensuel --}}
    <div class="col-span-2 bg-white rounded-xl border border-gray-200 overflow-hidden">
        <div class="px-4 py-3 border-b border-gray-100 flex items-center gap-3">
            <a href="{{ route('calendrier.index', ['mois' => $mois == 1 ? 12 : $mois-1, 'annee' => $mois == 1 ? $annee-1 : $annee]) }}"
               class="btn-ghost text-sm"><i class="ti ti-chevron-left"></i></a>
            <span class="text-sm font-semibold text-gray-800 flex-1 text-center">
                {{ $debut->translatedFormat('F Y') }}
            </span>
            <a href="{{ route('calendrier.index', ['mois' => $mois == 12 ? 1 : $mois+1, 'annee' => $mois == 12 ? $annee+1 : $annee]) }}"
               class="btn-ghost text-sm"><i class="ti ti-chevron-right"></i></a>
        </div>
        <div class="p-4">
            {{-- En-têtes jours --}}
            <div class="grid grid-cols-7 mb-2">
                @foreach(['Lun','Mar','Mer','Jeu','Ven','Sam','Dim'] as $j)
                <div class="text-center text-[10px] font-medium text-gray-400 py-1">{{ $j }}</div>
                @endforeach
            </div>

            {{-- Cases du mois --}}
            <div class="grid grid-cols-7 gap-1">
                {{-- Décalage premier jour --}}
                @php
                    $premierJour = $debut->dayOfWeek === 0 ? 6 : $debut->dayOfWeek - 1;
                @endphp
                @for($i = 0; $i < $premierJour; $i++)
                    <div></div>
                @endfor

                @for($d = 1; $d <= $debut->daysInMonth; $d++)
                    @php
                        $isToday = $d == now()->day && $mois == now()->month && $annee == now()->year;
                        $hasEv   = isset($evenements[$d]) && $evenements[$d]->count() > 0;
                    @endphp
                    <div class="relative p-1.5 rounded-lg text-center text-xs cursor-pointer
                                {{ $isToday ? 'bg-emerald-600 text-white font-semibold' : 'hover:bg-gray-100 text-gray-700' }}">
                        {{ $d }}
                        @if($hasEv)
                        <span class="absolute bottom-0.5 left-1/2 -translate-x-1/2 w-1 h-1 rounded-full
                                     {{ $isToday ? 'bg-white' : 'bg-emerald-500' }}"></span>
                        @endif
                    </div>
                @endfor
            </div>

            {{-- Légende --}}
            <div class="flex items-center gap-4 mt-3 pt-3 border-t border-gray-100">
                <span class="flex items-center gap-1 text-xs text-gray-400">
                    <span class="w-2 h-2 rounded-full bg-emerald-500 inline-block"></span> Échéance
                </span>
                <span class="flex items-center gap-1 text-xs text-gray-400">
                    <span class="w-4 h-4 rounded-lg bg-emerald-600 inline-block"></span> Aujourd'hui
                </span>
            </div>
        </div>
    </div>

    {{-- Prochaines échéances --}}
    <div class="bg-white rounded-xl border border-gray-200 overflow-hidden">
        <div class="px-4 py-3 border-b border-gray-100">
            <span class="text-sm font-semibold text-gray-800">Prochaines échéances</span>
        </div>
        <div class="divide-y divide-gray-100">
            @forelse($aVenir as $e)
            <div class="px-4 py-3 flex items-start gap-3">
                <div class="text-center flex-shrink-0">
                    <div class="text-[10px] text-gray-400 uppercase">{{ $e->date_echeance->translatedFormat('M') }}</div>
                    <div class="text-base font-bold text-gray-900 leading-none">{{ $e->date_echeance->format('d') }}</div>
                </div>
                <div class="flex-1 min-w-0">
                    <p class="text-xs font-medium text-gray-900 truncate">{{ $e->titre }}</p>
                    <p class="text-[10px] text-gray-400 mt-0.5">{{ ucfirst($e->type) }}</p>
                </div>
                <div class="flex items-center gap-1.5 flex-shrink-0">
                    @if($e->priorite === 'haute')   <span class="badge-red text-[9px]">Urgent</span>
                    @elseif($e->priorite === 'basse') <span class="badge-gray text-[9px]">Basse</span>
                    @else <span class="badge-blue text-[9px]">Normal</span>
                    @endif
                    <form method="POST" action="{{ route('calendrier.destroy', $e) }}">
                        @csrf @method('DELETE')
                        <button class="text-gray-300 hover:text-red-400" title="Supprimer"><i class="ti ti-x text-xs"></i></button>
                    </form>
                </div>
            </div>
            @empty
            <div class="px-4 py-8 text-center text-xs text-gray-400">
                <i class="ti ti-calendar-off text-2xl block mb-2"></i>
                Aucune échéance à venir.
            </div>
            @endforelse
        </div>
    </div>
</div>

{{-- Modal ajout échéance --}}
<x-modal id="modal-evenement" title="Nouvelle échéance">
    <form method="POST" action="{{ route('calendrier.store') }}" class="space-y-3">
        @csrf
        <div>
            <label class="label">Titre <span class="text-red-500">*</span></label>
            <input type="text" name="titre" class="input" required placeholder="ex: Déclaration TVA juin 2025">
        </div>
        <div class="grid grid-cols-2 gap-3">
            <div>
                <label class="label">Type</label>
                <select name="type" class="input">
                    <option value="tva">TVA</option>
                    <option value="cnps">CNPS</option>
                    <option value="bic">BIC</option>
                    <option value="facture">Facture</option>
                    <option value="rapprochement">Rapprochement</option>
                    <option value="autre" selected>Autre</option>
                </select>
            </div>
            <div>
                <label class="label">Priorité</label>
                <select name="priorite" class="input">
                    <option value="haute">Haute</option>
                    <option value="normale" selected>Normale</option>
                    <option value="basse">Basse</option>
                </select>
            </div>
        </div>
        <div>
            <label class="label">Date d'échéance <span class="text-red-500">*</span></label>
            <input type="date" name="date_echeance" class="input" required value="{{ today()->toDateString() }}">
        </div>
        <div>
            <label class="label">Description</label>
            <textarea name="description" class="input" rows="2" placeholder="Détails optionnels…"></textarea>
        </div>
        <div class="flex justify-end gap-2 pt-2">
            <button type="button" onclick="closeModal('modal-evenement')" class="btn-secondary text-xs">Annuler</button>
            <button type="submit" class="btn-primary text-xs"><i class="ti ti-check"></i> Enregistrer</button>
        </div>
    </form>
</x-modal>
@endsection
