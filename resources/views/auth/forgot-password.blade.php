<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Mot de passe oublié — ComptoFlow</title>
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
        <h1 class="text-lg font-semibold text-gray-900 mb-1">Mot de passe oublié</h1>
        <p class="text-sm text-gray-400 mb-6">Entrez votre email pour recevoir un lien de réinitialisation.</p>

        @if (session('status'))
            <div class="alert-success mb-4"><i class="ti ti-circle-check"></i> {{ session('status') }}</div>
        @endif

        @if ($errors->any())
            <div class="alert-error mb-4"><i class="ti ti-alert-triangle"></i> {{ $errors->first() }}</div>
        @endif

        <form method="POST" action="{{ route('password.email') }}" class="space-y-4">
            @csrf
            <div>
                <label class="label" for="email">Adresse email</label>
                <input id="email" type="email" name="email" value="{{ old('email') }}"
                       required autofocus class="input" placeholder="votre@email.ci">
            </div>
            <button type="submit" class="w-full btn-primary justify-center py-2.5 text-sm">
                <i class="ti ti-send"></i> Envoyer le lien
            </button>
        </form>

        <div class="mt-4 text-center">
            <a href="{{ route('login') }}" class="text-sm text-emerald-600 hover:underline flex items-center justify-center gap-1">
                <i class="ti ti-arrow-left text-xs"></i> Retour à la connexion
            </a>
        </div>
    </div>
</div>
</body>
</html>
