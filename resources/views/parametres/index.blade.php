@extends('layouts.app')
@section('title','Paramètres')
@section('page-title','Paramètres')

@section('content')
<div class="grid grid-cols-2 gap-4">

    {{-- Informations société --}}
    <div class="space-y-4">
        <div class="bg-white rounded-xl border border-gray-200 overflow-hidden">
            <div class="px-4 py-3 border-b border-gray-100 flex items-center gap-2">
                <i class="ti ti-building text-gray-400"></i>
                <span class="text-sm font-semibold text-gray-800">Informations société</span>
            </div>
            <form method="POST" action="{{ route('parametres.societe') }}" class="p-4 space-y-3">
                @csrf @method('PATCH')
                <div>
                    <label class="label">Raison sociale <span class="text-red-500">*</span></label>
                    <input type="text" name="raison_sociale" value="{{ old('raison_sociale', $societe->raison_sociale) }}" class="input @error('raison_sociale') input-error @enderror" required>
                    @error('raison_sociale')<p class="field-error">{{ $message }}</p>@enderror
                </div>
                <div>
                    <label class="label">N° contribuable <span class="text-red-500">*</span></label>
                    <input type="text" name="numero_contribuable" value="{{ old('numero_contribuable', $societe->numero_contribuable) }}" class="input" required>
                </div>
                <div class="grid grid-cols-2 gap-3">
                    <div>
                        <label class="label">Régime fiscal</label>
                        <select name="regime_fiscal" class="input">
                            <option value="reel_simplifie" @selected($societe->regime_fiscal==='reel_simplifie')>Réel simplifié</option>
                            <option value="reel_normal"    @selected($societe->regime_fiscal==='reel_normal')>Réel normal</option>
                            <option value="forfait"        @selected($societe->regime_fiscal==='forfait')>Forfait</option>
                        </select>
                    </div>
                    <div>
                        <label class="label">Devise</label>
                        <select name="devise" class="input">
                            <option value="XOF" @selected($societe->devise==='XOF')>FCFA (XOF)</option>
                            <option value="EUR" @selected($societe->devise==='EUR')>Euro (EUR)</option>
                            <option value="USD" @selected($societe->devise==='USD')>Dollar (USD)</option>
                        </select>
                    </div>
                </div>
                <div>
                    <label class="label">Taux TVA par défaut (%)</label>
                    <input type="number" name="taux_tva" value="{{ old('taux_tva', $societe->taux_tva) }}" class="input" step="0.01" min="0" max="100">
                    <p class="text-[10px] text-gray-400 mt-1">Taux standard CI : 18%</p>
                </div>
                <div>
                    <label class="label">Adresse</label>
                    <input type="text" name="adresse" value="{{ old('adresse', $societe->adresse) }}" class="input" placeholder="Avenue Botreau-Roussel…">
                </div>
                <div class="grid grid-cols-2 gap-3">
                    <div>
                        <label class="label">Ville</label>
                        <input type="text" name="ville" value="{{ old('ville', $societe->ville) }}" class="input" placeholder="Abidjan">
                    </div>
                    <div>
                        <label class="label">Téléphone</label>
                        <input type="text" name="telephone" value="{{ old('telephone', $societe->telephone) }}" class="input" placeholder="+225 07 00 00 00">
                    </div>
                </div>
                <div>
                    <label class="label">Email</label>
                    <input type="email" name="email" value="{{ old('email', $societe->email) }}" class="input">
                </div>
                <div class="flex justify-end pt-2">
                    <button type="submit" class="btn-primary text-xs">
                        <i class="ti ti-device-floppy"></i> Sauvegarder
                    </button>
                </div>
            </form>
        </div>
    </div>

    <div class="space-y-4">

        {{-- Comptes utilisateurs --}}
        <div class="bg-white rounded-xl border border-gray-200 overflow-hidden">
            <div class="px-4 py-3 border-b border-gray-100 flex items-center gap-2">
                <i class="ti ti-users text-gray-400"></i>
                <span class="text-sm font-semibold text-gray-800 flex-1">Comptes utilisateurs</span>
                <span class="badge-gray">{{ $nbUsers }}</span>
            </div>
            <div class="px-4 py-3">
                <p class="text-xs text-gray-500">Gérez les comptes de votre équipe : rôles, mots de passe, et activité individuelle de chacun.</p>
                <a href="{{ route('parametres.utilisateurs.index') }}" class="btn-secondary text-xs mt-3 inline-flex items-center gap-1">
                    <i class="ti ti-settings" aria-hidden="true"></i> Gérer les comptes
                </a>
                @if(auth()->user()->isAdmin())
                <a href="{{ route('parametres.utilisateurs.create') }}" class="btn-primary text-xs mt-3 ml-2 inline-flex items-center gap-1">
                    <i class="ti ti-user-plus" aria-hidden="true"></i> Créer un compte
                </a>
                @endif
            </div>
        </div>

        {{-- Notifications --}}
        <div class="bg-white rounded-xl border border-gray-200 overflow-hidden">
            <div class="px-4 py-3 border-b border-gray-100 flex items-center gap-2">
                <i class="ti ti-bell text-gray-400"></i>
                <span class="text-sm font-semibold text-gray-800">Notifications</span>
            </div>
            @foreach([
                ['Factures en retard',    'Alerte quand une facture dépasse son échéance'],
                ['Déclaration TVA',       'Rappel 15 jours avant l\'échéance TVA'],
                ['Écritures à valider',   'Résumé quotidien des brouillons en attente'],
                ['Rapprochement mensuel', 'Rappel de clôture mensuelle'],
            ] as [$titre, $desc])
            <div class="flex items-center gap-3 px-4 py-3 border-b border-gray-100 last:border-0">
                <div class="flex-1 min-w-0">
                    <p class="text-xs font-medium text-gray-900">{{ $titre }}</p>
                    <p class="text-[11px] text-gray-400">{{ $desc }}</p>
                </div>
                <label class="relative inline-flex items-center cursor-pointer">
                    <input type="checkbox" class="sr-only peer" checked>
                    <div class="w-9 h-5 bg-gray-200 peer-checked:bg-emerald-600 rounded-full transition
                                after:content-[''] after:absolute after:top-0.5 after:left-0.5
                                after:bg-white after:rounded-full after:h-4 after:w-4 after:transition
                                peer-checked:after:translate-x-4"></div>
                </label>
            </div>
            @endforeach
        </div>

        {{-- Plan comptable --}}
        <div class="bg-white rounded-xl border border-gray-200 overflow-hidden">
            <div class="px-4 py-3 border-b border-gray-100 flex items-center gap-2">
                <i class="ti ti-book text-gray-400"></i>
                <span class="text-sm font-semibold text-gray-800 flex-1">Plan comptable OHADA</span>
                <span class="badge-green">{{ auth()->user()->societe->comptes->count() }} comptes</span>
            </div>
            <div class="px-4 py-3">
                <p class="text-xs text-gray-500">Plan comptable SYSCOHADA révisé — Côte d'Ivoire.</p>
                <p class="text-[11px] text-gray-400 mt-1">7 classes · 80+ comptes standards chargés automatiquement.</p>
                <a href="#" class="text-xs text-emerald-600 hover:underline mt-2 inline-flex items-center gap-1">
                    <i class="ti ti-download"></i> Exporter le plan comptable
                </a>
            </div>
        </div>
    </div>
</div>

@endsection
