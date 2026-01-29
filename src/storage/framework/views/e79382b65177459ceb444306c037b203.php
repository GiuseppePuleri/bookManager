<!DOCTYPE html>
<html>

<?php echo $__env->make('showcase.partials._head', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
<style>
    .active-reservations{
        color: #d16806 !important;
        outline: none;
    }
</style>

<body>

    <div class="preloader">
        <div class="loader"></div>
    </div>

    <?php echo $__env->make('showcase.partials._header', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>


    <section style="padding:100px 0px 100px 0px" id="login">
        <div class="container-fluid padding-side padding-small pt-5" data-aos="fade-up">
            <div class="row">
                <div class="col-md-6 col-lg-3 mb-4 mb-lg-0"></div>
                <div class="col-md-6 col-lg-3 offset-lg-1 mb-4 mb-lg-0">
                    <h4 class="display-6 fw-normal">Accedi per vedere le tue prenotazioni</h4>
                    <p>La sessione dura 120 minuti. Dopo devi effettuare nuovamente il login</p>
                    
                    
                    <?php if($errors->any()): ?>
                        <div class="alert alert-danger alert-dismissible fade show" role="alert">
                            <ul class="mb-0">
                                <?php $__currentLoopData = $errors->all(); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $error): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                    <li><?php echo e($error); ?></li>
                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                            </ul>
                            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                        </div>
                    <?php endif; ?>

                    
                    <form method="POST" action="<?php echo e(route('login.submit')); ?>" class="position-relative">
                        <?php echo csrf_field(); ?>
                        
                        <input 
                            type="email" 
                            name="email"
                            class="form-control px-4 py-3 bg-transparent mb-3 <?php $__errorArgs = ['email'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>" 
                            placeholder="La tua email"
                            value="<?php echo e(old('email')); ?>"
                            required
                            autofocus>
                        <?php $__errorArgs = ['email'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                            <div class="invalid-feedback d-block mb-2"><?php echo e($message); ?></div>
                        <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                        
                        <input 
                            type="password" 
                            name="password"
                            class="form-control px-4 py-3 bg-transparent <?php $__errorArgs = ['password'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>" 
                            placeholder="La tua password"
                            required>
                        <?php $__errorArgs = ['password'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                            <div class="invalid-feedback d-block"><?php echo e($message); ?></div>
                        <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                        
                        <div class="d-grid">
                            <button type="submit" class="btn btn-arrow btn-primary mt-3">
                                <span>Accedi
                                    <svg width="18" height="18">
                                        <use xlink:href="#arrow-right"></use>
                                    </svg>
                                </span>
                            </button>
                        </div>
                    </form>
                    
                    <p class="mt-3"><i>Non hai un account? <a href="#register" class="text-primary">Registrati</a></i></p>
                </div>
                <div class="col-md-6 col-lg-3 offset-lg-1 mb-4 mb-lg-0"></div>
            </div>
        </div>
        <hr class="text-black">
    </section>

    
    <section style="padding:100px 0px 100px 0px" id="register">
        <div class="container-fluid padding-side padding-small pt-0" data-aos="fade-up">
            <div class="row">
                <div class="col-md-6 col-lg-3 mb-4 mb-lg-0"></div>
                <div class="col-md-6 col-lg-3 offset-lg-1 mb-4 mb-lg-0">
                    <h4 class="display-6 fw-normal">Registrati per effettuare le tue prenotazioni</h4>
                    <p>Conserva le credenziali in un posto sicuro!</p>
                    
                    
                    <form method="POST" action="<?php echo e(route('register')); ?>" class="position-relative">
                        <?php echo csrf_field(); ?>
                        
                        <input 
                            type="text" 
                            name="name"
                            class="form-control px-4 py-3 bg-transparent mb-3 <?php $__errorArgs = ['name'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>" 
                            placeholder="Il tuo nome"
                            value="<?php echo e(old('name')); ?>"
                            required>
                        <?php $__errorArgs = ['name'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                            <div class="invalid-feedback d-block mb-2"><?php echo e($message); ?></div>
                        <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                        
                        <input 
                            type="email" 
                            name="email"
                            class="form-control px-4 py-3 bg-transparent mb-3 <?php $__errorArgs = ['email'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>" 
                            placeholder="La tua email"
                            value="<?php echo e(old('email')); ?>"
                            required>
                        <?php $__errorArgs = ['email'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                            <div class="invalid-feedback d-block mb-2"><?php echo e($message); ?></div>
                        <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                        
                        <input 
                            type="password" 
                            name="password"
                            class="form-control px-4 py-3 bg-transparent mb-3 <?php $__errorArgs = ['password'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>" 
                            placeholder="Password (min. 8 caratteri)"
                            required>
                        <?php $__errorArgs = ['password'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                            <div class="invalid-feedback d-block mb-2"><?php echo e($message); ?></div>
                        <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                        
                        <input 
                            type="password" 
                            name="password_confirmation"
                            class="form-control px-4 py-3 bg-transparent" 
                            placeholder="Conferma password"
                            required>
                        
                        <div class="d-grid">
                            <button type="submit" class="btn btn-arrow btn-primary mt-3">
                                <span>Registrati
                                    <svg width="18" height="18">
                                        <use xlink:href="#arrow-right"></use>
                                    </svg>
                                </span>
                            </button>
                        </div>
                    </form>
                    
                    <p class="mt-3"><i>Hai già un account? <a href="#login" class="text-primary">Accedi</a></i></p>
                </div>
                <div class="col-md-6 col-lg-3 offset-lg-1 mb-4 mb-lg-0"></div>
            </div>
        </div>
        <hr class="text-black">
    </section>

    <script>
        // Scroll automatico alla sezione registrazione se ci sono errori di registrazione
        <?php if($errors->has('name') || ($errors->has('password') && old('name'))): ?>
            document.addEventListener('DOMContentLoaded', function() {
                document.getElementById('register').scrollIntoView({ behavior: 'smooth' });
            });
        <?php endif; ?>
    </script>
    <script>
    document.addEventListener('DOMContentLoaded', function() {
        const loginSection = document.getElementById('login');
        const registerSection = document.getElementById('register');

        // Mostra solo login all'inizio
        loginSection.style.display = 'block';
        registerSection.style.display = 'none';

        // Funzione per mostrare register
        document.querySelectorAll('a[href="#register"]').forEach(link => {
            link.addEventListener('click', function(e) {
                e.preventDefault();
                loginSection.style.display = 'none';
                registerSection.style.display = 'block';
            });
        });

        // Funzione per tornare al login
        document.querySelectorAll('a[href="#login"]').forEach(link => {
            link.addEventListener('click', function(e) {
                e.preventDefault();
                registerSection.style.display = 'none';
                loginSection.style.display = 'block';
            });
        });
    });
    </script>

    <script src=<?php echo e(asset('js/showcase/jquery-1.11.0.min.js')); ?>></script>
    <script type="text/javascript" src=<?php echo e(asset('js/showcase/bootstrap.bundle.min.js')); ?>></script>
    <script type="text/javascript" src=<?php echo e(asset('js/showcase/plugins.js')); ?>></script>
    <script src="https://cdn.jsdelivr.net/npm/swiper/swiper-bundle.min.js"></script>
    <script src="https://unpkg.com/aos@2.3.1/dist/aos.js"></script>
    <script type="text/javascript" src=<?php echo e(asset('js/showcase/script.js')); ?>></script>
    <script src="https://code.iconify.design/iconify-icon/1.0.7/iconify-icon.min.js"></script>
</body>

</html><?php /**PATH /var/www/resources/views/showcase/pages/showcase-login.blade.php ENDPATH**/ ?>