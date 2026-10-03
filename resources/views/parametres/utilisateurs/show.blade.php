@extends('layouts.app')

@section('title', $user->nom_complet)
@section('page-title', 'Compte — ' . $user->nom_complet)

@section('topbar-actions')
    <a href="{{ route('parametres.utilisateurs.index') }}" class="btn-secondary text-xs flex items-center gap-1">
        <i class="ti ti-arrow-left"></i> Retour
    </a>
@endsection

@section('content')
<div class="space-y-4 max-w-4xl">

    {{-- En-tête --}}
    <div class="bg-white rounded-xl border border-gray-200 p-5">
        <div class="flex items-center gap-4">
            <div class="w-16 h-16 rounded-full bg-blue-100 flex items-center justify-center text-blue-700 text-xl font-semibold flex-shrink-0">
                {{ $user->initiales }}
            </div>
            <div class="flex-1">
                <h2 class="text-lg font-semibold text-gray-900">{{ $user->nom_complet }}</h2>
                <p class="text-sm text-gray-400">{{ $user->email }}</p>
                <p class="text-[11px] text-gray-400 mt-1">Membre depuis le {{ $user->created_at->format('d/m/Y') }}</p>
            </div>
            <div class="flex items-center gap-2">
                @if(!$user->actif)
                <span class="badge-red">Désactivé</span>
                @endif
                @if($user->role === 'admin')   <span class="badge-green">Administrateur</span>
                @elseif($user->roleAssigne)
                    <span class="badge-{{ $user->roleAssigne->couleur }}">{{ $user->roleAssigne->nom }}</span>
                @elseif($user->role === 'editeur') <span class="badge-blue">Éditeur</span>
                @else <span class="badge-gray">Lecteur</span>
                @endif
            </div>
        </div>
    </div>

    {{-- KPIs activité --}}
    <div class="grid grid-cols-2 gap-3">
        <div class="bg-white rounded-xl border border-gray-200 p-4">
            <p class="text-xs text-gray-400 mb-1">Écritures saisies</p>
            <p class="text-xl font-semibold font-mono">{{ $user->ecritures_count }}</p>
        </div>
        <div class="bg-white rounded-xl border border-gray-200 p-4">
            <p class="text-xs text-gray-400 mb-1">Factures créées</p>
            <p class="text-xl font-semibold font-mono">{{ $user->factures_count }}</p>
        </div>
    </div>

    @if($user->id !== auth()->id())
    <div class="grid grid-cols-2 gap-4">

        {{-- Changer le rôle --}}
        <div class="bg-white rounded-xl border border-gray-200 overflow-hidden">
            <div class="px-4 py-3 border-b border-gray-100 flex items-center gap-2">
                <i class="ti ti-shield text-gray-400"></i>
                <span class="text-sm font-semibold text-gray-800">Rôle</span>
            </div>
            <form method="POST" action="{{ route('parametres.utilisateurs.role', $user) }}" class="p-4 space-y-3">
                @csrf @method('PATCH')
                <div>
                    <label class="label">Rôle système</label>
                    <select name="role" class="input">
                        <option value="admin"   @selected($user->role==='admin')>Administrateur — accès complet</option>
                        <option value="editeur" @selected($user->role==='editeur')>Éditeur — peut saisir et modifier</option>
                        <option value="lecteur" @selected($user->role==='lecteur')>Lecteur — consultation uniquement</option>
                    </select>
                    @error('role')<p class="field-error">{{ $message }}</p>@enderror
                </div>
                @if($roles->isNotEmpty())
                <div>
                    <label class="label">Rôle personnalisé</label>
                    <select name="role_id" class="input">
                        <option value="">— Aucun —</option>
                        @foreach($roles as $r)
                        <option value="{{ $r->id }}" @selected($user->role_id == $r->id)>{{ $r->nom }}</option>
                        @endforeach
                    </select>
                    @error('role_id')<p class="field-error">{{ $message }}</p>@enderror
                </div>
                @endif
                <div class="flex justify-end">
                    <button type="submit" class="btn-primary text-xs"><i class="ti ti-check"></i> Mettre à jour</button>
                </div>
            </form>
        </div>

        {{-- Réinitialiser le mot de passe --}}
        <div class="bg-white rounded-xl border border-gray-200 overflow-hidden">
            <div class="px-4 py-3 border-b border-gray-100 flex items-center gap-2">
                <i class="ti ti-key text-gray-400"></i>
                <span class="text-sm font-semibold text-gray-800">Réinitialiser le mot de passe</span>
            </div>
            <form method="POST" action="{{ route('parametres.utilisateurs.password', $user) }}" class="p-4 space-y-3">
                @csrf @method('PUT')
                <div>
                    <label class="label">Nouveau mot de passe</label>
                    <input type="password" name="password" required class="input @error('password') input-error @enderror">
                    @error('password')<p class="field-error">{{ $message }}</p>@enderror
                </div>
                <div>
                    <label class="label">Confirmer</label>
                    <input type="password" name="password_confirmation" required class="input">
                </div>
                <div class="flex justify-end">
                    <button type="submit" class="btn-primary text-xs"><i class="ti ti-key"></i> Réinitialiser</button>
                </div>
            </form>
        </div>
    </div>

    {{-- Activer / Désactiver (réversible) --}}
    <div class="bg-white rounded-xl border border-amber-200 overflow-hidden">
        <div class="px-4 py-3 border-b border-amber-100 flex items-center gap-2">
            <i class="ti ti-power text-amber-500"></i>
            <span class="text-sm font-semibold text-amber-700">{{ $user->actif ? 'Désactiver temporairement' : 'Réactiver ce compte' }}</span>
        </div>
        <div class="p-4 flex items-center justify-between">
            <p class="text-xs text-gray-500">
                @if($user->actif)
                    {{ $user->prenom }} ne pourra plus se connecter, mais le compte et tout son historique restent visibles et réversibles à tout moment.
                @else
                    Ce compte est actuellement désactivé. Le réactiver permettra à {{ $user->prenom }} de se reconnecter immédiatement.
                @endif
            </p>
            <form method="POST" action="{{ route('parametres.utilisateurs.statut', $user) }}">
                @csrf @method('PATCH')
                <button class="{{ $user->actif ? 'btn-secondary' : 'btn-primary' }} text-xs flex items-center gap-1 flex-shrink-0">
                    <i class="ti ti-power"></i> {{ $user->actif ? 'Désactiver' : 'Réactiver' }}
                </button>
            </form>
        </div>
    </div>

    {{-- Zone dangereuse --}}
    <div class="bg-white rounded-xl border border-red-200 overflow-hidden">
        <div class="px-4 py-3 border-b border-red-100 flex items-center gap-2">
            <i class="ti ti-alert-triangle text-red-400"></i>
            <span class="text-sm font-semibold text-red-700">Supprimer ce compte</span>
        </div>
        <div class="p-4 flex items-center justify-between">
            <p class="text-xs text-gray-500">
                Le compte sera retiré de la liste active. L'historique (écritures, factures, journal d'activité) reste conservé et consultable.
            </p>
            <form method="POST" action="{{ route('parametres.utilisateurs.destroy', $user) }}"
                  onsubmit="return confirm('Supprimer le compte de {{ $user->nom_complet }} ?')">
                @csrf @method('DELETE')
                <button class="btn-danger text-xs flex items-center gap-1 flex-shrink-0">
                    <i class="ti ti-trash"></i> Supprimer
                </button>
            </form>
        </div>
    </div>
    @else
    <div class="alert-info">
        <i class="ti ti-info-circle"></i> Ceci est votre propre compte. Rendez-vous sur <a href="{{ route('profile.show') }}" class="underline font-medium">Mon profil</a> pour modifier vos informations ou votre mot de passe.
    </div>
    @endif

    {{-- Activité --}}
    <div class="grid grid-cols-2 gap-4">
        <div class="bg-white rounded-xl border border-gray-200 overflow-hidden">
            <div class="px-4 py-3 border-b border-gray-100 flex items-center gap-2">
                <i class="ti ti-pencil text-gray-400"></i>
                <span class="text-sm font-semibold text-gray-800">Dernières écritures</span>
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
                <div class="px-4 py-8 text-center text-xs text-gray-400">Aucune écriture saisie.</div>
                @endforelse
            </div>
        </div>

        <div class="bg-white rounded-xl border border-gray-200 overflow-hidden">
            <div class="px-4 py-3 border-b border-gray-100 flex items-center gap-2">
                <i class="ti ti-file-invoice text-gray-400"></i>
                <span class="text-sm font-semibold text-gray-800">Dernières factures</span>
            </div>
            <div class="divide-y divide-gray-100">
                @forelse($dernieresFactures as $f)
                <a href="{{ route('factures.show', $f) }}" class="flex items-center gap-3 px-4 py-2.5 hover:bg-gray-50 transition">
                    <div class="flex-1 min-w-0">
                        <p class="text-xs font-medium text-gray-900 truncate">{{ $f->tiers->nom }}</p>
                        <p class="text-[11px] text-gray-400 font-mono">{{ $f->numero }} · {{ $f->date_emission->format('d/m/Y') }}</p>
                    </div>
                    <span class="font-mono text-xs font-medium">{{ number_format($f->montant_ttc, 0, ',', ' ') }} F</span>
                </a>
                @empty
                <div class="px-4 py-8 text-center text-xs text-gray-400">Aucune facture créée.</div>
                @endforelse
            </div>
        </div>
    </div>
    {{-- Journal d'activité récent --}}
    <div class="bg-white rounded-xl border border-gray-200 overflow-hidden">
        <div class="px-4 py-3 border-b border-gray-100 flex items-center gap-2">
            <i class="ti ti-history text-gray-400"></i>
            <span class="text-sm font-semibold text-gray-800 flex-1">Activité récente</span>
            <a href="{{ route('parametres.utilisateurs.activite', $user) }}" class="text-xs text-emerald-600 hover:underline">
                Voir tout l'historique →
            </a>
        </div>
        <div class="divide-y divide-gray-100">
            @forelse($derniersLogs as $log)
            <div class="px-4 py-2.5 text-xs flex items-center justify-between">
                <span class="text-gray-700">{{ $log->description }}</span>
                <span class="text-gray-400 flex-shrink-0 ml-3">{{ $log->created_at->format('d/m/Y H:i') }}</span>
            </div>
            @empty
            <div class="px-4 py-8 text-center text-xs text-gray-400">Aucune activité enregistrée pour le moment.</div>
            @endforelse
        </div>
    </div>

</div>
@endsection
