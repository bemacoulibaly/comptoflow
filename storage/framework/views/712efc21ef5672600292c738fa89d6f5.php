<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Connexion — ComptoFlow</title>
    <?php echo app('Illuminate\Foundation\Vite')(['resources/css/app.css', 'resources/js/app.js']); ?>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/@tabler/icons-webfont@3.0.0/dist/tabler-icons.min.css">
</head>
<body class="min-h-screen bg-gray-50 flex items-center justify-center">

<div class="w-full max-w-md px-4">

    
    <div class="text-center mb-8">
        <div class="inline-flex items-center gap-2 mb-2">
            <span class="w-3 h-3 rounded-full bg-emerald-500 inline-block"></span>
            <span class="text-2xl font-semibold text-gray-900">ComptoFlow</span>
        </div>
        <p class="text-sm text-gray-500">Plateforme comptable SYSCOHADA — Côte d'Ivoire</p>
    </div>

    
    <div class="bg-white rounded-2xl border border-gray-200 shadow-sm p-8">

        <h1 class="text-lg font-semibold text-gray-900 mb-1">Connexion</h1>
        <p class="text-sm text-gray-400 mb-6">Entrez vos identifiants pour accéder à votre espace.</p>

        
        <?php if(session('status')): ?>
            <div class="alert-success mb-4">
                <i class="ti ti-circle-check"></i> <?php echo e(session('status')); ?>

            </div>
        <?php endif; ?>

        
        <?php if($errors->any()): ?>
            <div class="alert-error mb-4">
                <i class="ti ti-alert-triangle"></i>
                <span><?php echo e($errors->first()); ?></span>
            </div>
        <?php endif; ?>

        <form method="POST" action="<?php echo e(route('login')); ?>" class="space-y-4">
            <?php echo csrf_field(); ?>

            <div>
                <label class="label" for="email">Adresse email</label>
                <div class="relative">
                    <span class="absolute left-3 top-1/2 -translate-y-1/2 text-gray-400">
                        <i class="ti ti-mail text-sm"></i>
                    </span>
                    <input
                        id="email"
                        type="email"
                        name="email"
                        value="<?php echo e(old('email')); ?>"
                        required
                        autofocus
                        autocomplete="email"
                        placeholder="admin@comptoflow.ci"
                        class="input pl-9 <?php $__errorArgs = ['email'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> input-error <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>">
                </div>
            </div>

            <div>
                <div class="flex items-center justify-between mb-1">
                    <label class="label mb-0" for="password">Mot de passe</label>
                    <?php if(Route::has('password.request')): ?>
                        <a href="<?php echo e(route('password.request')); ?>"
                           class="text-xs text-emerald-600 hover:text-emerald-700 hover:underline">
                            Mot de passe oublié ?
                        </a>
                    <?php endif; ?>
                </div>
                <div class="relative">
                    <span class="absolute left-3 top-1/2 -translate-y-1/2 text-gray-400">
                        <i class="ti ti-lock text-sm"></i>
                    </span>
                    <input
                        id="password"
                        type="password"
                        name="password"
                        required
                        autocomplete="current-password"
                        placeholder="••••••••"
                        class="input pl-9 <?php $__errorArgs = ['password'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> input-error <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>">
                    <button type="button"
                        onclick="togglePassword()"
                        class="absolute right-3 top-1/2 -translate-y-1/2 text-gray-400 hover:text-gray-600">
                        <i class="ti ti-eye text-sm" id="eye-icon"></i>
                    </button>
                </div>
            </div>

            <div class="flex items-center gap-2">
                <input type="checkbox" id="remember" name="remember"
                       class="w-4 h-4 rounded border-gray-300 text-emerald-600 cursor-pointer">
                <label for="remember" class="text-sm text-gray-600 cursor-pointer">Se souvenir de moi</label>
            </div>

            <button type="submit"
                class="w-full btn-primary justify-center py-2.5 text-sm font-medium">
                <i class="ti ti-login"></i> Se connecter
            </button>
        </form>
    </div>

    
    <div class="mt-4 bg-white rounded-xl border border-gray-200 p-4">
        <p class="text-xs font-medium text-gray-500 mb-2 flex items-center gap-1">
            <i class="ti ti-info-circle text-emerald-500"></i> Comptes de démonstration
        </p>
        <div class="space-y-1.5">
            <?php $__currentLoopData = [
                ['admin@comptoflow.ci',   'Administrateur', 'bg-emerald-100 text-emerald-700'],
                ['editeur@comptoflow.ci', 'Éditeur',        'bg-blue-100 text-blue-700'],
                ['lecteur@comptoflow.ci', 'Lecteur',        'bg-gray-100 text-gray-600'],
            ]; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as [$email, $role, $cls]): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
            <div class="flex items-center justify-between cursor-pointer hover:bg-gray-50 rounded-lg px-2 py-1 transition"
                 onclick="document.getElementById('email').value='<?php echo e($email); ?>';document.getElementById('password').value='password'">
                <span class="text-xs text-gray-600 font-mono"><?php echo e($email); ?></span>
                <span class="text-[10px] font-medium px-2 py-0.5 rounded-full <?php echo e($cls); ?>"><?php echo e($role); ?></span>
            </div>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
        </div>
        <p class="text-[10px] text-gray-400 mt-2">Cliquez sur un compte pour le sélectionner. Mot de passe : <code class="bg-gray-100 px-1 rounded">password</code></p>
    </div>

    <p class="text-center text-xs text-gray-400 mt-6">
        ComptoFlow © <?php echo e(now()->year); ?> — Plan comptable SYSCOHADA révisé
    </p>
</div>

<script>
function togglePassword() {
    const input = document.getElementById('password');
    const icon  = document.getElementById('eye-icon');
    if (input.type === 'password') {
        input.type = 'text';
        icon.className = 'ti ti-eye-off text-sm';
    } else {
        input.type = 'password';
        icon.className = 'ti ti-eye text-sm';
    }
}
</script>
</body>
</html>
<?php /**PATH C:\xampp\htdocs\Comptoflow\resources\views/auth/login.blade.php ENDPATH**/ ?>