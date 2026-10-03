@extends('layouts.app')

@section('title', 'Écritures')
@section('page-title', 'Journal des écritures')

@section('topbar-actions')
    <a href="{{ route('ecritures.grand-livre') }}" class="btn-secondary text-xs flex items-center gap-1">
        <i class="ti ti-book"></i> Grand livre
    </a>
    <a href="{{ route('import.create') }}" class="btn-secondary text-xs flex items-center gap-1">
        <i class="ti ti-table-import"></i> Importer relevé
    </a>
    <a href="{{ route('ecritures.create') }}" class="btn-primary text-xs flex items-center gap-1">
        <i class="ti ti-plus"></i> Nouvelle écriture
    </a>
@endsection

@section('content')
<div class="space-y-4">

    {{-- Filtres --}}
    <form method="GET" class="bg-white rounded-xl border border-gray-200 p-3 flex flex-wrap items-end gap-3">
        <div>
            <label class="label">Journal</label>
            <select name="journal" class="input text-xs">
                <option value="">Tous</option>
                @foreach(['BQ'=>'Banque','CA'=>'Caisse','AC'=>'Achats','VT'=>'Ventes','OD'=>'Opérations diverses','SA'=>'Salaires'] as $code=>$label)
                <option value="{{ $code }}" @selected(request('journal')===$code)>{{ $code }} — {{ $label }}</option>
                @endforeach
            </select>
        </div>
        <div>
            <label class="label">Statut</label>
            <select name="statut" class="input text-xs">
                <option value="">Tous</option>
                <option value="brouillon"  @selected(request('statut')==='brouillon')>Brouillon</option>
                <option value="validee"    @selected(request('statut')==='validee')>Validée</option>
                <option value="annulee"    @selected(request('statut')==='annulee')>Annulée</option>
            </select>
        </div>
        <div>
            <label class="label">Du</label>
            <input type="date" name="debut" value="{{ request('debut') }}" class="input text-xs">
        </div>
        <div>
            <label class="label">Au</label>
            <input type="date" name="fin" value="{{ request('fin') }}" class="input text-xs">
        </div>
        <div class="flex-1">
            <label class="label">Recherche</label>
            <input type="text" name="search" value="{{ request('search') }}" placeholder="Libellé, n° pièce…" class="input text-xs">
        </div>
        <button type="submit" class="btn-primary text-xs">Filtrer</button>
        <a href="{{ route('ecritures.index') }}" class="btn-secondary text-xs">Réinitialiser</a>
    </form>

    {{-- Tableau --}}
    <div class="bg-white rounded-xl border border-gray-200 overflow-hidden">
        <table class="w-full text-xs">
            <thead class="bg-gray-50 border-b border-gray-200">
                <tr>
                    <th class="th">N° pièce</th>
                    <th class="th">Date</th>
                    <th class="th">Journal</th>
                    <th class="th">Libellé</th>
                    <th class="th text-right">Débit</th>
                    <th class="th text-right">Crédit</th>
                    <th class="th">Saisi par</th>
                    <th class="th">Statut</th>
                    <th class="th"></th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-100">
                @forelse($ecritures as $ecriture)
                <tr class="hover:bg-gray-50 transition">
                    <td class="td font-mono text-gray-500">{{ $ecriture->numero_piece }}</td>
                    <td class="td text-gray-600">{{ $ecriture->date_ecriture->format('d/m/Y') }}</td>
                    <td class="td">
                        <span class="badge-blue">{{ $ecriture->journal }}</span>
                    </td>
                    <td class="td font-medium text-gray-900 max-w-xs truncate">{{ $ecriture->libelle }}</td>
                    <td class="td text-right font-mono">{{ number_format($ecriture->total_debit, 0, ',', ' ') }}</td>
                    <td class="td text-right font-mono">{{ number_format($ecriture->total_credit, 0, ',', ' ') }}</td>
                    <td class="td text-gray-500">{{ $ecriture->user->nom_complet }}</td>
                    <td class="td">
                        @if($ecriture->statut === 'validee')
                            <span class="badge-green">Validée</span>
                        @elseif($ecriture->statut === 'annulee')
                            <span class="badge-red">Annulée</span>
                        @else
                            <span class="badge-amber">Brouillon</span>
                        @endif
                    </td>
                    <td class="td">
                        <div class="flex items-center gap-2">
                            <a href="{{ route('ecritures.show', $ecriture) }}" class="text-gray-400 hover:text-blue-600">
                                <i class="ti ti-eye"></i>
                            </a>
                            @if($ecriture->statut === 'brouillon' && auth()->user()->peutEditer())
                            <form method="POST" action="{{ route('ecritures.valider', $ecriture) }}">
                                @csrf
                                <button type="submit" class="text-gray-400 hover:text-emerald-600" title="Valider">
                                    <i class="ti ti-check"></i>
                                </button>
                            </form>
                            @endif
                        </div>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="9" class="td text-center text-gray-400 py-10">
                        <i class="ti ti-inbox text-3xl block mb-2"></i>
                        Aucune écriture trouvée.
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>

        <div class="px-4 py-3 border-t border-gray-100">
            {{ $ecritures->links() }}
        </div>
    </div>
</div>
@endsection
