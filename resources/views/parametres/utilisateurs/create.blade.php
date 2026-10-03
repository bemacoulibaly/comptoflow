@extends('layouts.app')

@section('title', 'Créer un compte')
@section('page-title', 'Créer un compte')

@section('topbar-actions')
    <a href="{{ route('parametres.utilisateurs.index') }}" class="btn-secondary text-xs flex items-center gap-1">
        <i class="ti ti-arrow-left"></i> Retour
    </a>
@endsection

@section('content')
<div class="max-w-xl">

    <div class="alert-info mb-4">
        <i class="ti ti-info-circle"></i>
        Choisissez un mot de passe initial et communiquez-le vous-même au collaborateur (en main propre ou par un canal sécurisé).
        Aucun email automatique n'est envoyé.
    </div>

    <form method="POST" action="{{ route('parametres.utilisateurs.store') }}" class="space-y-4">
        @csrf
        <div class="bg-white rounded-xl border border-gray-200 p-5 space-y-4">

            <div class="grid grid-cols-2 gap-4">
                <div>
                    <label class="label">Prénom <span class="text-red-500">*</span></label>
                    <input type="text" name="prenom" value="{{ old('prenom') }}" required
                           class="input @error('prenom') input-error @enderror">
                    @error('prenom')<p class="field-error">{{ $message }}</p>@enderror
                </div>
                <div>
                    <label class="label">Nom <span class="text-red-500">*</span></label>
                    <input type="text" name="nom" value="{{ old('nom') }}" required
                           class="input @error('nom') input-error @enderror">
                    @error('nom')<p class="field-error">{{ $message }}</p>@enderror
                </div>
            </div>

            <div>
                <label class="label">Email <span class="text-red-500">*</span></label>
                <input type="email" name="email" value="{{ old('email') }}" required
                       class="input @error('email') input-error @enderror" placeholder="prenom.nom@cabinet.ci">
                @error('email')<p class="field-error">{{ $message }}</p>@enderror
            </div>

            <div>
                <label class="label">Rôle système <span class="text-red-500">*</span></label>
                <select name="role" class="input @error('role') input-error @enderror" required>
                    <option value="editeur" @selected(old('role')==='editeur')>Éditeur — peut saisir et modifier</option>
                    <option value="lecteur" @selected(old('role')==='lecteur')>Lecteur — consultation uniquement</option>
                    <option value="admin"   @selected(old('role')==='admin')>Administrateur — accès complet</option>
                </select>
                @error('role')<p class="field-error">{{ $message }}</p>@enderror
                <p class="text-[11px] text-gray-400 mt-1">Détermine le niveau d'accès de base. L'administrateur a toujours accès à tout, sans exception.</p>
            </div>

            @if($roles->isNotEmpty())
            <div>
                <label class="label">Rôle personnalisé (optionnel)</label>
                <select name="role_id" class="input @error('role_id') input-error @enderror">
                    <option value="">— Aucun, utiliser uniquement le rôle système ci-dessus —</option>
                    @foreach($roles as $r)
                    <option value="{{ $r->id }}" @selected(old('role_id') == $r->id)>
                        {{ $r->nom }} @if($r->est_systeme) (système) @endif
                    </option>
                    @endforeach
                </select>
                @error('role_id')<p class="field-error">{{ $message }}</p>@enderror
                <p class="text-[11px] text-gray-400 mt-1">Si choisi, ce rôle personnalisé détermine les pages accessibles (sauf pour un compte Administrateur, qui garde un accès total).</p>
            </div>
            @endif

            <div class="grid grid-cols-2 gap-4 pt-2 border-t border-gray-100">
                <div>
                    <label class="label">Mot de passe initial <span class="text-red-500">*</span></label>
                    <input type="password" name="password" required
                           class="input @error('password') input-error @enderror">
                    @error('password')<p class="field-error">{{ $message }}</p>@enderror
                </div>
                <div>
                    <label class="label">Confirmer le mot de passe <span class="text-red-500">*</span></label>
                    <input type="password" name="password_confirmation" required class="input">
                </div>
            </div>
        </div>

        <div class="flex justify-end gap-3">
            <a href="{{ route('parametres.utilisateurs.index') }}" class="btn-secondary text-sm">Annuler</a>
            <button type="submit" class="btn-primary text-sm"><i class="ti ti-user-plus"></i> Créer le compte</button>
        </div>
    </form>
</div>
@endsection
