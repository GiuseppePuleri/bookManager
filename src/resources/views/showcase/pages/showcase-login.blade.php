<!DOCTYPE html>
<html>

@include('showcase.partials._head')
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

    @include('showcase.partials._header')


    <section style="padding:100px 0px 100px 0px" id="login">
        <div class="container-fluid padding-side padding-small pt-5" data-aos="fade-up">
            <div class="row">
                <div class="col-md-6 col-lg-3 mb-4 mb-lg-0"></div>
                <div class="col-md-6 col-lg-3 offset-lg-1 mb-4 mb-lg-0">
                    <h4 class="display-6 fw-normal">Accedi per vedere le tue prenotazioni</h4>
                    <p>La sessione dura 120 minuti. Dopo devi effettuare nuovamente il login</p>
                    
                    {{-- Messaggi di errore --}}
                    @if ($errors->any())
                        <div class="alert alert-danger alert-dismissible fade show" role="alert">
                            <ul class="mb-0">
                                @foreach ($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                        </div>
                    @endif

                    {{-- Form Login --}}
                    <form method="POST" action="{{ route('login.submit') }}" class="position-relative">
                        @csrf
                        
                        <input 
                            type="email" 
                            name="email"
                            class="form-control px-4 py-3 bg-transparent mb-3 @error('email') is-invalid @enderror" 
                            placeholder="La tua email"
                            value="{{ old('email') }}"
                            required
                            autofocus>
                        @error('email')
                            <div class="invalid-feedback d-block mb-2">{{ $message }}</div>
                        @enderror
                        
                        <input 
                            type="password" 
                            name="password"
                            class="form-control px-4 py-3 bg-transparent @error('password') is-invalid @enderror" 
                            placeholder="La tua password"
                            required>
                        @error('password')
                            <div class="invalid-feedback d-block">{{ $message }}</div>
                        @enderror
                        
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

    {{-- Sezione Registrazione --}}
    <section style="padding:100px 0px 100px 0px" id="register">
        <div class="container-fluid padding-side padding-small pt-0" data-aos="fade-up">
            <div class="row">
                <div class="col-md-6 col-lg-3 mb-4 mb-lg-0"></div>
                <div class="col-md-6 col-lg-3 offset-lg-1 mb-4 mb-lg-0">
                    <h4 class="display-6 fw-normal">Registrati per effettuare le tue prenotazioni</h4>
                    <p>Conserva le credenziali in un posto sicuro!</p>
                    
                    {{-- Form Registrazione --}}
                    <form method="POST" action="{{ route('register') }}" class="position-relative">
                        @csrf
                        
                        <input 
                            type="text" 
                            name="name"
                            class="form-control px-4 py-3 bg-transparent mb-3 @error('name') is-invalid @enderror" 
                            placeholder="Il tuo nome"
                            value="{{ old('name') }}"
                            required>
                        @error('name')
                            <div class="invalid-feedback d-block mb-2">{{ $message }}</div>
                        @enderror
                        
                        <input 
                            type="email" 
                            name="email"
                            class="form-control px-4 py-3 bg-transparent mb-3 @error('email') is-invalid @enderror" 
                            placeholder="La tua email"
                            value="{{ old('email') }}"
                            required>
                        @error('email')
                            <div class="invalid-feedback d-block mb-2">{{ $message }}</div>
                        @enderror
                        
                        <input 
                            type="password" 
                            name="password"
                            class="form-control px-4 py-3 bg-transparent mb-3 @error('password') is-invalid @enderror" 
                            placeholder="Password (min. 8 caratteri)"
                            required>
                        @error('password')
                            <div class="invalid-feedback d-block mb-2">{{ $message }}</div>
                        @enderror
                        
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
        @if($errors->has('name') || ($errors->has('password') && old('name')))
            document.addEventListener('DOMContentLoaded', function() {
                document.getElementById('register').scrollIntoView({ behavior: 'smooth' });
            });
        @endif
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

    <script src={{ asset('js/showcase/jquery-1.11.0.min.js') }}></script>
    <script type="text/javascript" src={{ asset('js/showcase/bootstrap.bundle.min.js') }}></script>
    <script type="text/javascript" src={{ asset('js/showcase/plugins.js') }}></script>
    <script src="https://cdn.jsdelivr.net/npm/swiper/swiper-bundle.min.js"></script>
    <script src="https://unpkg.com/aos@2.3.1/dist/aos.js"></script>
    <script type="text/javascript" src={{ asset('js/showcase/script.js') }}></script>
    <script src="https://code.iconify.design/iconify-icon/1.0.7/iconify-icon.min.js"></script>
</body>

</html>