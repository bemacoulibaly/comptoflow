@extends('layouts.app')
@section('title','Nouveau rapprochement')
@section('page-title','Nouveau rapprochement bancaire')
@section('content')
<div class="max-w-xl">
    <form method="POST" action="{{ route('rapprochement.store') }}" class="space-y-4">
        @csrf
        <div class="bg-white rounded-xl border border-gray-200 p-5 space-y-4">
            <div>
                <label class="label">Compte bancaire <span class="text-red-500">*</span></label>
                <select name="numero_compte" class="input" required>
                    @foreach($comptes as $c)
                    <option value="{{ $c->numero }}">{{ $c->numero }} — {{ $c->libelle }}</option>
                    @endforeach
                </select>
            </div>
            <div class="grid grid-cols-2 gap-4">
                <div>
                    <label class="label">Du <span class="text-red-500">*</span></label>
                    <input type="date" name="date_debut" class="input" required value="{{ now()->startOfMonth()->toDateString() }}">
                </div>
                <div>
                    <label class="label">Au <span class="text-red-500">*</span></label>
                    <input type="date" name="date_fin" class="input" required value="{{ now()->endOfMonth()->toDateString() }}">
                </div>
            </div>
            <div>
                <label class="label">Solde du relevé bancaire (FCFA) <span class="text-red-500">*</span></label>
                <input type="number" name="solde_releve" class="input" required placeholder="12 450 000" step="1">
            </div>
        </div>
        <div class="flex justify-end gap-3">
            <a href="{{ route('rapprochement.index') }}" class="btn-secondary text-sm">Annuler</a>
            <button type="submit" class="btn-primary text-sm"><i class="ti ti-check"></i> Créer</button>
        </div>
    </form>
</div>
@endsection
