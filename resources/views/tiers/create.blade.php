@extends('layouts.app')
@section('title', 'Nouveau tiers')
@section('page-title', 'Nouveau tiers')

@section('content')
<div class="max-w-2xl">
    <form method="POST" action="{{ route('tiers.store') }}" class="space-y-4">
        @csrf
        <div class="bg-white rounded-xl border border-gray-200 p-5 space-y-4">
            <div class="grid grid-cols-2 gap-4">
                <div class="col-span-2">
                    <label class="label">Type <span class="text-red-500">*</span></label>
                    <select name="type" class="input @error('type') input-error @enderror" required>
                        <option value="client"      @selected(old('type')==='client')>Client</option>
                        <option value="fournisseur" @selected(old('type')==='fournisseur')>Fournisseur</option>
                        <option value="les_deux"    @selected(old('type')==='les_deux')>Client & Fournisseur</option>
                    </select>
                    @error('type') <p class="field-error">{{ $message }}</p> @enderror
                </div>
                <div class="col-span-2">
                    <label class="label">Nom / Raison sociale <span class="text-red-500">*</span></label>
                    <input type="text" name="nom" value="{{ old('nom') }}" required
                           class="input @error('nom') input-error @enderror" placeholder="SARL Tana Import…">
                    @error('nom') <p class="field-error">{{ $message }}</p> @enderror
                </div>
                <div>
                    <label class="label">Email</label>
                    <input type="email" name="email" value="{{ old('email') }}" class="input" placeholder="contact@example.ci">
                </div>
                <div>
                    <label class="label">Téléphone</label>
                    <input type="text" name="telephone" value="{{ old('telephone') }}" class="input" placeholder="+225 07 00 00 00">
                </div>
                <div>
                    <label class="label">Ville</label>
                    <input type="text" name="ville" value="{{ old('ville') }}" class="input" placeholder="Abidjan">
                </div>
                <div>
                    <label class="label">N° contribuable</label>
                    <input type="text" name="numero_contribuable" value="{{ old('numero_contribuable') }}" class="input" placeholder="CI-ABJ-…">
                </div>
                <div class="col-span-2">
                    <label class="label">Adresse</label>
                    <input type="text" name="adresse" value="{{ old('adresse') }}" class="input" placeholder="Avenue Botreau-Roussel, Plateau">
                </div>
            </div>
        </div>
        <div class="flex justify-end gap-3">
            <a href="{{ route('tiers.index') }}" class="btn-secondary text-sm">Annuler</a>
            <button type="submit" class="btn-primary text-sm"><i class="ti ti-device-floppy"></i> Enregistrer</button>
        </div>
    </form>
</div>
@endsection
