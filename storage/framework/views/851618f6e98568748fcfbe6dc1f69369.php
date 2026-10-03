<!DOCTYPE html>
<html lang="fr" class="">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="<?php echo e(csrf_token()); ?>">
    <title><?php echo $__env->yieldContent('title', 'ComptoFlow'); ?> — ComptoFlow</title>
    <?php echo app('Illuminate\Foundation\Vite')(['resources/css/app.css', 'resources/js/app.js']); ?>
    
    <script>
        (function() {
            var dark = localStorage.getItem('darkMode') === '1'
                || (!localStorage.getItem('darkMode') && window.matchMedia('(prefers-color-scheme: dark)').matches);
            if (dark) document.documentElement.classList.add('dark');
        })();
    </script>
    <script>
        window.SOCIETE_ID = <?php echo e(auth()->check() ? auth()->user()->societe_id : "null"); ?>;
        window.CURRENT_USER_ID = <?php echo e(auth()->check() ? auth()->id() : "null"); ?>;
    </script>
</head>
<body class="bg-gray-50 dark:bg-gray-900 text-gray-900 dark:text-gray-100 antialiased">

<div class="flex h-screen overflow-hidden">

    
    <aside class="w-56 flex-shrink-0 bg-white dark:bg-gray-800 border-r border-gray-200 dark:border-gray-700 flex flex-col">

        <div class="px-4 py-4 border-b border-gray-200 flex items-center gap-2">
            <span class="w-2.5 h-2.5 rounded-full bg-emerald-500 inline-block"></span>
            <span class="text-base font-semibold text-gray-900">ComptoFlow</span>
        </div>

        <nav class="flex-1 px-2 py-3 overflow-y-auto space-y-0.5">

            <p class="px-2 pt-1 pb-0.5 text-[10px] uppercase tracking-widest text-gray-400 font-medium">Principal</p>

            <?php if(auth()->user()->peutAccederA('dashboard')): ?>
            <?php if (isset($component)) { $__componentOriginale4ab10e54832a606d08a208396899ba2 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginale4ab10e54832a606d08a208396899ba2 = $attributes; } ?>
<?php $component = App\View\Components\NavItem::resolve(['route' => 'dashboard','icon' => 'ti-layout-dashboard'] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('nav-item'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\App\View\Components\NavItem::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes([]); ?>Tableau de bord <?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginale4ab10e54832a606d08a208396899ba2)): ?>
<?php $attributes = $__attributesOriginale4ab10e54832a606d08a208396899ba2; ?>
<?php unset($__attributesOriginale4ab10e54832a606d08a208396899ba2); ?>
<?php endif; ?>
<?php if (isset($__componentOriginale4ab10e54832a606d08a208396899ba2)): ?>
<?php $component = $__componentOriginale4ab10e54832a606d08a208396899ba2; ?>
<?php unset($__componentOriginale4ab10e54832a606d08a208396899ba2); ?>
<?php endif; ?>
            <?php endif; ?>
            <?php if(auth()->user()->peutAccederA('ecritures')): ?>
            <?php if (isset($component)) { $__componentOriginale4ab10e54832a606d08a208396899ba2 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginale4ab10e54832a606d08a208396899ba2 = $attributes; } ?>
<?php $component = App\View\Components\NavItem::resolve(['route' => 'ecritures.index','icon' => 'ti-pencil'] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('nav-item'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\App\View\Components\NavItem::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes([]); ?>
                Écritures
                <?php if($ecrituresBrouillon ?? 0): ?>
                    <span class="ml-auto text-[10px] font-semibold bg-emerald-100 text-emerald-800 px-1.5 py-0.5 rounded-full">
                        <?php echo e($ecrituresBrouillon); ?>

                    </span>
                <?php endif; ?>
             <?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginale4ab10e54832a606d08a208396899ba2)): ?>
<?php $attributes = $__attributesOriginale4ab10e54832a606d08a208396899ba2; ?>
<?php unset($__attributesOriginale4ab10e54832a606d08a208396899ba2); ?>
<?php endif; ?>
<?php if (isset($__componentOriginale4ab10e54832a606d08a208396899ba2)): ?>
<?php $component = $__componentOriginale4ab10e54832a606d08a208396899ba2; ?>
<?php unset($__componentOriginale4ab10e54832a606d08a208396899ba2); ?>
<?php endif; ?>
            <?php endif; ?>
            <?php if(auth()->user()->peutAccederA('factures')): ?>
            <?php if (isset($component)) { $__componentOriginale4ab10e54832a606d08a208396899ba2 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginale4ab10e54832a606d08a208396899ba2 = $attributes; } ?>
