<!DOCTYPE html>
<html lang="en">

@include('dashboard.partials._head')
<link rel="stylesheet" href="{{ asset('css/reservations.css') }}">

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
                <h4 class="font-weight-bold mb-0 d-none d-md-block mt-1">{{ auth()->user()->name ?? null }}</h4>
                <ul class="navbar-nav navbar-nav-right">
                    <li class="nav-item">
                    <h4 class="mb-0 font-weight-bold d-none d-xl-block">{{ now()->format('d/m/Y H:i') }}</h4>
                    </li>

                </ul>
                <button class="navbar-toggler navbar-toggler-right d-lg-none align-self-center" type="button" data-toggle="offcanvas">
                    <span class="mdi mdi-menu"></span>
                </button>
                </div>

                <div class="navbar-menu-wrapper navbar-search-wrapper d-none d-lg-flex align-items-center">
                    <form action="{{ route('reservations.index') }}" method="GET" class="d-flex align-items-center w-100">
                        <ul class="navbar-nav mr-lg-2" style="width:50%!important">
                            <li style="width:50%!important" class="nav-item nav-search d-none d-lg-block">
                                <div class="input-group">
                                    <input type="text"
                                        name="search"
                                        class="form-control"
                                        placeholder="Cerca per utente, libro, autore..."
                                        aria-label="search"
                                        value="{{ request('search') }}">
                                </div>
                            </li>
                        </ul>

                        <ul class="navbar-nav navbar-nav-right d-flex align-items-center">
                            <!-- Select filtri -->
                            <li class="nav-item me-2">
                                <select name="status" class="form-control">
                                    <option value="">Tutti gli stati</option>
                                    <option value="active" {{ request('status') == 'active' ? 'selected' : '' }}>Attive (In corso)</option>
                                    <option value="completed" {{ request('status') == 'completed' ? 'selected' : '' }}>Completate (Resi)</option>
                                    <option value="overdue" {{ request('status') == 'overdue' ? 'selected' : '' }}>In Ritardo</option> 
                                    <option value="cancelled" {{ request('status') == 'cancelled' ? 'selected' : '' }}>Annullate</option>
                                </select>
                            </li>

                            <!-- Bottone cerca -->
                            <li class="nav-item">
                                <div class="my-3">
                                    <button type="submit" class="btn btn-info btn-lg font-weight-small auth-form-btn">
                                        Cerca
                                    </button>
                                </div>
                            </li>
                            
                            <!-- Bottone reset -->
                            @if(request('search') || request('status'))
                            <li class="nav-item ms-2">
                                <div class="my-3">
                                    <a href="{{ route('reservations.index') }}" class="btn btn-secondary btn-lg font-weight-small">
                                        Reset
                                    </a>
                                </div>
                            </li>
                            @endif
                        </ul>
                    </form>
                </div>
            </nav>

            <!-- main panel -->
            <div class="main-panel">
                <div class="content-wrapper">
                    <div class="row">
                        <div class="col-lg-12 mb-3">
                            <!-- Statistiche Dashboard -->
                            <div class="row">
                                <div class="col-md-2">
                                    <div class="card bg-primary text-white">
                                        <div class="card-body p-3 text-center">
                                            <h3 class="mb-0" id="statTotal">{{ $stats['total'] ?? null }}</h3>
                                            <small>Totali</small>
                                        </div>
                                    </div>
                                </div>

                                <div class="col-md-2">
                                    <div class="card bg-success text-white">
                                        <div class="card-body p-3 text-center">
                                            <h3 class="mb-0" id="statActive">{{ $stats['active'] ?? null }}</h3>
                                            <small>Attive</small>
                                        </div>
                                    </div>
                                </div>

                                <div class="col-md-2">
                                    <div class="card bg-danger text-white">
                                        <div class="card-body p-3 text-center">
                                            <h3 class="mb-0" id="statOverdue">{{ $stats['overdue'] ?? null }}</h3>
                                            <small>In Ritardo</small>
                                        </div>
                                    </div>
                                </div>

                                <div class="col-md-2">
                                    <div class="card bg-info text-white">
                                        <div class="card-body p-3 text-center">
                                            <h3 class="mb-0" id="statCompleted">{{ $stats['completed'] ?? null }}</h3>
                                            <small>Completate</small>
                                        </div>
                                    </div>
                                </div>

                                <div class="col-md-2">
                                    <div class="card bg-secondary text-white">
                                        <div class="card-body p-3 text-center">
                                            <h3 class="mb-0" id="statCancelled">{{ $stats['cancelled'] ?? null }}</h3>
                                            <small>Annullate</small>
                                        </div>
                                    </div>
                                </div>

                                <div class="col-md-2">
                                    <div class="card bg-warning text-dark">
                                        <div class="card-body p-3 text-center">
                                            <h3 class="mb-0">
                                                {{ 
                                                    number_format(
                                                        (($stats['active'] ?? 0) / max(($stats['total'] ?? 0), 1)) * 100,
                                                        0
                                                    )
                                                }}%
                                            </h3>
                                            <small>Tasso Attive</small>
                                        </div>
                                    </div>
                                </div>
                            </div>

                        </div>
                    </div>

                    <div class="row">
                        <div class="col-lg-12 grid-margin stretch-card">
                            <div class="card">
                                <div class="card-body">
                                    <div class="d-flex justify-content-between align-items-center mb-3">
                                        <h4 class="card-title mb-0">Gestione Prenotazioni</h4>
                                        <div>
                                            <button type="button" class="btn btn-primary btn-sm" onclick="openCreateReservationModal()">
                                                <i class="mdi mdi-plus"></i> Nuova Prenotazione
                                            </button>
                                        </div>
                                    </div>

                                    <!-- Alert per messaggi -->
                                    <div id="alertContainer"></div>

                                    <!-- Azioni bulk -->
                                    <div class="mb-3" id="bulkActions" style="display:none;">
                                        <button class="btn btn-sm btn-success" onclick="bulkComplete()">
                                            <i class="mdi mdi-check-all"></i> Completa Selezionate
                                        </button>
                                        <button class="btn btn-sm btn-danger" onclick="bulkCancel()">
                                            <i class="mdi mdi-close-circle"></i> Annulla Selezionate
                                        </button>
                                        <span class="ml-2 text-muted" id="selectedCount">0 selezionate</span>
                                    </div>

                                    <div class="table-responsive">
                                        <table class="table table-hover">
                                            <thead>
                                                <tr>
                                                    <th width="30">
                                                        <input type="checkbox" id="selectAll" onchange="toggleSelectAll()">
                                                    </th>
                                                    <th>ID</th>
                                                    <th>Utente</th>
                                                    <th>Libro</th>
                                                    <th>Barcode</th>
                                                    <th>Categoria</th>
                                                    <th>Stato</th>
                                                    <th>Prenotato il</th>
                                                    <th>Scadenza</th>
                                                    <th>Estensioni</th>
                                                    <th>Azioni</th>
                                                </tr>
                                            </thead>
                                            <tbody id="reservationsTableBody">
                                                @forelse(($reservations ?? []) as $reservation)
                                                @php
                                                    $isOverdue = $reservation->isOverdue();
                                                    $dueDateClass = $isOverdue ? 'text-danger font-weight-bold blink' : '';
                                                @endphp
                                                <tr data-reservation-id="{{ $reservation->id }}" 
                                                    data-status="{{ $reservation->status }}"
                                                    data-user-id="{{ $reservation->user_id }}"
                                                    data-overdue="{{ $isOverdue ? '1' : '0' }}">
                                                    <td>
                                                        @if($reservation->status === 'active')
                                                            <input type="checkbox" class="reservation-checkbox" value="{{ $reservation->id }}">
                                                        @endif
                                                    </td>
                                                    <td>{{ $reservation->id }}</td>
                                                    <td>
                                                        <strong>{{ $reservation->user->name }}</strong><br>
                                                        <small class="text-muted">{{ $reservation->user->email }}</small>
                                                    </td>
                                                    <td>
                                                        <strong>{{ $reservation->bookCopy->book->title }}</strong><br>
                                                        <small class="text-muted">{{ $reservation->bookCopy->book->author }}</small>
                                                    </td>
                                                    <td>
                                                        <code>{{ $reservation->bookCopy->barcode }}</code>
                                                    </td>
                                                    <td>
                                                        <span class="badge badge-info">{{ $reservation->bookCopy->book->category->name }}</span>
                                                    </td>
                                                    <td>
                                                        <span class="badge {{ $reservation->status_badge_class }}">
                                                            {{ $reservation->status_label }}
                                                        </span>
                                                        @if($isOverdue)
                                                            <br><small class="text-danger">{{ abs($reservation->days_overdue) }} gg ritardo</small>
                                                        @endif
                                                    </td>
                                                    <td>{{ $reservation->reserved_at->format('d/m/Y') }}</td>
                                                    <td class="{{ $dueDateClass }}">
                                                        {{ $reservation->due_date->format('d/m/Y') }}
                                                        @if($reservation->status === 'active' && !$isOverdue)
                                                            <br><small class="text-muted">{{ $reservation->days_until_due }} giorni</small>
                                                        @endif
                                                    </td>
                                                    <td>
                                                        @if($reservation->extended_count > 0)
                                                            <span class="badge badge-warning">{{ $reservation->extended_count }}</span>
                                                        @else
                                                            -
                                                        @endif
                                                    </td>
                                                    <td>
                                                        <button class="btn btn-sm btn-info" onclick="viewReservationDetails({{ $reservation->id }})" title="Dettagli">
                                                            <i class="mdi mdi-eye"></i>
                                                        </button>
                                                        @if($reservation->status === 'active')
                                                            <button class="btn btn-sm btn-success" onclick="completeReservation({{ $reservation->id }})" title="Segna come restituito">
                                                                <i class="mdi mdi-check"></i>
                                                            </button>
                                                            <button class="btn btn-sm btn-warning" onclick="extendReservation({{ $reservation->id }})" title="Estendi">
                                                                <i class="mdi mdi-clock-outline"></i>
                                                            </button>
                                                            <button class="btn btn-sm btn-danger" onclick="cancelReservation({{ $reservation->id }})" title="Annulla">
                                                                <i class="mdi mdi-close"></i>
                                                            </button>
                                                        @else
                                                            <button class="btn btn-sm btn-secondary" onclick="deleteReservation({{ $reservation->id }})" title="Elimina">
                                                                <i class="mdi mdi-delete"></i>
                                                            </button>
                                                        @endif
                                                    </td>
                                                </tr>
                                                @empty
                                                <tr>
                                                    <td colspan="11" class="text-center text-muted">Nessuna prenotazione presente.</td>
                                                </tr>
                                                @endforelse
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Modal Details Prenotazione -->
                    <div class="modal fade" id="detailsModal" tabindex="-1" role="dialog">
                        <div class="modal-dialog modal-lg" role="document">
                            <div class="modal-content">
                                <div class="modal-header">
                                    <h5 class="modal-title">Dettagli Prenotazione #<span id="detailsId"></span></h5>
                                    <button type="button" class="close" data-dismiss="modal">
                                        <span>&times;</span>
                                    </button>
                                </div>
                                <div class="modal-body">
                                    <div class="row">
                                        <div class="col-md-6">
                                            <h6 class="text-muted mb-3">Informazioni Utente</h6>
                                            <p><strong>Nome:</strong> <span id="detailsUserName"></span></p>
                                            <p><strong>Email:</strong> <span id="detailsUserEmail"></span></p>
                                            <p><strong>ID Utente:</strong> <span id="detailsUserId"></span></p>
                                        </div>
                                        <div class="col-md-6">
                                            <h6 class="text-muted mb-3">Informazioni Libro</h6>
                                            <p><strong>Titolo:</strong> <span id="detailsBookTitle"></span></p>
                                            <p><strong>Autore:</strong> <span id="detailsBookAuthor"></span></p>
                                            <p><strong>Barcode:</strong> <code id="detailsBarcode"></code></p>
                                            <p><strong>Categoria:</strong> <span id="detailsCategory"></span></p>
                                        </div>
                                    </div>
                                    <hr>
                                    <div class="row">
                                        <div class="col-md-6">
                                            <h6 class="text-muted mb-3">Date e Scadenze</h6>
                                            <p><strong>Prenotato il:</strong> <span id="detailsReservedAt"></span></p>
                                            <p><strong>Scadenza:</strong> <span id="detailsDueDate"></span></p>
                                            <p><strong>Restituito il:</strong> <span id="detailsReturnedAt">-</span></p>
                                            <p><strong>Durata prestito:</strong> <span id="detailsDuration"></span> giorni</p>
                                        </div>
                                        <div class="col-md-6">
                                            <h6 class="text-muted mb-3">Stato e Estensioni</h6>
                                            <p><strong>Stato:</strong> <span id="detailsStatus"></span></p>
                                            <p><strong>Estensioni:</strong> <span id="detailsExtensions"></span></p>
                                            <p><strong>Giorni rimanenti:</strong> <span id="detailsDaysRemaining"></span></p>
                                            <p><strong>In ritardo di:</strong> <span id="detailsOverdueDays">-</span></p>
                                        </div>
                                    </div>
                                </div>
                                <div class="modal-footer">
                                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Chiudi</button>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Modal Create Prenotazione -->
                    <div class="modal fade" id="createReservationModal" tabindex="-1" role="dialog">
                        <div class="modal-dialog" role="document">
                            <div class="modal-content">
                                <div class="modal-header">
                                    <h5 class="modal-title">Nuova Prenotazione</h5>
                                    <button type="button" class="close" data-dismiss="modal">
                                        <span>&times;</span>
                                    </button>
                                </div>
                                <form id="createReservationForm">
                                    @csrf
                                    <div class="modal-body">
                                        <div class="form-group">
                                            <label for="createUserId">Utente <span class="text-danger">*</span></label>
                                            <select class="form-control" id="createUserId" name="user_id" required>
                                                <option value="">Seleziona utente</option>
                                                @foreach(($users ?? []) as $user)
                                                    <option value="{{ $user->id }}">
                                                        {{ $user->name }} ({{ $user->email }})
                                                    </option>
                                                @endforeach

                                            </select>
                                            <small class="form-text text-danger d-none" id="createUserIdError"></small>
                                        </div>

                                        <div class="form-group">
                                            <label for="createBookId">Libro <span class="text-danger">*</span></label>
                                            <select class="form-control" id="createBookId" onchange="loadAvailableCopies()" required>
                                                <option value="">Seleziona libro</option>
                                                @forelse(\App\Models\Book::with('category')->orderBy('title')->get() as $book)
                                                    <option value="{{ $book->id }}">
                                                        {{ $book->title }} - {{ $book->author }}
                                                    </option>
                                                @empty
                                                    <option disabled>Nessun libro disponibile</option>
                                                @endforelse
                                            </select>
                                        </div>

                                        <div class="form-group">
                                            <label for="createBookCopyId">Copia Disponibile <span class="text-danger">*</span></label>
                                            <select class="form-control" id="createBookCopyId" name="book_copy_id" required disabled>
                                                <option value="">Prima seleziona un libro</option>
                                            </select>
                                            <small class="form-text text-danger d-none" id="createBookCopyIdError"></small>
                                        </div>

                                        <div class="form-group">
                                            <label for="createDueDate">Scadenza</label>
                                            <input type="date" class="form-control" id="createDueDate" name="due_date" 
                                                min="{{ now()->addDay()->format('Y-m-d') }}"
                                                value="{{ now()->addDays(14)->format('Y-m-d') }}">
                                            <small class="form-text text-muted">Default: 14 giorni da oggi</small>
                                            <small class="form-text text-danger d-none" id="createDueDateError"></small>
                                        </div>
                                    </div>
                                    <div class="modal-footer">
                                        <button type="button" class="btn btn-secondary" data-dismiss="modal">Annulla</button>
                                        <button type="submit" class="btn btn-primary" id="createReservationBtn">Crea Prenotazione</button>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>

                    <!-- Modal Extend Prenotazione -->
                    <div class="modal fade" id="extendModal" tabindex="-1" role="dialog">
                        <div class="modal-dialog" role="document">
                            <div class="modal-content">
                                <div class="modal-header">
                                    <h5 class="modal-title">Estendi Prestito</h5>
                                    <button type="button" class="close" data-dismiss="modal">
                                        <span>&times;</span>
                                    </button>
                                </div>
                                <form id="extendForm">
                                    @csrf
                                    <input type="hidden" id="extendReservationId">
                                    <div class="modal-body">
                                        <div class="form-group">
                                            <label for="extendDays">Giorni di Estensione <span class="text-danger">*</span></label>
                                            <input type="number" class="form-control" id="extendDays" name="days" value="7" min="1" max="30" required>
                                            <small class="form-text text-muted">Massimo 30 giorni</small>
                                            <small class="form-text text-danger d-none" id="extendDaysError"></small>
                                        </div>
                                    </div>
                                    <div class="modal-footer">
                                        <button type="button" class="btn btn-secondary" data-dismiss="modal">Annulla</button>
                                        <button type="submit" class="btn btn-warning">Estendi</button>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>
                    @include('dashboard.partials._footer')
                </div>
                <!-- content-wrapper ends -->
                <!-- partial:../../partials/_footer.html -->
                <!-- partial -->
            </div>
        <!-- main-panel ends -->
        </div>
        <!-- page-body-wrapper ends -->
    </div>
    <!-- container-scroller -->
    <!-- Plugin js for this page-->
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@4.6.0/dist/js/bootstrap.bundle.min.js"></script>
    <!-- End plugin js for this page-->
    <!-- inject:js -->
    <script src="{{ asset('js/dashboard/off-canvas.js') }}"></script>
    <script src="{{ asset('js/dashboard/hoverable-collapse.js') }}"></script>
    <script src="{{ asset('js/dashboard/template.js') }}"></script>
    <!-- endinject -->
    <!-- Custom js for this page-->
    <script src="{{ asset('js/dashboard/dashboard.js') }}"></script>
    <!-- End custom js for this page-->
    <!-- Active sidebar-->
    <script>
        document.addEventListener("DOMContentLoaded", function() {
            const reservations = document.getElementById('reservations');
            if (reservations) {
                reservations.classList.add('sidebar-active');
            }
        });
    </script>
    <!-- END Active sidebar-->
    <!-- CRUD popup and ajax-->

    <script>
        let currentReservationId = null;

        // URL helper Laravel
        const reservationsIndexUrl = "{{ route('reservations.index') }}";
        const storeReservationUrl = "{{ route('reservations.store') }}";
        const reservationUrlBase = "{{ url('reservations') }}";
        const completeReservationUrlBase = "{{ url('reservations') }}";
        const extendReservationUrlBase = "{{ url('reservations') }}";
        const cancelReservationUrlBase = "{{ url('reservations') }}";
        const bulkActionUrl = "{{ route('reservations.bulkAction') }}";
        const availableCopiesUrlBase = "{{ url('books') }}";

        // VISUALIZZAZIONE E FILTRI

        // Applica filtri
        function applyFilters() {
            const status = $('#filterStatus').val();
            const userId = $('#filterUser').val();
            
            $('#reservationsTableBody tr').each(function() {
                let show = true;
                
                if (status && $(this).data('status') !== status) {
                    show = false;
                }
                
                if (userId && $(this).data('user-id') != userId) {
                    show = false;
                }
                
                $(this).toggle(show);
            });
        }

        // Mostra solo in ritardo
        function showOverdueOnly() {
            $('#filterStatus').val('active');
            $('#reservationsTableBody tr').each(function() {
                const isOverdue = $(this).data('overdue') == '1';
                const isActive = $(this).data('status') === 'active';
                $(this).toggle(isOverdue && isActive);
            });
        }

        // Cerca prenotazioni
        function searchReservations() {
            const search = $('#searchInput').val().toLowerCase();
            
            $('#reservationsTableBody tr').each(function() {
                const text = $(this).text().toLowerCase();
                $(this).toggle(text.includes(search));
            });
        }

        // Reset filtri
        function clearFilters() {
            $('#filterStatus').val('');
            $('#filterUser').val('');
            $('#searchInput').val('');
            $('#reservationsTableBody tr').show();
        }

        // DETTAGLI PRENOTAZIONE

        function viewReservationDetails(id) {
            $.ajax({
                url: `${reservationUrlBase}/${id}`,
                type: 'GET',
                success: function(response) {
                    if (response.success) {
                        const r = response.reservation;
                        const info = response.info;
                        
                        $('#detailsId').text(r.id);
                        $('#detailsUserName').text(r.user.name);
                        $('#detailsUserEmail').text(r.user.email);
                        $('#detailsUserId').text(r.user.id);
                        $('#detailsBookTitle').text(r.book_copy.book.title);
                        $('#detailsBookAuthor').text(r.book_copy.book.author);
                        $('#detailsBarcode').text(r.book_copy.barcode);
                        $('#detailsCategory').text(r.book_copy.book.category.name);
                        $('#detailsReservedAt').text(new Date(r.reserved_at).toLocaleDateString('it-IT'));
                        $('#detailsDueDate').text(new Date(r.due_date).toLocaleDateString('it-IT'));
                        $('#detailsReturnedAt').text(r.returned_at ? new Date(r.returned_at).toLocaleDateString('it-IT') : '-');
                        $('#detailsStatus').html(getReservationStatusBadge(r.status));
                        $('#detailsExtensions').text(r.extended_count);
                        $('#detailsDuration').text(info.duration_days);
                        
                        if (info.is_overdue) {
                            $('#detailsDaysRemaining').text('-').parent().hide();
                            $('#detailsOverdueDays').text(info.days_overdue + ' giorni').parent().show();
                        } else if (r.status === 'active') {
                            $('#detailsDaysRemaining').text(info.days_until_due + ' giorni').parent().show();
                            $('#detailsOverdueDays').text('-').parent().hide();
                        } else {
                            $('#detailsDaysRemaining').text('-').parent().show();
                            $('#detailsOverdueDays').text('-').parent().hide();
                        }
                        
                        $('#detailsModal').modal('show');
                    }
                },
                error: function() {
                    showAlert('Errore durante il caricamento dei dettagli.', 'danger');
                }
            });
        }

        // CREA PRENOTAZIONE

        function openCreateReservationModal() {
            $('#createReservationForm')[0].reset();
            $('#createBookCopyId').prop('disabled', true).html('<option value="">Prima seleziona un libro</option>');
            $('.text-danger').addClass('d-none').text('');
            $('.form-control').removeClass('is-invalid');
            $('#createReservationModal').modal('show');
        }

        function loadAvailableCopies() {
            const bookId = $('#createBookId').val();
            
            if (!bookId) {
                $('#createBookCopyId').prop('disabled', true).html('<option value="">Prima seleziona un libro</option>');
                return;
            }
            
            $.ajax({
                url: `${availableCopiesUrlBase}/${bookId}/available-copies`,
                type: 'GET',
                success: function(response) {
                    if (response.success) {
                        const $select = $('#createBookCopyId');
                        $select.empty().prop('disabled', false);
                        
                        if (response.copies.length === 0) {
                            $select.append('<option value="">Nessuna copia disponibile</option>').prop('disabled', true);
                        } else {
                            $select.append('<option value="">Seleziona copia</option>');
                            response.copies.forEach(copy => {
                                const conditionLabel = getConditionLabel(copy.condition);
                                $select.append(`<option value="${copy.id}">${copy.barcode} - ${conditionLabel}</option>`);
                            });
                        }
                    }
                },
                error: function() {
                    showAlert('Errore durante il caricamento delle copie.', 'danger');
                }
            });
        }

        $('#createReservationForm').submit(function(e) {
            e.preventDefault();
            
            $('.text-danger').addClass('d-none').text('');
            $('.form-control').removeClass('is-invalid');
            $('#createReservationBtn').prop('disabled', true).text('Creazione...');
            
            $.ajax({
                url: storeReservationUrl,
                type: 'POST',
                data: $(this).serialize(),
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                success: function(response) {
                    if (response.success) {
                        $('#createReservationModal').modal('hide');
                        showAlert(response.message, 'success');
                        setTimeout(() => location.reload(), 1000);
                    }
                },
                error: function(xhr) {
                    const errors = xhr.responseJSON?.errors;
                    if (errors) {
                        Object.keys(errors).forEach(key => {
                            const fieldId = 'create' + key.charAt(0).toUpperCase() + key.slice(1).replace('_', '') + 'Error';
                            $(`#${fieldId}`).removeClass('d-none').text(errors[key][0]);
                            $(`[name="${key}"]`).addClass('is-invalid');
                        });
                    } else {
                        showAlert(xhr.responseJSON?.message || 'Errore durante la creazione.', 'danger');
                    }
                },
                complete: function() {
                    $('#createReservationBtn').prop('disabled', false).text('Crea Prenotazione');
                }
            });
        });

        // AZIONI PRENOTAZIONI

        function completeReservation(id) {
            if (confirm('Confermi la restituzione del libro?')) {
                $.ajax({
                    url: `${completeReservationUrlBase}/${id}/complete`,
                    type: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    },
                    success: function(response) {
                        if (response.success) {
                            showAlert(response.message, 'success');
                            setTimeout(() => location.reload(), 1000);
                        }
                    },
                    error: function(xhr) {
                        showAlert(xhr.responseJSON?.message || 'Errore.', 'danger');
                    }
                });
            }
        }

        function extendReservation(id) {
            currentReservationId = id;
            $('#extendReservationId').val(id);
            $('#extendDays').val(7);
            $('.text-danger').addClass('d-none').text('');
            $('#extendModal').modal('show');
        }

        $('#extendForm').submit(function(e) {
            e.preventDefault();
            const id = $('#extendReservationId').val();
            
            $.ajax({
                url: `${extendReservationUrlBase}/${id}/extend`,
                type: 'POST',
                data: $(this).serialize(),
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                success: function(response) {
                    if (response.success) {
                        $('#extendModal').modal('hide');
                        showAlert(response.message, 'success');
                        setTimeout(() => location.reload(), 1000);
                    }
                },
                error: function(xhr) {
                    const errors = xhr.responseJSON?.errors;
                    if (errors && errors.days) {
                        $('#extendDaysError').removeClass('d-none').text(errors.days[0]);
                    } else {
                        showAlert(xhr.responseJSON?.message || 'Errore.', 'danger');
                    }
                }
            });
        });

        function cancelReservation(id) {
            if (confirm('Sei sicuro di voler annullare questa prenotazione?')) {
                $.ajax({
                    url: `${cancelReservationUrlBase}/${id}/cancel`,
                    type: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    },
                    success: function(response) {
                        if (response.success) {
                            showAlert(response.message, 'success');
                            setTimeout(() => location.reload(), 1000);
                        }
                    },
                    error: function(xhr) {
                        showAlert(xhr.responseJSON?.message || 'Errore.', 'danger');
                    }
                });
            }
        }

        function deleteReservation(id) {
            if (confirm('Sei sicuro di voler eliminare questa prenotazione?')) {
                $.ajax({
                    url: `${reservationUrlBase}/${id}`,
                    type: 'DELETE',
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    },
                    success: function(response) {
                        if (response.success) {
                            $(`tr[data-reservation-id="${id}"]`).fadeOut(300, function() {
                                $(this).remove();
                            });
                            showAlert(response.message, 'success');
                        }
                    },
                    error: function(xhr) {
                        showAlert(xhr.responseJSON?.message || 'Errore.', 'danger');
                    }
                });
            }
        }

        // OPERAZIONI BULK

        function toggleSelectAll() {
            $('.reservation-checkbox').prop('checked', $('#selectAll').is(':checked'));
            updateBulkActions();
        }

        $('.reservation-checkbox').on('change', function() {
            updateBulkActions();
        });

        function updateBulkActions() {
            const selected = $('.reservation-checkbox:checked').length;
            if (selected > 0) {
                $('#bulkActions').show();
                $('#selectedCount').text(`${selected} selezionate`);
            } else {
                $('#bulkActions').hide();
                $('#selectAll').prop('checked', false);
            }
        }

        function bulkComplete() {
            const ids = $('.reservation-checkbox:checked').map(function() {
                return $(this).val();
            }).get();
            
            if (ids.length === 0) return;
            
            if (confirm(`Confermi la restituzione di ${ids.length} libri?`)) {
                performBulkAction('complete', ids);
            }
        }

        function bulkCancel() {
            const ids = $('.reservation-checkbox:checked').map(function() {
                return $(this).val();
            }).get();
            
            if (ids.length === 0) return;
            
            if (confirm(`Sei sicuro di voler annullare ${ids.length} prenotazioni?`)) {
                performBulkAction('cancel', ids);
            }
        }

        function performBulkAction(action, ids) {
            $.ajax({
                url: bulkActionUrl,
                type: 'POST',
                data: {
                    action: action,
                    reservation_ids: ids
                },
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                success: function(response) {
                    if (response.success) {
                        showAlert(response.message, 'success');
                        setTimeout(() => location.reload(), 1000);
                    }
                },
                error: function(xhr) {
                    showAlert(xhr.responseJSON?.message || 'Errore durante l\'operazione.', 'danger');
                }
            });
        }

        // UTILITY

        function showAlert(message, type) {
            const alertHtml = `
                <div class="alert alert-${type} alert-dismissible fade show" role="alert">
                    ${message}
                    <button type="button" class="close" data-dismiss="alert">
                        <span>&times;</span>
                    </button>
                </div>
            `;
            
            $('#alertContainer').html(alertHtml);
            
            setTimeout(() => {
                $('.alert').fadeOut(300, function() {
                    $(this).remove();
                });
            }, 5000);
        }

        function getReservationStatusBadge(status) {
            const badges = {
                'active': '<span class="badge badge-success">Attiva</span>',
                'completed': '<span class="badge badge-info">Completata</span>',
                'cancelled': '<span class="badge badge-danger">Annullata</span>'
            };
            return badges[status] || status;
        }

        function getConditionLabel(condition) {
            const labels = {
                'very good': 'Ottimo',
                'good': 'Buono',
                'bad': 'Discreto'
            };
            return labels[condition] || condition;
        }

        // Aggiorna checkbox quando si cambia pagina/filtro
        $(document).ready(function() {
            updateBulkActions();
            
            // Event delegation per checkbox dinamiche
            $(document).on('change', '.reservation-checkbox', function() {
                updateBulkActions();
            });
        });
    </script>

    <!-- END CRUD popup and ajax-->
    <!-- End custom js for this page-->
</body>

</html>
