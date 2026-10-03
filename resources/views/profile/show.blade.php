@extends('layouts.app')

@section('title', 'Mon profil')
@section('page-title', 'Mon profil')

@section('content')
<div class="space-y-4 max-w-4xl">

    {{-- En-tête profil --}}
    <div class="bg-white rounded-xl border border-gray-200 p-5">
        <div class="flex items-center gap-4">
            <div class="w-16 h-16 rounded-full bg-blue-100 flex items-center justify-center text-blue-700 text-xl font-semibold flex-shrink-0">
                {{ $user->initiales }}
            </div>
            <div class="flex-1">
                <h2 class="text-lg font-semibold text-gray-900">{{ $user->nom_complet }}</h2>
                <p class="text-sm text-gray-400">{{ $user->email }}</p>
            </div>
            <div>
                @if($user->role === 'admin')   <span class="badge-green">Administrateur</span>
                @elseif($user->role === 'editeur') <span class="badge-blue">Éditeur</span>
                @else <span class="badge-gray">Lecteur</span>
                @endif
            </div>
        </div>
    </div>

    {{-- KPIs d'activité --}}
    <div class="grid grid-cols-4 gap-3">
        <div class="bg-white rounded-xl border border-gray-200 p-4">
            <p class="text-xs text-gray-400 mb-1">Écritures saisies</p>
            <p class="text-xl font-semibold font-mono">{{ $user->ecritures_count }}</p>
        </div>
        <div class="bg-white rounded-xl border border-gray-200 p-4">
            <p class="text-xs text-gray-400 mb-1">Validées</p>
            <p class="text-xl font-semibold font-mono text-emerald-600">{{ $ecrituresValidees }}</p>
        </div>
        <div class="bg-white rounded-xl border border-gray-200 p-4">
            <p class="text-xs text-gray-400 mb-1">En brouillon</p>
            <p class="text-xl font-semibold font-mono {{ $ecrituresBrouillon > 0 ? 'text-amber-600' : 'text-gray-900' }}">
                {{ $ecrituresBrouillon }}
            </p>
        </div>
        <div class="bg-white rounded-xl border border-gray-200 p-4">
            <p class="text-xs text-gray-400 mb-1">Total facturé (clients)</p>
            <p class="text-xl font-semibold font-mono text-emerald-600">{{ number_format($totalFacture, 0, ',', ' ') }}</p>
        </div>
    </div>

    <div class="grid grid-cols-2 gap-4">

        {{-- Informations personnelles --}}
        <div class="bg-white rounded-xl border border-gray-200 overflow-hidden">
            <div class="px-4 py-3 border-b border-gray-100 flex items-center gap-2">
                <i class="ti ti-user text-gray-400"></i>
                <span class="text-sm font-semibold text-gray-800">Informations personnelles</span>
            </div>
            <form method="POST" action="{{ route('profile.update') }}" class="p-4 space-y-3">
                @csrf @method('PATCH')
                <div class="grid grid-cols-2 gap-3">
                    <div>
                        <label class="label">Prénom</label>
                        <input type="text" name="prenom" value="{{ old('prenom', $user->prenom) }}" class="input @error('prenom') input-error @enderror" required>
                        @error('prenom')<p class="field-error">{{ $message }}</p>@enderror
                    </div>
                    <div>
                        <label class="label">Nom</label>
                        <input type="text" name="nom" value="{{ old('nom', $user->nom) }}" class="input @error('nom') input-error @enderror" required>
                        @error('nom')<p class="field-error">{{ $message }}</p>@enderror
                    </div>
                </div>
                <div>
                    <label class="label">Email</label>
                    <input type="email" name="email" value="{{ old('email', $user->email) }}" class="input @error('email') input-error @enderror" required>
                    @error('email')<p class="field-error">{{ $message }}</p>@enderror
                </div>
                <div class="flex justify-end pt-1">
                    <button type="submit" class="btn-primary text-xs"><i class="ti ti-device-floppy"></i> Enregistrer</button>
                </div>
            </form>
        </div>

        {{-- Changer le mot de passe --}}
        <div class="bg-white rounded-xl border border-gray-200 overflow-hidden">
            <div class="px-4 py-3 border-b border-gray-100 flex items-center gap-2">
                <i class="ti ti-lock text-gray-400"></i>
                <span class="text-sm font-semibold text-gray-800">Mot de passe</span>
            </div>
            <form method="POST" action="{{ route('profile.password') }}" class="p-4 space-y-3">
                @csrf @method('PUT')
                <div>
                    <label class="label">Mot de passe actuel</label>
                    <input type="password" name="current_password" class="input @error('current_password') input-error @enderror" required>
                    @error('current_password')<p class="field-error">{{ $message }}</p>@enderror
                </div>
                <div>
                    <label class="label">Nouveau mot de passe</label>
                    <input type="password" name="password" class="input @error('password') input-error @enderror" required>
                    @error('password')<p class="field-error">{{ $message }}</p>@enderror
                </div>
                <div>
                    <label class="label">Confirmer le nouveau mot de passe</label>
                    <input type="password" name="password_confirmation" class="input" required>
                </div>
                <div class="flex justify-end pt-1">
                    <button type="submit" class="btn-primary text-xs"><i class="ti ti-check"></i> Changer le mot de passe</button>
                </div>
            </form>
        </div>
    </div>

    {{-- Mon activité --}}
    <div class="grid grid-cols-2 gap-4">

        {{-- Dernières écritures --}}
        <div class="bg-white rounded-xl border border-gray-200 overflow-hidden">
            <div class="px-4 py-3 border-b border-gray-100 flex items-center gap-2">
                <i class="ti ti-pencil text-gray-400"></i>
                <span class="text-sm font-semibold text-gray-800 flex-1">Mes dernières écritures</span>
                <a href="{{ route('ecritures.index') }}" class="text-xs text-emerald-600 hover:underline">Tout voir</a>
            </div>
            <div class="divide-y divide-gray-100">
                @forelse($dernieresEcritures as $e)
                <a href="{{ route('ecritures.show', $e) }}" class="flex items-center gap-3 px-4 py-2.5 hover:bg-gray-50 transition">
                    <div class="flex-1 min-w-0">
                        <p class="text-xs font-medium text-gray-900 truncate">{{ $e->libelle }}</p>
                        <p class="text-[11px] text-gray-400">{{ $e->numero_piece }} · {{ $e->date_ecriture->format('d/m/Y') }}</p>
                    </div>
                    @if($e->statut === 'validee') <span class="badge-green">Validée</span>
                    @elseif($e->statut === 'annulee') <span class="badge-red">Annulée</span>
                    @else <span class="badge-amber">Brouillon</span>
                    @endif
                </a>
                @empty
                <div class="px-4 py-8 text-center text-xs text-gray-400">
                    <i class="ti ti-inbox text-2xl block mb-2"></i> Aucune écriture saisie.
                </div>
                @endforelse
            </div>
        </div>

        {{-- Dernières factures --}}
        <div class="bg-white rounded-xl border border-gray-200 overflow-hidden">
            <div class="px-4 py-3 border-b border-gray-100 flex items-center gap-2">
                <i class="ti ti-file-invoice text-gray-400"></i>
                <span class="text-sm font-semibold text-gray-800 flex-1">Mes dernières factures</span>
                <a href="{{ route('factures.index') }}" class="text-xs text-emerald-600 hover:underline">Tout voir</a>
            </div>
            <div class="divide-y divide-gray-100">
                @forelse($dernieresFactures as $f)
                <a href="{{ route('factures.show', $f) }}" class="flex items-center gap-3 px-4 py-2.5 hover:bg-gray-50 transition">
                    <div class="flex-1 min-w-0">
                        <p class="text-xs font-medium text-gray-900 truncate">{{ $f->tiers->nom }}</p>
                        <p class="text-[11px] text-gray-400 font-mono">{{ $f->numero }} · {{ $f->date_emission->format('d/m/Y') }}</p>
                    </div>
                    <span class="font-mono text-xs font-medium">{{ number_format($f->montant_ttc, 0, ',', ' ') }} F</span>
                    @if($f->statut === 'payee') <span class="badge-green">Payée</span>
                    @elseif($f->statut === 'partielle') <span class="badge-amber">Partielle</span>
                    @elseif($f->statut === 'en_retard') <span class="badge-red">Retard</span>
                    @else <span class="badge-blue">En cours</span>
                    @endif
                </a>
                @empty
                <div class="px-4 py-8 text-center text-xs text-gray-400">
                    <i class="ti ti-file-off text-2xl block mb-2"></i> Aucune facture créée.
                </div>
                @endforelse
            </div>
        </div>
    </div>

</div>
@endsection