<?php $component = App\View\Components\NavItem::resolve(['route' => 'factures.index','icon' => 'ti-file-invoice'] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('nav-item'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\App\View\Components\NavItem::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes([]); ?>Factures <?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginale4ab10e54832a606d08a208396899ba2)): ?>
<?php $attributes = $__attributesOriginale4ab10e54832a606d08a208396899ba2; ?>
<?php unset($__attributesOriginale4ab10e54832a606d08a208396899ba2); ?>
<?php endif; ?>
<?php if (isset($__componentOriginale4ab10e54832a606d08a208396899ba2)): ?>
<?php $component = $__componentOriginale4ab10e54832a606d08a208396899ba2; ?>
<?php unset($__componentOriginale4ab10e54832a606d08a208396899ba2); ?>
<?php endif; ?>
            <?php endif; ?>
            <?php if(auth()->user()->peutAccederA('rapprochement')): ?>
            <?php if (isset($component)) { $__componentOriginale4ab10e54832a606d08a208396899ba2 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginale4ab10e54832a606d08a208396899ba2 = $attributes; } ?>
<?php $component = App\View\Components\NavItem::resolve(['route' => 'rapprochement.index','icon' => 'ti-arrows-exchange'] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('nav-item'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\App\View\Components\NavItem::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes([]); ?>Rapprochement <?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginale4ab10e54832a606d08a208396899ba2)): ?>
<?php $attributes = $__attributesOriginale4ab10e54832a606d08a208396899ba2; ?>
<?php unset($__attributesOriginale4ab10e54832a606d08a208396899ba2); ?>
<?php endif; ?>
<?php if (isset($__componentOriginale4ab10e54832a606d08a208396899ba2)): ?>
<?php $component = $__componentOriginale4ab10e54832a606d08a208396899ba2; ?>
<?php unset($__componentOriginale4ab10e54832a606d08a208396899ba2); ?>
<?php endif; ?>
            <?php endif; ?>

            <?php if(auth()->user()->peutAccederA('bilan') || auth()->user()->peutAccederA('tva')): ?>
            <p class="px-2 pt-3 pb-0.5 text-[10px] uppercase tracking-widest text-gray-400 font-medium">Rapports</p>
            <?php endif; ?>
            <?php if(auth()->user()->peutAccederA('bilan')): ?>
            <?php if (isset($component)) { $__componentOriginale4ab10e54832a606d08a208396899ba2 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginale4ab10e54832a606d08a208396899ba2 = $attributes; } ?>
<?php $component = App\View\Components\NavItem::resolve(['route' => 'rapports.bilan','icon' => 'ti-scale'] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('nav-item'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\App\View\Components\NavItem::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes([]); ?>Bilan <?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginale4ab10e54832a606d08a208396899ba2)): ?>
<?php $attributes = $__attributesOriginale4ab10e54832a606d08a208396899ba2; ?>
<?php unset($__attributesOriginale4ab10e54832a606d08a208396899ba2); ?>
<?php endif; ?>
<?php if (isset($__componentOriginale4ab10e54832a606d08a208396899ba2)): ?>
<?php $component = $__componentOriginale4ab10e54832a606d08a208396899ba2; ?>
<?php unset($__componentOriginale4ab10e54832a606d08a208396899ba2); ?>
<?php endif; ?>
            <?php if (isset($component)) { $__componentOriginale4ab10e54832a606d08a208396899ba2 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginale4ab10e54832a606d08a208396899ba2 = $attributes; } ?>
<?php $component = App\View\Components\NavItem::resolve(['route' => 'rapports.resultat','icon' => 'ti-chart-bar'] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('nav-item'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\App\View\Components\NavItem::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes([]); ?>Résultat <?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginale4ab10e54832a606d08a208396899ba2)): ?>
<?php $attributes = $__attributesOriginale4ab10e54832a606d08a208396899ba2; ?>
<?php unset($__attributesOriginale4ab10e54832a606d08a208396899ba2); ?>
<?php endif; ?>
<?php if (isset($__componentOriginale4ab10e54832a606d08a208396899ba2)): ?>
<?php $component = $__componentOriginale4ab10e54832a606d08a208396899ba2; ?>
<?php unset($__componentOriginale4ab10e54832a606d08a208396899ba2); ?>
<?php endif; ?>
            <?php if (isset($component)) { $__componentOriginale4ab10e54832a606d08a208396899ba2 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginale4ab10e54832a606d08a208396899ba2 = $attributes; } ?>
