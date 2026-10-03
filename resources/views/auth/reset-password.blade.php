<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Réinitialisation — ComptoFlow</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/@tabler/icons-webfont@3.0.0/dist/tabler-icons.min.css">
</head>
<body class="min-h-screen bg-gray-50 flex items-center justify-center">
<div class="w-full max-w-md px-4">
    <div class="text-center mb-8">
        <div class="inline-flex items-center gap-2 mb-2">
            <span class="w-3 h-3 rounded-full bg-emerald-500 inline-block"></span>
            <span class="text-2xl font-semibold text-gray-900">ComptoFlow</span>
        </div>
    </div>
    <div class="bg-white rounded-2xl border border-gray-200 shadow-sm p-8">
        <h1 class="text-lg font-semibold text-gray-900 mb-1">Nouveau mot de passe</h1>
        <p class="text-sm text-gray-400 mb-6">Choisissez un nouveau mot de passe sécurisé.</p>

        @if ($errors->any())
            <div class="alert-error mb-4"><i class="ti ti-alert-triangle"></i> {{ $errors->first() }}</div>
        @endif

        <form method="POST" action="{{ route('password.store') }}" class="space-y-4">
            @csrf
            <input type="hidden" name="token" value="{{ $request->route('token') }}">
            <div>
                <label class="label">Email</label>
                <input type="email" name="email" value="{{ old('email', $request->email) }}" required class="input">
            </div>
            <div>
                <label class="label">Nouveau mot de passe</label>
                <input type="password" name="password" required class="input" placeholder="8 caractères minimum">
            </div>
            <div>
                <label class="label">Confirmer le mot de passe</label>
                <input type="password" name="password_confirmation" required class="input">
            </div>
            <button type="submit" class="w-full btn-primary justify-center py-2.5 text-sm">
                <i class="ti ti-check"></i> Réinitialiser le mot de passe
            </button>
        </form>
    </div>
</div>
</body>
</html>
