<!DOCTYPE html>
<html>
@include('showcase.partials._head')

<body>
    <div class="preloader">
        <div class="loader"></div>
    </div>

    @include('showcase.partials._header')


    <section id="footer" style="padding:150px 0px 150px 0px!important;">
        <div class="container-fluid padding-side padding-small pt-0" data-aos="fade-up">
        <footer class="row">
            <div class="col-md-6 col-lg-3 mb-4 mb-lg-0">
                @error('email') <p>{{ $message }}</p> @enderror
                @error('password') <p>{{ $message }}</p> @enderror
            </div>
            <div class="col-md-6 col-lg-3 offset-lg-1 mb-4 mb-lg-0">
                <h4 class="display-6 fw-normal">Accedi al pannello amministrazione</h4>
                <p>Solo se hai le credenziali da admin puoi accedere al pannello. <br><code>[Seeder Credentials] <br> | Email:admin@library.local <br> | Pass:admin123</code></p>
                <form method="POST" action="{{ route('login.submit') }}" class=" position-relative">
                    @csrf
                    <input type="email" name="email" class="form-control px-4 py-3 bg-transparent" placeholder="Your email">
                    <input type="password" name="password" class="form-control px-4 py-3 bg-transparent mb-3" placeholder="Your Password">
                    <div class="d-grid">
                    <button href="#" class="btn btn-arrow btn-primary mt-3">
                        <span>Accedi<svg xmlns="http://www.w3.org/2000/svg" width="10" height="10" fill="currentColor" class="bi bi-lock" viewBox="0 0 16 16">
                        <path fill-rule="evenodd" d="M8 0a4 4 0 0 1 4 4v2.05a2.5 2.5 0 0 1 2 2.45v5a2.5 2.5 0 0 1-2.5 2.5h-7A2.5 2.5 0 0 1 2 13.5v-5a2.5 2.5 0 0 1 2-2.45V4a4 4 0 0 1 4-4M4.5 7A1.5 1.5 0 0 0 3 8.5v5A1.5 1.5 0 0 0 4.5 15h7a1.5 1.5 0 0 0 1.5-1.5v-5A1.5 1.5 0 0 0 11.5 7zM8 1a3 3 0 0 0-3 3v2h6V4a3 3 0 0 0-3-3"/>
                        </svg></span>
                    </button>
                    </div>
                </form>
            </div>
            <div class="col-md-6 col-lg-3 offset-lg-1 mb-4 mb-lg-0"></div>
        </footer>
        </div>
        <hr class="text-black">

    </section>

  <script src={{ asset('js/showcase/jquery-1.11.0.min.js') }}></script>
  <script type="text/javascript" src={{ asset('js/showcase/bootstrap.bundle.min.js') }}></script>
  <script type="text/javascript" src={{ asset('js/showcase/plugins.js') }}></script>
  <script src="https://cdn.jsdelivr.net/npm/swiper/swiper-bundle.min.js"></script>
  <script src="https://unpkg.com/aos@2.3.1/dist/aos.js"></script>
  <script type="text/javascript" src={{ asset('js/showcase/script.js') }}></script>
  <script src="https://code.iconify.design/iconify-icon/1.0.7/iconify-icon.min.js"></script>
</body>

</html>