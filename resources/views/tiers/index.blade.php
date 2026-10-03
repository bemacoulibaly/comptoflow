@extends('layouts.app')
@section('title', 'Tiers')
@section('page-title', 'Clients & Fournisseurs')

@section('topbar-actions')
    <a href="{{ route('tiers.create') }}" class="btn-primary text-xs flex items-center gap-1">
        <i class="ti ti-plus"></i> Nouveau tiers
    </a>
@endsection

@section('content')
<div class="space-y-4">
    <form method="GET" class="bg-white rounded-xl border border-gray-200 p-3 flex gap-3 items-end">
        <div>
            <label class="label">Type</label>
            <select name="type" class="input text-xs">
                <option value="">Tous</option>
                <option value="client"      @selected(request('type')==='client')>Clients</option>
                <option value="fournisseur" @selected(request('type')==='fournisseur')>Fournisseurs</option>
                <option value="les_deux"    @selected(request('type')==='les_deux')>Les deux</option>
            </select>
        </div>
        <div class="flex-1">
            <label class="label">Recherche</label>
            <input type="text" name="search" value="{{ request('search') }}" placeholder="Nom, ville…" class="input text-xs">
        </div>
        <button type="submit" class="btn-primary text-xs">Filtrer</button>
        <a href="{{ route('tiers.index') }}" class="btn-secondary text-xs">Reset</a>
    </form>

    <div class="bg-white rounded-xl border border-gray-200 overflow-hidden">
        <table class="w-full text-xs">
            <thead class="bg-gray-50 border-b border-gray-200">
                <tr>
                    <th class="th">Tiers</th>
                    <th class="th">Type</th>
                    <th class="th">Ville</th>
                    <th class="th">Email</th>
                    <th class="th">Téléphone</th>
                    <th class="th">N° contribuable</th>
                    <th class="th">Statut</th>
                    <th class="th"></th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-100">
                @forelse($tiers as $tier)
                <tr class="hover:bg-gray-50 transition">
                    <td class="td">
                        <div class="flex items-center gap-2">
                            <div class="w-7 h-7 rounded-full bg-blue-100 flex items-center justify-center text-blue-700 text-[10px] font-semibold flex-shrink-0">
                                {{ $tier->initiales }}
                            </div>
                            <span class="font-medium text-gray-900">{{ $tier->nom }}</span>
                        </div>
                    </td>
                    <td class="td">
                        @if($tier->type === 'client') <span class="badge-green">Client</span>
                        @elseif($tier->type === 'fournisseur') <span class="badge-amber">Fournisseur</span>
                        @else <span class="badge-blue">Les deux</span>
                        @endif
                    </td>
                    <td class="td text-gray-500">{{ $tier->ville ?? '—' }}</td>
                    <td class="td text-gray-500">{{ $tier->email ?? '—' }}</td>
                    <td class="td text-gray-500">{{ $tier->telephone ?? '—' }}</td>
                    <td class="td font-mono text-gray-400">{{ $tier->numero_contribuable ?? '—' }}</td>
                    <td class="td">
                        @if($tier->actif) <span class="badge-green">Actif</span>
                        @else <span class="badge-gray">Inactif</span>
                        @endif
                    </td>
                    <td class="td">
                        <div class="flex items-center gap-2">
                            <a href="{{ route('tiers.show', $tier) }}" class="text-gray-400 hover:text-blue-600"><i class="ti ti-eye"></i></a>
                            <a href="{{ route('tiers.edit', $tier) }}" class="text-gray-400 hover:text-emerald-600"><i class="ti ti-pencil"></i></a>
                            @if(auth()->user()->isAdmin())
                            <form method="POST" action="{{ route('tiers.destroy', $tier) }}"
                                  onsubmit="return confirm('Supprimer ce tiers ?')">
                                @csrf @method('DELETE')
                                <button class="text-gray-400 hover:text-red-500"><i class="ti ti-trash"></i></button>
                            </form>
                            @endif
                        </div>
                    </td>
                </tr>
                @empty
                <tr><td colspan="8" class="td text-center text-gray-400 py-10">
                    <i class="ti ti-users text-3xl block mb-2"></i> Aucun tiers trouvé.
                </td></tr>
                @endforelse
            </tbody>
        </table>
        <div class="px-4 py-3 border-t border-gray-100">{{ $tiers->links() }}</div>
    </div>
</div>
@endsection
