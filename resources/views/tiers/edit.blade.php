@extends('layouts.app')
@section('title', 'Modifier ' . $tiers->nom)
@section('page-title', 'Modifier — ' . $tiers->nom)

@section('content')
<div class="max-w-2xl">
    <form method="POST" action="{{ route('tiers.update', $tiers) }}" class="space-y-4">
        @csrf @method('PUT')
        <div class="bg-white rounded-xl border border-gray-200 p-5 space-y-4">
            <div class="grid grid-cols-2 gap-4">
                <div class="col-span-2">
                    <label class="label">Type <span class="text-red-500">*</span></label>
                    <select name="type" class="input" required>
                        <option value="client"      @selected(old('type',$tiers->type)==='client')>Client</option>
                        <option value="fournisseur" @selected(old('type',$tiers->type)==='fournisseur')>Fournisseur</option>
                        <option value="les_deux"    @selected(old('type',$tiers->type)==='les_deux')>Client & Fournisseur</option>
                    </select>
                </div>
                <div class="col-span-2">
                    <label class="label">Nom / Raison sociale <span class="text-red-500">*</span></label>
                    <input type="text" name="nom" value="{{ old('nom', $tiers->nom) }}" required class="input">
                </div>
                <div>
                    <label class="label">Email</label>
                    <input type="email" name="email" value="{{ old('email', $tiers->email) }}" class="input">
                </div>
                <div>
                    <label class="label">Téléphone</label>
                    <input type="text" name="telephone" value="{{ old('telephone', $tiers->telephone) }}" class="input">
                </div>
                <div>
                    <label class="label">Ville</label>
                    <input type="text" name="ville" value="{{ old('ville', $tiers->ville) }}" class="input">
                </div>
                <div>
                    <label class="label">N° contribuable</label>
                    <input type="text" name="numero_contribuable" value="{{ old('numero_contribuable', $tiers->numero_contribuable) }}" class="input">
                </div>
                <div class="col-span-2">
                    <label class="label">Adresse</label>
                    <input type="text" name="adresse" value="{{ old('adresse', $tiers->adresse) }}" class="input">
                </div>
            </div>
        </div>
        <div class="flex justify-end gap-3">
            <a href="{{ route('tiers.show', $tiers) }}" class="btn-secondary text-sm">Annuler</a>
            <button type="submit" class="btn-primary text-sm"><i class="ti ti-device-floppy"></i> Sauvegarder</button>
        </div>
    </form>
</div>
@endsection
