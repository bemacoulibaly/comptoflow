<?php $__env->startSection('title', 'Mon profil'); ?>
<?php $__env->startSection('page-title', 'Mon profil'); ?>

<?php $__env->startSection('content'); ?>
<div class="space-y-4 max-w-4xl">

    
    <div class="bg-white rounded-xl border border-gray-200 p-5">
        <div class="flex items-center gap-4">
            <div class="w-16 h-16 rounded-full bg-blue-100 flex items-center justify-center text-blue-700 text-xl font-semibold flex-shrink-0">
                <?php echo e($user->initiales); ?>

            </div>
            <div class="flex-1">
                <h2 class="text-lg font-semibold text-gray-900"><?php echo e($user->nom_complet); ?></h2>
                <p class="text-sm text-gray-400"><?php echo e($user->email); ?></p>
            </div>
            <div>
                <?php if($user->role === 'admin'): ?>   <span class="badge-green">Administrateur</span>
                <?php elseif($user->role === 'editeur'): ?> <span class="badge-blue">Éditeur</span>
                <?php else: ?> <span class="badge-gray">Lecteur</span>
                <?php endif; ?>
            </div>
        </div>
    </div>

    
    <div class="grid grid-cols-4 gap-3">
        <div class="bg-white rounded-xl border border-gray-200 p-4">
            <p class="text-xs text-gray-400 mb-1">Écritures saisies</p>
            <p class="text-xl font-semibold font-mono"><?php echo e($user->ecritures_count); ?></p>
        </div>
        <div class="bg-white rounded-xl border border-gray-200 p-4">
            <p class="text-xs text-gray-400 mb-1">Validées</p>
            <p class="text-xl font-semibold font-mono text-emerald-600"><?php echo e($ecrituresValidees); ?></p>
        </div>
        <div class="bg-white rounded-xl border border-gray-200 p-4">
            <p class="text-xs text-gray-400 mb-1">En brouillon</p>
            <p class="text-xl font-semibold font-mono <?php echo e($ecrituresBrouillon > 0 ? 'text-amber-600' : 'text-gray-900'); ?>">
                <?php echo e($ecrituresBrouillon); ?>

            </p>
        </div>
        <div class="bg-white rounded-xl border border-gray-200 p-4">
            <p class="text-xs text-gray-400 mb-1">Total facturé (clients)</p>
            <p class="text-xl font-semibold font-mono text-emerald-600"><?php echo e(number_format($totalFacture, 0, ',', ' ')); ?></p>
        </div>
    </div>

    <div class="grid grid-cols-2 gap-4">

        
        <div class="bg-white rounded-xl border border-gray-200 overflow-hidden">
            <div class="px-4 py-3 border-b border-gray-100 flex items-center gap-2">
                <i class="ti ti-user text-gray-400"></i>
                <span class="text-sm font-semibold text-gray-800">Informations personnelles</span>
            </div>
            <form method="POST" action="<?php echo e(route('profile.update')); ?>" class="p-4 space-y-3">
                <?php echo csrf_field(); ?> <?php echo method_field('PATCH'); ?>
                <div class="grid grid-cols-2 gap-3">
                    <div>
                        <label class="label">Prénom</label>
                        <input type="text" name="prenom" value="<?php echo e(old('prenom', $user->prenom)); ?>" class="input <?php $__errorArgs = ['prenom'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> input-error <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>" required>
                        <?php $__errorArgs = ['prenom'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?><p class="field-error"><?php echo e($message); ?></p><?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                    </div>
                    <div>
                        <label class="label">Nom</label>
                        <input type="text" name="nom" value="<?php echo e(old('nom', $user->nom)); ?>" class="input <?php $__errorArgs = ['nom'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> input-error <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>" required>
                        <?php $__errorArgs = ['nom'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?><p class="field-error"><?php echo e($message); ?></p><?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                    </div>
                </div>
                <div>
                    <label class="label">Email</label>
                    <input type="email" name="email" value="<?php echo e(old('email', $user->email)); ?>" class="input <?php $__errorArgs = ['email'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> input-error <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>" required>
                    <?php $__errorArgs = ['email'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?><p class="field-error"><?php echo e($message); ?></p><?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                </div>
                <div class="flex justify-end pt-1">
                    <button type="submit" class="btn-primary text-xs"><i class="ti ti-device-floppy"></i> Enregistrer</button>
                </div>
            </form>
        </div>

        
        <div class="bg-white rounded-xl border border-gray-200 overflow-hidden">
            <div class="px-4 py-3 border-b border-gray-100 flex items-center gap-2">
                <i class="ti ti-lock text-gray-400"></i>
                <span class="text-sm font-semibold text-gray-800">Mot de passe</span>
            </div>
            <form method="POST" action="<?php echo e(route('profile.password')); ?>" class="p-4 space-y-3">
                <?php echo csrf_field(); ?> <?php echo method_field('PUT'); ?>
                <div>
                    <label class="label">Mot de passe actuel</label>
                    <input type="password" name="current_password" class="input <?php $__errorArgs = ['current_password'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> input-error <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>" required>
                    <?php $__errorArgs = ['current_password'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?><p class="field-error"><?php echo e($message); ?></p><?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                </div>
                <div>
                    <label class="label">Nouveau mot de passe</label>
                    <input type="password" name="password" class="input <?php $__errorArgs = ['password'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> input-error <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>" required>
                    <?php $__errorArgs = ['password'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?><p class="field-error"><?php echo e($message); ?></p><?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
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

    
    <div class="grid grid-cols-2 gap-4">

        
        <div class="bg-white rounded-xl border border-gray-200 overflow-hidden">
            <div class="px-4 py-3 border-b border-gray-100 flex items-center gap-2">
                <i class="ti ti-pencil text-gray-400"></i>
                <span class="text-sm font-semibold text-gray-800 flex-1">Mes dernières écritures</span>
                <a href="<?php echo e(route('ecritures.index')); ?>" class="text-xs text-emerald-600 hover:underline">Tout voir</a>
            </div>
            <div class="divide-y divide-gray-100">
                <?php $__empty_1 = true; $__currentLoopData = $dernieresEcritures; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $e): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                <a href="<?php echo e(route('ecritures.show', $e)); ?>" class="flex items-center gap-3 px-4 py-2.5 hover:bg-gray-50 transition">
                    <div class="flex-1 min-w-0">
                        <p class="text-xs font-medium text-gray-900 truncate"><?php echo e($e->libelle); ?></p>
                        <p class="text-[11px] text-gray-400"><?php echo e($e->numero_piece); ?> · <?php echo e($e->date_ecriture->format('d/m/Y')); ?></p>
                    </div>
                    <?php if($e->statut === 'validee'): ?> <span class="badge-green">Validée</span>
                    <?php elseif($e->statut === 'annulee'): ?> <span class="badge-red">Annulée</span>
                    <?php else: ?> <span class="badge-amber">Brouillon</span>
                    <?php endif; ?>
                </a>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                <div class="px-4 py-8 text-center text-xs text-gray-400">
                    <i class="ti ti-inbox text-2xl block mb-2"></i> Aucune écriture saisie.
                </div>
                <?php endif; ?>
            </div>
        </div>

        
        <div class="bg-white rounded-xl border border-gray-200 overflow-hidden">
            <div class="px-4 py-3 border-b border-gray-100 flex items-center gap-2">
                <i class="ti ti-file-invoice text-gray-400"></i>
                <span class="text-sm font-semibold text-gray-800 flex-1">Mes dernières factures</span>
                <a href="<?php echo e(route('factures.index')); ?>" class="text-xs text-emerald-600 hover:underline">Tout voir</a>
            </div>
            <div class="divide-y divide-gray-100">
                <?php $__empty_1 = true; $__currentLoopData = $dernieresFactures; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $f): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                <a href="<?php echo e(route('factures.show', $f)); ?>" class="flex items-center gap-3 px-4 py-2.5 hover:bg-gray-50 transition">
                    <div class="flex-1 min-w-0">
                        <p class="text-xs font-medium text-gray-900 truncate"><?php echo e($f->tiers->nom); ?></p>
                        <p class="text-[11px] text-gray-400 font-mono"><?php echo e($f->numero); ?> · <?php echo e($f->date_emission->format('d/m/Y')); ?></p>
                    </div>
                    <span class="font-mono text-xs font-medium"><?php echo e(number_format($f->montant_ttc, 0, ',', ' ')); ?> F</span>
                    <?php if($f->statut === 'payee'): ?> <span class="badge-green">Payée</span>
                    <?php elseif($f->statut === 'partielle'): ?> <span class="badge-amber">Partielle</span>
                    <?php elseif($f->statut === 'en_retard'): ?> <span class="badge-red">Retard</span>
                    <?php else: ?> <span class="badge-blue">En cours</span>
                    <?php endif; ?>
                </a>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                <div class="px-4 py-8 text-center text-xs text-gray-400">
                    <i class="ti ti-file-off text-2xl block mb-2"></i> Aucune facture créée.
                </div>
                <?php endif; ?>
            </div>
        </div>
    </div>

</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\xampp\htdocs\Comptoflow\resources\views/profile/show.blade.php ENDPATH**/ ?>