<?php $component = App\View\Components\NavItem::resolve(['route' => 'previsions.index','icon' => 'ti-chart-arrows-vertical'] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('nav-item'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\App\View\Components\NavItem::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes([]); ?>Prévisions <?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginale4ab10e54832a606d08a208396899ba2)): ?>
<?php $attributes = $__attributesOriginale4ab10e54832a606d08a208396899ba2; ?>
<?php unset($__attributesOriginale4ab10e54832a606d08a208396899ba2); ?>
<?php endif; ?>
<?php if (isset($__componentOriginale4ab10e54832a606d08a208396899ba2)): ?>
<?php $component = $__componentOriginale4ab10e54832a606d08a208396899ba2; ?>
<?php unset($__componentOriginale4ab10e54832a606d08a208396899ba2); ?>
<?php endif; ?>
            <?php endif; ?>
            <?php if(auth()->user()->peutAccederA('tva')): ?>
            <?php if (isset($component)) { $__componentOriginale4ab10e54832a606d08a208396899ba2 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginale4ab10e54832a606d08a208396899ba2 = $attributes; } ?>
<?php $component = App\View\Components\NavItem::resolve(['route' => 'tva.index','icon' => 'ti-receipt-tax'] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('nav-item'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\App\View\Components\NavItem::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes([]); ?>TVA <?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginale4ab10e54832a606d08a208396899ba2)): ?>
<?php $attributes = $__attributesOriginale4ab10e54832a606d08a208396899ba2; ?>
<?php unset($__attributesOriginale4ab10e54832a606d08a208396899ba2); ?>
<?php endif; ?>
<?php if (isset($__componentOriginale4ab10e54832a606d08a208396899ba2)): ?>
<?php $component = $__componentOriginale4ab10e54832a606d08a208396899ba2; ?>
<?php unset($__componentOriginale4ab10e54832a606d08a208396899ba2); ?>
<?php endif; ?>
            <?php endif; ?>

            <?php if(auth()->user()->peutAccederA('tiers') || auth()->user()->peutAccederA('calendrier') || auth()->user()->peutAccederA('parametres')): ?>
            <p class="px-2 pt-3 pb-0.5 text-[10px] uppercase tracking-widest text-gray-400 font-medium">Gestion</p>
            <?php endif; ?>
            <?php if(auth()->user()->peutAccederA('tiers')): ?>
            <?php if (isset($component)) { $__componentOriginale4ab10e54832a606d08a208396899ba2 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginale4ab10e54832a606d08a208396899ba2 = $attributes; } ?>
<?php $component = App\View\Components\NavItem::resolve(['route' => 'tiers.index','icon' => 'ti-users'] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('nav-item'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\App\View\Components\NavItem::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes([]); ?>Tiers <?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginale4ab10e54832a606d08a208396899ba2)): ?>
<?php $attributes = $__attributesOriginale4ab10e54832a606d08a208396899ba2; ?>
<?php unset($__attributesOriginale4ab10e54832a606d08a208396899ba2); ?>
<?php endif; ?>
<?php if (isset($__componentOriginale4ab10e54832a606d08a208396899ba2)): ?>
<?php $component = $__componentOriginale4ab10e54832a606d08a208396899ba2; ?>
<?php unset($__componentOriginale4ab10e54832a606d08a208396899ba2); ?>
<?php endif; ?>
            <?php endif; ?>
            <?php if(auth()->user()->peutAccederA('calendrier')): ?>
            <?php if (isset($component)) { $__componentOriginale4ab10e54832a606d08a208396899ba2 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginale4ab10e54832a606d08a208396899ba2 = $attributes; } ?>
<?php $component = App\View\Components\NavItem::resolve(['route' => 'calendrier.index','icon' => 'ti-calendar'] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('nav-item'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\App\View\Components\NavItem::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes([]); ?>Calendrier <?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginale4ab10e54832a606d08a208396899ba2)): ?>
<?php $attributes = $__attributesOriginale4ab10e54832a606d08a208396899ba2; ?>
<?php unset($__attributesOriginale4ab10e54832a606d08a208396899ba2); ?>
<?php endif; ?>
<?php if (isset($__componentOriginale4ab10e54832a606d08a208396899ba2)): ?>
<?php $component = $__componentOriginale4ab10e54832a606d08a208396899ba2; ?>
<?php unset($__componentOriginale4ab10e54832a606d08a208396899ba2); ?>
<?php endif; ?>
            <?php endif; ?>
            <?php if(auth()->user()->peutAccederA('parametres')): ?>
            <?php if (isset($component)) { $__componentOriginale4ab10e54832a606d08a208396899ba2 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginale4ab10e54832a606d08a208396899ba2 = $attributes; } ?>
