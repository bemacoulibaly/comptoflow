@extends('layouts.app')
@section('title', 'Rapprochement')
@section('page-title', 'Rapprochement bancaire')
@section('topbar-actions')
    <a href="{{ route('rapprochement.create') }}" class="btn-primary text-xs flex items-center gap-1">
        <i class="ti ti-plus"></i> Nouveau
    </a>
@endsection
@section('content')
<div class="bg-white rounded-xl border border-gray-200 overflow-hidden">
    <table class="w-full text-xs">
        <thead class="bg-gray-50 border-b border-gray-200">
            <tr>
                <th class="th">Compte</th><th class="th">Période</th>
                <th class="th text-right">Solde relevé</th><th class="th text-right">Solde compta</th>
                <th class="th text-right">Écart</th><th class="th">Statut</th><th class="th"></th>
            </tr>
        </thead>
        <tbody class="divide-y divide-gray-100">
            @forelse($rapprochements as $r)
            <tr class="hover:bg-gray-50">
                <td class="td font-medium">{{ $r->compte->numero }} — {{ $r->compte->libelle }}</td>
                <td class="td text-gray-500">{{ $r->date_debut->format('d/m/Y') }} → {{ $r->date_fin->format('d/m/Y') }}</td>
                <td class="td text-right font-mono">{{ number_format($r->solde_releve,0,',',' ') }}</td>
                <td class="td text-right font-mono">{{ number_format($r->solde_comptable,0,',',' ') }}</td>
                <td class="td text-right font-mono {{ abs($r->ecart)<0.01?'text-emerald-600':'text-amber-600' }}">{{ number_format($r->ecart,0,',',' ') }}</td>
                <td class="td">@if($r->statut==='valide')<span class="badge-green">Validé</span>@else<span class="badge-amber">En cours</span>@endif</td>
                <td class="td"><a href="{{ route('rapprochement.show',$r) }}" class="text-gray-400 hover:text-blue-600"><i class="ti ti-eye"></i></a></td>
            </tr>
            @empty
            <tr><td colspan="7" class="td text-center text-gray-400 py-10"><i class="ti ti-arrows-exchange text-3xl block mb-2"></i>Aucun rapprochement.</td></tr>
            @endforelse
        </tbody>
    </table>
    <div class="px-4 py-3 border-t border-gray-100">{{ $rapprochements->links() }}</div>
</div>
@endsection
