<?php $__env->startSection('title', 'Créer un compte'); ?>
<?php $__env->startSection('page-title', 'Créer un compte'); ?>

<?php $__env->startSection('topbar-actions'); ?>
    <a href="<?php echo e(route('parametres.utilisateurs.index')); ?>" class="btn-secondary text-xs flex items-center gap-1">
        <i class="ti ti-arrow-left"></i> Retour
    </a>
<?php $__env->stopSection(); ?>

<?php $__env->startSection('content'); ?>
<div class="max-w-xl">

    <div class="alert-info mb-4">
        <i class="ti ti-info-circle"></i>
        Choisissez un mot de passe initial et communiquez-le vous-même au collaborateur (en main propre ou par un canal sécurisé).
        Aucun email automatique n'est envoyé.
    </div>

    <form method="POST" action="<?php echo e(route('parametres.utilisateurs.store')); ?>" class="space-y-4">
        <?php echo csrf_field(); ?>
        <div class="bg-white rounded-xl border border-gray-200 p-5 space-y-4">

            <div class="grid grid-cols-2 gap-4">
                <div>
                    <label class="label">Prénom <span class="text-red-500">*</span></label>
                    <input type="text" name="prenom" value="<?php echo e(old('prenom')); ?>" required
                           class="input <?php $__errorArgs = ['prenom'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> input-error <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>">
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
                    <label class="label">Nom <span class="text-red-500">*</span></label>
                    <input type="text" name="nom" value="<?php echo e(old('nom')); ?>" required
                           class="input <?php $__errorArgs = ['nom'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> input-error <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>">
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
                <label class="label">Email <span class="text-red-500">*</span></label>
                <input type="email" name="email" value="<?php echo e(old('email')); ?>" required
                       class="input <?php $__errorArgs = ['email'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> input-error <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>" placeholder="prenom.nom@cabinet.ci">
                <?php $__errorArgs = ['email'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?><p class="field-error"><?php echo e($message); ?></p><?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
            </div>

            <div>
                <label class="label">Rôle système <span class="text-red-500">*</span></label>
                <select name="role" class="input <?php $__errorArgs = ['role'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> input-error <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>" required>
                    <option value="editeur" <?php if(old('role')==='editeur'): echo 'selected'; endif; ?>>Éditeur — peut saisir et modifier</option>
                    <option value="lecteur" <?php if(old('role')==='lecteur'): echo 'selected'; endif; ?>>Lecteur — consultation uniquement</option>
                    <option value="admin"   <?php if(old('role')==='admin'): echo 'selected'; endif; ?>>Administrateur — accès complet</option>
                </select>
                <?php $__errorArgs = ['role'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?><p class="field-error"><?php echo e($message); ?></p><?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                <p class="text-[11px] text-gray-400 mt-1">Détermine le niveau d'accès de base. L'administrateur a toujours accès à tout, sans exception.</p>
            </div>

            <?php if($roles->isNotEmpty()): ?>
            <div>
                <label class="label">Rôle personnalisé (optionnel)</label>
                <select name="role_id" class="input <?php $__errorArgs = ['role_id'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> input-error <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>">
                    <option value="">— Aucun, utiliser uniquement le rôle système ci-dessus —</option>
                    <?php $__currentLoopData = $roles; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $r): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                    <option value="<?php echo e($r->id); ?>" <?php if(old('role_id') == $r->id): echo 'selected'; endif; ?>>
                        <?php echo e($r->nom); ?> <?php if($r->est_systeme): ?> (système) <?php endif; ?>
                    </option>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                </select>
                <?php $__errorArgs = ['role_id'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?><p class="field-error"><?php echo e($message); ?></p><?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                <p class="text-[11px] text-gray-400 mt-1">Si choisi, ce rôle personnalisé détermine les pages accessibles (sauf pour un compte Administrateur, qui garde un accès total).</p>
            </div>
            <?php endif; ?>

            <div class="grid grid-cols-2 gap-4 pt-2 border-t border-gray-100">
                <div>
                    <label class="label">Mot de passe initial <span class="text-red-500">*</span></label>
                    <input type="password" name="password" required
                           class="input <?php $__errorArgs = ['password'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> input-error <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>">
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
                    <label class="label">Confirmer le mot de passe <span class="text-red-500">*</span></label>
                    <input type="password" name="password_confirmation" required class="input">
                </div>
            </div>
        </div>

        <div class="flex justify-end gap-3">
            <a href="<?php echo e(route('parametres.utilisateurs.index')); ?>" class="btn-secondary text-sm">Annuler</a>
            <button type="submit" class="btn-primary text-sm"><i class="ti ti-user-plus"></i> Créer le compte</button>
        </div>
    </form>
</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\xampp\htdocs\Comptoflow\resources\views/parametres/utilisateurs/create.blade.php ENDPATH**/ ?>