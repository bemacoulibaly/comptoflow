<!DOCTYPE html>
<html lang="fr" class="">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'ComptoFlow') — ComptoFlow</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    {{-- Anti-flash mode sombre : s'exécute avant tout rendu pour éviter le flash blanc --}}
    <script>
        (function() {
            var dark = localStorage.getItem('darkMode') === '1'
                || (!localStorage.getItem('darkMode') && window.matchMedia('(prefers-color-scheme: dark)').matches);
            if (dark) document.documentElement.classList.add('dark');
        })();
    </script>
    <script>
        window.SOCIETE_ID = {{ auth()->check() ? auth()->user()->societe_id : "null" }};
        window.CURRENT_USER_ID = {{ auth()->check() ? auth()->id() : "null" }};
    </script>
</head>
<body class="bg-gray-50 dark:bg-gray-900 text-gray-900 dark:text-gray-100 antialiased">

<div class="flex h-screen overflow-hidden">

    {{-- ── Sidebar ─────────────────────────────────────── --}}
    <aside class="w-56 flex-shrink-0 bg-white dark:bg-gray-800 border-r border-gray-200 dark:border-gray-700 flex flex-col">

        <div class="px-4 py-4 border-b border-gray-200 flex items-center gap-2">
            <span class="w-2.5 h-2.5 rounded-full bg-emerald-500 inline-block"></span>
            <span class="text-base font-semibold text-gray-900">ComptoFlow</span>
        </div>

        <nav class="flex-1 px-2 py-3 overflow-y-auto space-y-0.5">

            <p class="px-2 pt-1 pb-0.5 text-[10px] uppercase tracking-widest text-gray-400 font-medium">Principal</p>

            @if(auth()->user()->peutAccederA('dashboard'))
            <x-nav-item route="dashboard" icon="ti-layout-dashboard">Tableau de bord</x-nav-item>
            @endif
            @if(auth()->user()->peutAccederA('ecritures'))
            <x-nav-item route="ecritures.index" icon="ti-pencil">
                Écritures
                @if($ecrituresBrouillon ?? 0)
                    <span class="ml-auto text-[10px] font-semibold bg-emerald-100 text-emerald-800 px-1.5 py-0.5 rounded-full">
                        {{ $ecrituresBrouillon }}
                    </span>
                @endif
            </x-nav-item>
            @endif
            @if(auth()->user()->peutAccederA('factures'))
            <x-nav-item route="factures.index"       icon="ti-file-invoice">Factures</x-nav-item>
            @endif
            @if(auth()->user()->peutAccederA('rapprochement'))
            <x-nav-item route="rapprochement.index"  icon="ti-arrows-exchange">Rapprochement</x-nav-item>
            @endif

            @if(auth()->user()->peutAccederA('bilan') || auth()->user()->peutAccederA('tva'))
            <p class="px-2 pt-3 pb-0.5 text-[10px] uppercase tracking-widest text-gray-400 font-medium">Rapports</p>
            @endif
            @if(auth()->user()->peutAccederA('bilan'))
            <x-nav-item route="rapports.bilan"    icon="ti-scale">Bilan</x-nav-item>
            <x-nav-item route="rapports.resultat" icon="ti-chart-bar">Résultat</x-nav-item>
            <x-nav-item route="previsions.index"  icon="ti-chart-arrows-vertical">Prévisions</x-nav-item>
            @endif
            @if(auth()->user()->peutAccederA('tva'))
            <x-nav-item route="tva.index"         icon="ti-receipt-tax">TVA</x-nav-item>
            @endif

            @if(auth()->user()->peutAccederA('tiers') || auth()->user()->peutAccederA('calendrier') || auth()->user()->peutAccederA('parametres'))
            <p class="px-2 pt-3 pb-0.5 text-[10px] uppercase tracking-widest text-gray-400 font-medium">Gestion</p>
            @endif
            @if(auth()->user()->peutAccederA('tiers'))
            <x-nav-item route="tiers.index"       icon="ti-users">Tiers</x-nav-item>
            @endif
            @if(auth()->user()->peutAccederA('calendrier'))
            <x-nav-item route="calendrier.index"  icon="ti-calendar">Calendrier</x-nav-item>
            @endif
            @if(auth()->user()->peutAccederA('parametres'))
            <x-nav-item route="parametres.index"  icon="ti-settings">Paramètres</x-nav-item>
            @endif
        </nav>

        {{-- Profil utilisateur --}}
        <div class="border-t border-gray-200 px-2 py-3 space-y-0.5">
            <a href="{{ route('profile.show') }}"
               class="flex items-center gap-2 px-2 py-1.5 rounded-lg hover:bg-gray-100 transition
                      {{ request()->routeIs('profile.*') ? 'bg-gray-100' : '' }}">
                <div class="w-7 h-7 rounded-full bg-blue-100 flex items-center justify-center text-blue-700 text-xs font-semibold flex-shrink-0">
                    {{ auth()->user()->initiales }}
                </div>
                <div class="min-w-0 flex-1">
                    <p class="text-xs font-medium text-gray-900 truncate">{{ auth()->user()->nom_complet }}</p>
                    <p class="text-[11px] text-gray-400 truncate">{{ auth()->user()->nom_role_affichage }}</p>
                </div>
            </a>
            <form method="POST" action="{{ route('logout') }}">
                @csrf
                <button type="submit" class="w-full flex items-center gap-2 px-2 py-1.5 rounded-lg text-xs text-gray-500 hover:bg-gray-100 hover:text-gray-800 transition">
                    <i class="ti ti-logout text-sm"></i> Déconnexion
                </button>
            </form>
        </div>
    </aside>

    {{-- ── Contenu principal ───────────────────────────── --}}
    <div class="flex-1 flex flex-col overflow-hidden min-w-0">

        {{-- Topbar --}}
        <header class="bg-white dark:bg-gray-800 border-b border-gray-200 dark:border-gray-700 px-5 py-3 flex items-center gap-3 flex-shrink-0">
            <h1 class="text-sm font-semibold text-gray-900 flex-1">@yield('page-title')</h1>

            {{-- Alertes globales --}}
            <a href="{{ route('factures.index', ['statut' => 'en_retard']) }}"
               class="flex items-center gap-1 text-xs text-red-600 font-medium hover:underline">
                <i class="ti ti-alert-triangle"></i>
                @php $retards = \App\Models\Facture::where('societe_id', auth()->user()->societe_id)->enRetard()->count(); @endphp
                @if($retards > 0) {{ $retards }} facture(s) en retard @endif
            </a>

            {{-- Bouton assistant vocal IA --}}
            <button id="btn-assistant-vocal"
                    class="btn-secondary text-xs flex items-center gap-1"
                    title="Assistant vocal">
                <i class="ti ti-microphone" id="icon-micro"></i>
                <span class="hidden sm:inline">Assistant</span>
            </button>

            
            {{-- Assistant vocal --}}
            <button id="btn-assistant-vocal"
                    class="btn-secondary text-xs flex items-center gap-1"
                    title="Assistant comptable IA">
                <i class="ti ti-microphone"></i>
                <span class="hidden sm:inline">Assistant</span>
            </button>

@yield('topbar-actions')
        </header>

        {{-- Flash messages --}}
        @if(session('success'))
        <div class="mx-5 mt-3 px-4 py-2.5 bg-emerald-50 border border-emerald-200 rounded-lg text-sm text-emerald-700 flex items-center gap-2">
            <i class="ti ti-circle-check"></i> {{ session('success') }}
        </div>
        @endif

        @if($errors->any())
        <div class="mx-5 mt-3 px-4 py-2.5 bg-red-50 border border-red-200 rounded-lg text-sm text-red-700">
            <ul class="space-y-0.5">
                @foreach($errors->all() as $error)
                    <li class="flex items-center gap-1"><i class="ti ti-x text-xs"></i> {{ $error }}</li>
                @endforeach
            </ul>
        </div>
        @endif

        {{-- Page content --}}
        <main class="flex-1 overflow-y-auto p-5">
            @yield('content')
        </main>
    </div>
</div>

{{-- Modales globales (slot) --}}
@stack('modals')

@include('partials.assistant-vocal')
@stack('scripts')
</body>
</html>