<?php $component = App\View\Components\NavItem::resolve(['route' => 'parametres.index','icon' => 'ti-settings'] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('nav-item'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\App\View\Components\NavItem::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes([]); ?>Paramètres <?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginale4ab10e54832a606d08a208396899ba2)): ?>
<?php $attributes = $__attributesOriginale4ab10e54832a606d08a208396899ba2; ?>
<?php unset($__attributesOriginale4ab10e54832a606d08a208396899ba2); ?>
<?php endif; ?>
<?php if (isset($__componentOriginale4ab10e54832a606d08a208396899ba2)): ?>
<?php $component = $__componentOriginale4ab10e54832a606d08a208396899ba2; ?>
<?php unset($__componentOriginale4ab10e54832a606d08a208396899ba2); ?>
<?php endif; ?>
            <?php endif; ?>
        </nav>

        
        <div class="border-t border-gray-200 px-2 py-3 space-y-0.5">
            <a href="<?php echo e(route('profile.show')); ?>"
               class="flex items-center gap-2 px-2 py-1.5 rounded-lg hover:bg-gray-100 transition
                      <?php echo e(request()->routeIs('profile.*') ? 'bg-gray-100' : ''); ?>">
                <div class="w-7 h-7 rounded-full bg-blue-100 flex items-center justify-center text-blue-700 text-xs font-semibold flex-shrink-0">
                    <?php echo e(auth()->user()->initiales); ?>

                </div>
                <div class="min-w-0 flex-1">
                    <p class="text-xs font-medium text-gray-900 truncate"><?php echo e(auth()->user()->nom_complet); ?></p>
                    <p class="text-[11px] text-gray-400 truncate"><?php echo e(auth()->user()->nom_role_affichage); ?></p>
                </div>
            </a>
            <form method="POST" action="<?php echo e(route('logout')); ?>">
                <?php echo csrf_field(); ?>
                <button type="submit" class="w-full flex items-center gap-2 px-2 py-1.5 rounded-lg text-xs text-gray-500 hover:bg-gray-100 hover:text-gray-800 transition">
                    <i class="ti ti-logout text-sm"></i> Déconnexion
                </button>
            </form>
        </div>
    </aside>

    
    <div class="flex-1 flex flex-col overflow-hidden min-w-0">

        
        <header class="bg-white dark:bg-gray-800 border-b border-gray-200 dark:border-gray-700 px-5 py-3 flex items-center gap-3 flex-shrink-0">
            <h1 class="text-sm font-semibold text-gray-900 flex-1"><?php echo $__env->yieldContent('page-title'); ?></h1>

            
            <a href="<?php echo e(route('factures.index', ['statut' => 'en_retard'])); ?>"
               class="flex items-center gap-1 text-xs text-red-600 font-medium hover:underline">
                <i class="ti ti-alert-triangle"></i>
                <?php $retards = \App\Models\Facture::where('societe_id', auth()->user()->societe_id)->enRetard()->count(); ?>
                <?php if($retards > 0): ?> <?php echo e($retards); ?> facture(s) en retard <?php endif; ?>
            </a>

            
            <button id="btn-assistant-vocal"
                    class="btn-secondary text-xs flex items-center gap-1"
                    title="Assistant vocal">
                <i class="ti ti-microphone" id="icon-micro"></i>
                <span class="hidden sm:inline">Assistant</span>
            </button>

            
            
            <button id="btn-assistant-vocal"
                    class="btn-secondary text-xs flex items-center gap-1"
                    title="Assistant comptable IA">
                <i class="ti ti-microphone"></i>
                <span class="hidden sm:inline">Assistant</span>
            </button>

<?php echo $__env->yieldContent('topbar-actions'); ?>
        </header>

        
        <?php if(session('success')): ?>
        <div class="mx-5 mt-3 px-4 py-2.5 bg-emerald-50 border border-emerald-200 rounded-lg text-sm text-emerald-700 flex items-center gap-2">
            <i class="ti ti-circle-check"></i> <?php echo e(session('success')); ?>

        </div>
        <?php endif; ?>

        <?php if($errors->any()): ?>
        <div class="mx-5 mt-3 px-4 py-2.5 bg-red-50 border border-red-200 rounded-lg text-sm text-red-700">
            <ul class="space-y-0.5">
                <?php $__currentLoopData = $errors->all(); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $error): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                    <li class="flex items-center gap-1"><i class="ti ti-x text-xs"></i> <?php echo e($error); ?></li>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
            </ul>
        </div>
        <?php endif; ?>

        
        <main class="flex-1 overflow-y-auto p-5">
            <?php echo $__env->yieldContent('content'); ?>
        </main>
    </div>
</div>


<?php echo $__env->yieldPushContent('modals'); ?>

<?php echo $__env->make('partials.assistant-vocal', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
<?php echo $__env->yieldPushContent('scripts'); ?>
</body>
</html>
<?php /**PATH C:\xampp\htdocs\Comptoflow\resources\views/layouts/app.blade.php ENDPATH**/ ?>