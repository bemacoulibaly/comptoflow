<?php $__env->startSection('title','Paramètres'); ?>
<?php $__env->startSection('page-title','Paramètres'); ?>

<?php $__env->startSection('content'); ?>
<div class="grid grid-cols-2 gap-4">

    
    <div class="space-y-4">
        <div class="bg-white rounded-xl border border-gray-200 overflow-hidden">
            <div class="px-4 py-3 border-b border-gray-100 flex items-center gap-2">
                <i class="ti ti-building text-gray-400"></i>
                <span class="text-sm font-semibold text-gray-800">Informations société</span>
            </div>
            <form method="POST" action="<?php echo e(route('parametres.societe')); ?>" class="p-4 space-y-3">
                <?php echo csrf_field(); ?> <?php echo method_field('PATCH'); ?>
                <div>
                    <label class="label">Raison sociale <span class="text-red-500">*</span></label>
                    <input type="text" name="raison_sociale" value="<?php echo e(old('raison_sociale', $societe->raison_sociale)); ?>" class="input <?php $__errorArgs = ['raison_sociale'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> input-error <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>" required>
                    <?php $__errorArgs = ['raison_sociale'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?><p class="field-error"><?php echo e($message); ?></p><?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                </div>
                <div>
                    <label class="label">N° contribuable <span class="text-red-500">*</span></label>
                    <input type="text" name="numero_contribuable" value="<?php echo e(old('numero_contribuable', $societe->numero_contribuable)); ?>" class="input" required>
                </div>
                <div class="grid grid-cols-2 gap-3">
                    <div>
                        <label class="label">Régime fiscal</label>
                        <select name="regime_fiscal" class="input">
                            <option value="reel_simplifie" <?php if($societe->regime_fiscal==='reel_simplifie'): echo 'selected'; endif; ?>>Réel simplifié</option>
                            <option value="reel_normal"    <?php if($societe->regime_fiscal==='reel_normal'): echo 'selected'; endif; ?>>Réel normal</option>
                            <option value="forfait"        <?php if($societe->regime_fiscal==='forfait'): echo 'selected'; endif; ?>>Forfait</option>
                        </select>
                    </div>
                    <div>
                        <label class="label">Devise</label>
                        <select name="devise" class="input">
                            <option value="XOF" <?php if($societe->devise==='XOF'): echo 'selected'; endif; ?>>FCFA (XOF)</option>
                            <option value="EUR" <?php if($societe->devise==='EUR'): echo 'selected'; endif; ?>>Euro (EUR)</option>
                            <option value="USD" <?php if($societe->devise==='USD'): echo 'selected'; endif; ?>>Dollar (USD)</option>
                        </select>
                    </div>
                </div>
                <div>
                    <label class="label">Taux TVA par défaut (%)</label>
                    <input type="number" name="taux_tva" value="<?php echo e(old('taux_tva', $societe->taux_tva)); ?>" class="input" step="0.01" min="0" max="100">
                    <p class="text-[10px] text-gray-400 mt-1">Taux standard CI : 18%</p>
                </div>
                <div>
                    <label class="label">Adresse</label>
                    <input type="text" name="adresse" value="<?php echo e(old('adresse', $societe->adresse)); ?>" class="input" placeholder="Avenue Botreau-Roussel…">
                </div>
                <div class="grid grid-cols-2 gap-3">
                    <div>
                        <label class="label">Ville</label>
                        <input type="text" name="ville" value="<?php echo e(old('ville', $societe->ville)); ?>" class="input" placeholder="Abidjan">
                    </div>
                    <div>
                        <label class="label">Téléphone</label>
                        <input type="text" name="telephone" value="<?php echo e(old('telephone', $societe->telephone)); ?>" class="input" placeholder="+225 07 00 00 00">
                    </div>
                </div>
                <div>
                    <label class="label">Email</label>
                    <input type="email" name="email" value="<?php echo e(old('email', $societe->email)); ?>" class="input">
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

        
        <div class="bg-white rounded-xl border border-gray-200 overflow-hidden">
            <div class="px-4 py-3 border-b border-gray-100 flex items-center gap-2">
                <i class="ti ti-users text-gray-400"></i>
                <span class="text-sm font-semibold text-gray-800 flex-1">Comptes utilisateurs</span>
                <span class="badge-gray"><?php echo e($nbUsers); ?></span>
            </div>
            <div class="px-4 py-3">
                <p class="text-xs text-gray-500">Gérez les comptes de votre équipe : rôles, mots de passe, et activité individuelle de chacun.</p>
                <a href="<?php echo e(route('parametres.utilisateurs.index')); ?>" class="btn-secondary text-xs mt-3 inline-flex items-center gap-1">
                    <i class="ti ti-settings" aria-hidden="true"></i> Gérer les comptes
                </a>
                <?php if(auth()->user()->isAdmin()): ?>
                <a href="<?php echo e(route('parametres.utilisateurs.create')); ?>" class="btn-primary text-xs mt-3 ml-2 inline-flex items-center gap-1">
                    <i class="ti ti-user-plus" aria-hidden="true"></i> Créer un compte
                </a>
                <?php endif; ?>
            </div>
        </div>

        
        <div class="bg-white rounded-xl border border-gray-200 overflow-hidden">
            <div class="px-4 py-3 border-b border-gray-100 flex items-center gap-2">
                <i class="ti ti-bell text-gray-400"></i>
                <span class="text-sm font-semibold text-gray-800">Notifications</span>
            </div>
            <?php $__currentLoopData = [
                ['Factures en retard',    'Alerte quand une facture dépasse son échéance'],
                ['Déclaration TVA',       'Rappel 15 jours avant l\'échéance TVA'],
                ['Écritures à valider',   'Résumé quotidien des brouillons en attente'],
                ['Rapprochement mensuel', 'Rappel de clôture mensuelle'],
            ]; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as [$titre, $desc]): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
            <div class="flex items-center gap-3 px-4 py-3 border-b border-gray-100 last:border-0">
                <div class="flex-1 min-w-0">
                    <p class="text-xs font-medium text-gray-900"><?php echo e($titre); ?></p>
                    <p class="text-[11px] text-gray-400"><?php echo e($desc); ?></p>
                </div>
                <label class="relative inline-flex items-center cursor-pointer">
                    <input type="checkbox" class="sr-only peer" checked>
                    <div class="w-9 h-5 bg-gray-200 peer-checked:bg-emerald-600 rounded-full transition
                                after:content-[''] after:absolute after:top-0.5 after:left-0.5
                                after:bg-white after:rounded-full after:h-4 after:w-4 after:transition
                                peer-checked:after:translate-x-4"></div>
                </label>
            </div>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
        </div>

        
        <div class="bg-white rounded-xl border border-gray-200 overflow-hidden">
            <div class="px-4 py-3 border-b border-gray-100 flex items-center gap-2">
                <i class="ti ti-book text-gray-400"></i>
                <span class="text-sm font-semibold text-gray-800 flex-1">Plan comptable OHADA</span>
                <span class="badge-green"><?php echo e(auth()->user()->societe->comptes->count()); ?> comptes</span>
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

<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\xampp\htdocs\Comptoflow\resources\views/parametres/index.blade.php ENDPATH**/ ?>