<!DOCTYPE html>
<html lang="en">

@include('dashboard.partials._head')


<body>
  <div class="container-scroller d-flex">
    <!-- partial:./partials/_sidebar.html -->
    @include('dashboard.partials._sidebar')

    
    <!-- partial -->
    <div class="container-fluid page-body-wrapper">
      <!-- partial:./partials/_navbar.html -->
      <nav style="background: url('{{ asset('assets/dashboard/images/other/nav-cover.png') }}') center center no-repeat !important; background-size: cover;" class="navbar col-lg-12 col-12 px-0 py-0 py-lg-4 d-flex flex-row">
        <div class="navbar-menu-wrapper d-flex align-items-center justify-content-end">
          <button class="navbar-toggler navbar-toggler align-self-center" type="button" data-toggle="minimize">
            <span class="mdi mdi-menu"></span>
          </button>
          <h4 class="font-weight-bold mb-0 d-none d-md-block mt-1">Welcome back, {{ auth()->user()->name ?? null }}</h4>
          <ul class="navbar-nav navbar-nav-right">
            <li class="nav-item">
              <h4 class="mb-0 font-weight-bold d-none d-xl-block">{{ now()->format('d/m/Y H:i') }}</h4>
            </li>

          </ul>
          <button class="navbar-toggler navbar-toggler-right d-lg-none align-self-center" type="button" data-toggle="offcanvas">
            <span class="mdi mdi-menu"></span>
          </button>
        </div>
      </nav>

      <!--  main panel -->
      <div class="main-panel">
        <div class="content-wrapper">
          <div class="row">
            <div class="col-12 col-xl-6 grid-margin stretch-card">
              <div class="row w-100 flex-grow">
                <div class="col-md-12 grid-margin stretch-card">
                  <!-- Totale libri per categoria -->
                  <div class="card">
                    <div class="card-body">
                      <p class="card-title">Totale libri per categoria</p>
                      <canvas id="booksByCategoryChart" height="200"></canvas>
                    </div>
                  </div>
                </div>
                <div class="col-md-6 stretch-card">
                  <!-- Copie disponibili vs prenotate -->
                  <div class="card">
                    <div class="card-body">
                      <p class="card-title">Totale copie disponibili vs prenotate</p>
                      <canvas id="copiesStatusChart" height="200"></canvas>
                    </div>
                  </div>
                </div>
                <div class="col-md-6 stretch-card">
                  <!-- Libri più prenotati -->
                  <div class="card">
                    <div class="card-body">
                      <p class="card-title">Libri più prenotati</p>
                      <canvas id="mostReservedBooksChart" height="200"></canvas>
                    </div>
                  </div>

                </div>
              </div>
            </div>
            <div class="col-12 col-xl-6 grid-margin stretch-card">
              <div class="row w-100 flex-grow">
                <div class="col-md-12 grid-margin stretch-card">
                  <!-- Prenotazioni attive per utente -->
                  <div class="card">
                    <div class="card-body">
                      <p class="card-title">Prenotazioni attive per utente</p>
                      <canvas id="activeReservationsByUserChart" height="200"></canvas>
                    </div>
                  </div>
                </div>
              </div>
            </div>
          </div>
          @include('dashboard.partials._footer')

        </div>
        <!-- content-wrapper ends -->
        <!-- partial:./partials/_footer.html -->

        <!-- partial -->
      </div>
      <!-- main-panel ends -->
    </div>
    <!-- page-body-wrapper ends -->
  </div>
  <!-- container-scroller -->

  <!-- Plugin js for this page-->
  <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

  <script>
    const booksByCategory = @json($booksByCategory);
    const copiesStatus = @json($copiesStatus);
    const mostReservedBooks = @json($mostReservedBooks);
    const activeReservationsByUser = @json($activeReservationsByUser);
  </script>

  <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@4.6.0/dist/js/bootstrap.bundle.min.js"></script>
  <!-- End plugin js for this page-->
  <!-- inject:js -->
  <script src="{{ asset('js/dashboard/off-canvas.js') }}"></script>
  <script src="{{ asset('js/dashboard/hoverable-collapse.js') }}"></script>
  <!-- endinject -->
  <!-- Custom js for this page-->
    <script src="{{ asset('js/dashboard/dashboard.js') }}"></script>

  <!-- End custom js for this page-->

  <!-- Active sidebar-->
  <script>
    document.addEventListener("DOMContentLoaded", function() {
        const dashboard = document.getElementById('dashboard');
        if (dashboard) {
            dashboard.classList.add('sidebar-active');
        }
    });
  </script>
  <!-- END Active sidebar-->
</body>

</html>