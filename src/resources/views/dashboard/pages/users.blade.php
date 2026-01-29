<!DOCTYPE html>
<html lang="en">

@include('dashboard.partials._head')
<link rel="stylesheet" href="{{ asset('css/dashboard/users.css') }}">

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
                    <form action="{{ route('users.index') }}" method="GET" class="d-flex align-items-center w-100">
                        <ul class="navbar-nav mr-lg-2" style="width:50%!important">
                            <li style="width:50%!important" class="nav-item nav-search d-none d-lg-block">
                                <div class="input-group">
                                    <input type="text"
                                        name="search"
                                        class="form-control"
                                        placeholder="Cerca utente per nome o email..."
                                        aria-label="search"
                                        value="{{ request('search') }}">
                                </div>
                            </li>
                        </ul>

                        <ul class="navbar-nav navbar-nav-right d-flex align-items-center">
                            <!-- Bottone cerca -->
                            <li class="nav-item">
                                <div class="my-3">
                                    <button type="submit" class="btn btn-info btn-lg font-weight-small auth-form-btn">
                                        Cerca
                                    </button>
                                </div>
                            </li>
                            
                            <!-- Bottone reset -->
                            @if(request('search'))
                            <li class="nav-item ms-2">
                                <div class="my-3">
                                    <a href="{{ route('users.index') }}" class="btn btn-secondary btn-lg font-weight-small">
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
                        <div class="col-lg-12 grid-margin stretch-card">
                            <div class="card">
                                <div class="card-body">
                                    <div class="d-flex justify-content-between align-items-center mb-3">
                                        <h4 class="card-title mb-0">Gestione Utenti</h4>
                                        <button type="button" class="btn btn-primary btn-sm" onclick="openCreateUserModal()">
                                            <i class="mdi mdi-account-plus"></i> Nuovo Utente
                                        </button>
                                    </div>

                                    <!-- Alert per messaggi -->
                                    <div id="alertContainer"></div>

                                    <div class="table-responsive">
                                        <table class="table table-hover">
                                            <thead>
                                                <tr>
                                                    <th>ID</th>
                                                    <th>Nome</th>
                                                    <th>Email</th>
                                                    <th>Ruolo</th>
                                                    <th>Prenotazioni attive</th>
                                                    <th>Totale prenotazioni</th>
                                                    <th>Registrato il</th>
                                                    <th>Azioni</th>
                                                </tr>
                                            </thead>
                                            <tbody id="usersTableBody">
                                                @forelse($users as $user)
                                                <tr data-user-id="{{ $user->id }}">
                                                    <td>{{ $user->id }}</td>
                                                    <td><strong>{{ $user->name }}</strong></td>
                                                    <td>{{ $user->email }}</td>
                                                    <td>
                                                        <span class="badge {{ $user->role_badge_class }}" id="roleBadge{{ $user->id }}">
                                                            {{ $user->role_label }}
                                                        </span>
                                                    </td>
                                                    <td>
                                                        <span class="badge badge-success">{{ $user->active_reservations_count }}</span>
                                                    </td>
                                                    <td>
                                                        <span class="badge badge-info">{{ $user->reservations_count }}</span>
                                                    </td>
                                                    <td>{{ $user->created_at->format('d/m/Y') }}</td>
                                                    <td>
                                                        <button class="btn btn-sm btn-info" onclick="viewUserDetails({{ $user->id }})" title="Dettagli e prenotazioni">
                                                            <i class="mdi mdi-eye"></i>
                                                        </button>
                                                        @if($user->id !== auth()->id())
                                                            <button class="btn btn-sm btn-warning" onclick="toggleUserRole({{ $user->id }})" title="Cambia ruolo">
                                                                <i class="mdi mdi-account-key"></i>
                                                            </button>
                                                            <button class="btn btn-sm btn-primary" onclick="editUser({{ $user->id }})" title="Modifica utente">
                                                                <i class="mdi mdi-pencil"></i>
                                                            </button>
                                                            <button class="btn btn-sm btn-danger" onclick="deleteUser({{ $user->id }})" title="Elimina utente">
                                                                <i class="mdi mdi-delete"></i>
                                                            </button>
                                                        @else
                                                            <span class="badge badge-secondary">Tu</span>
                                                        @endif
                                                    </td>
                                                </tr>
                                                @empty
                                                <tr>
                                                    <td colspan="8" class="text-center text-muted">Nessun utente presente.</td>
                                                </tr>
                                                @endforelse
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Modal create Utente -->
                    <div class="modal fade" id="createUserModal" tabindex="-1" role="dialog">
                        <div class="modal-dialog" role="document">
                            <div class="modal-content">
                                <div class="modal-header">
                                    <h5 class="modal-title">Nuovo Utente</h5>
                                    <button type="button" class="close" data-dismiss="modal">
                                        <span>&times;</span>
                                    </button>
                                </div>
                                <form id="createUserForm">
                                    @csrf
                                    <div class="modal-body">
                                        <div class="form-group">
                                            <label for="createUserName">Nome <span class="text-danger">*</span></label>
                                            <input type="text" class="form-control" id="createUserName" name="name" required maxlength="255">
                                            <small class="form-text text-danger d-none" id="createNameError"></small>
                                        </div>

                                        <div class="form-group">
                                            <label for="createUserEmail">Email <span class="text-danger">*</span></label>
                                            <input type="email" class="form-control" id="createUserEmail" name="email" required maxlength="255">
                                            <small class="form-text text-danger d-none" id="createEmailError"></small>
                                        </div>

                                        <div class="form-group">
                                            <label for="createUserPassword">Password <span class="text-danger">*</span></label>
                                            <input type="password" class="form-control" id="createUserPassword" name="password" required minlength="8">
                                            <small class="form-text text-muted">Minimo 8 caratteri</small>
                                            <small class="form-text text-danger d-none" id="createPasswordError"></small>
                                        </div>

                                        <div class="form-group">
                                            <label for="createUserRole">Ruolo <span class="text-danger">*</span></label>
                                            <select class="form-control" id="createUserRole" name="role" required>
                                                <option value="user">Utente</option>
                                                <option value="admin">Admin</option>
                                            </select>
                                            <small class="form-text text-danger d-none" id="createRoleError"></small>
                                        </div>
                                    </div>
                                    <div class="modal-footer">
                                        <button type="button" class="btn btn-secondary" data-dismiss="modal">Annulla</button>
                                        <button type="submit" class="btn btn-primary" id="createUserSubmitBtn">Crea Utente</button>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>

                    <!-- Modal edit Utente -->
                    <div class="modal fade" id="editUserModal" tabindex="-1" role="dialog">
                        <div class="modal-dialog" role="document">
                            <div class="modal-content">
                                <div class="modal-header">
                                    <h5 class="modal-title">Modifica Utente</h5>
                                    <button type="button" class="close" data-dismiss="modal">
                                        <span>&times;</span>
                                    </button>
                                </div>
                                <form id="editUserForm">
                                    @csrf
                                    <input type="hidden" id="editUserId">
                                    <div class="modal-body">
                                        <div class="form-group">
                                            <label for="editUserName">Nome <span class="text-danger">*</span></label>
                                            <input type="text" class="form-control" id="editUserName" name="name" required maxlength="255">
                                            <small class="form-text text-danger d-none" id="editNameError"></small>
                                        </div>

                                        <div class="form-group">
                                            <label for="editUserEmail">Email <span class="text-danger">*</span></label>
                                            <input type="email" class="form-control" id="editUserEmail" name="email" required maxlength="255">
                                            <small class="form-text text-danger d-none" id="editEmailError"></small>
                                        </div>

                                        <div class="form-group">
                                            <label for="editUserPassword">Nuova Password</label>
                                            <input type="password" class="form-control" id="editUserPassword" name="password" minlength="8">
                                            <small class="form-text text-muted">Lascia vuoto per non modificare</small>
                                            <small class="form-text text-danger d-none" id="editPasswordError"></small>
                                        </div>
                                    </div>
                                    <div class="modal-footer">
                                        <button type="button" class="btn btn-secondary" data-dismiss="modal">Annulla</button>
                                        <button type="submit" class="btn btn-primary" id="editUserSubmitBtn">Salva Modifiche</button>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>

                    <!-- Modal Ddetails -->
                    <div class="modal fade" id="userDetailsModal" tabindex="-1" role="dialog">
                        <div class="modal-dialog modal-xl" role="document">
                            <div class="modal-content">
                                <div class="modal-header">
                                    <div>
                                        <h5 class="modal-title" id="userDetailsName">Dettagli Utente</h5>
                                        <small class="text-muted" id="userDetailsEmail"></small>
                                    </div>
                                    <button type="button" class="close" data-dismiss="modal">
                                        <span>&times;</span>
                                    </button>
                                </div>
                                <div class="modal-body">
                                    <!-- Statistiche -->
                                    <div class="row mb-4">
                                        <div class="col-md-3">
                                            <div class="card bg-success text-white">
                                                <div class="card-body p-3 text-center">
                                                    <h3 class="mb-0" id="statsActive">0</h3>
                                                    <small>Prenotazioni Attive</small>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-md-3">
                                            <div class="card bg-danger text-white">
                                                <div class="card-body p-3 text-center">
                                                    <h3 class="mb-0" id="statsOverdue">0</h3>
                                                    <small>In Ritardo</small>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-md-3">
                                            <div class="card bg-info text-white">
                                                <div class="card-body p-3 text-center">
                                                    <h3 class="mb-0" id="statsCompleted">0</h3>
                                                    <small>Completate</small>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-md-3">
                                            <div class="card bg-secondary text-white">
                                                <div class="card-body p-3 text-center">
                                                    <h3 class="mb-0" id="statsTotal">0</h3>
                                                    <small>Totali</small>
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                    <!-- Filtri -->
                                    <div class="mb-3">
                                        <button class="btn btn-sm btn-outline-primary" onclick="filterReservations('all')">Tutte</button>
                                        <button class="btn btn-sm btn-outline-success" onclick="filterReservations('active')">Attive</button>
                                        <button class="btn btn-sm btn-outline-info" onclick="filterReservations('completed')">Completate</button>
                                        <button class="btn btn-sm btn-outline-danger" onclick="filterReservations('cancelled')">Annullate</button>
                                    </div>

                                    <!-- Tabella Prenotazioni -->
                                    <div class="table-responsive">
                                        <table class="table table-sm">
                                            <thead>
                                                <tr>
                                                    <th>Libro</th>
                                                    <th>Barcode Copia</th>
                                                    <th>Categoria</th>
                                                    <th>Prenotato il</th>
                                                    <th>Scadenza</th>
                                                    <th>Stato</th>
                                                    <th>Estensioni</th>
                                                    <th>Azioni</th>
                                                </tr>
                                            </thead>
                                            <tbody id="reservationsTableBody">
                                                <tr>
                                                    <td colspan="8" class="text-center text-muted">Caricamento...</td>
                                                </tr>
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Modal Confirm delete -->
                    <div class="modal fade" id="deleteUserModal" tabindex="-1" role="dialog">
                        <div class="modal-dialog" role="document">
                            <div class="modal-content">
                                <div class="modal-header bg-danger text-white">
                                    <h5 class="modal-title">Conferma Eliminazione</h5>
                                    <button type="button" class="close text-white" data-dismiss="modal">
                                        <span>&times;</span>
                                    </button>
                                </div>
                                <div class="modal-body">
                                    <p>Sei sicuro di voler eliminare questo utente?</p>
                                    <p class="text-muted">Verranno eliminate anche tutte le prenotazioni completate. Le prenotazioni attive bloccheranno l'eliminazione.</p>
                                </div>
                                <div class="modal-footer">
                                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Annulla</button>
                                    <button type="button" class="btn btn-danger" id="confirmDeleteUserBtn">Elimina</button>
                                </div>
                            </div>
                        </div>
                    </div>
                    @include('dashboard.partials._footer')
                </div>
                <!-- content-wrapper ends -->
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
            const users = document.getElementById('users');
            if (users) {
                users.classList.add('sidebar-active');
            }
        });
    </script>
    <!-- END Active sidebar-->

    <script>
        let currentUserId = null;
        let currentReservations = [];

        // URL helper Laravel
        const storeUserUrl = "{{ route('users.store') }}";
        const getUserUrlBase = "{{ url('users') }}";
        const updateUserUrlBase = "{{ url('users') }}";
        const deleteUserUrlBase = "{{ url('users') }}";
        const toggleRoleUrlBase = "{{ url('users') }}";
        const getReservationsUrlBase = "{{ url('users') }}";
        const completeReservationUrlBase = "{{ url('users') }}";
        const extendReservationUrlBase = "{{ url('users') }}";
        const cancelReservationUrlBase = "{{ url('users') }}";

        // GESTIONE UTENTI

        // Apri modal creazione utente
        function openCreateUserModal() {
            $('#createUserForm')[0].reset();
            $('.text-danger').addClass('d-none').text('');
            $('.form-control').removeClass('is-invalid');
            $('#createUserModal').modal('show');
        }

        // Crea utente
        $('#createUserForm').submit(function(e) {
            e.preventDefault();
            
            $('.text-danger').addClass('d-none').text('');
            $('.form-control').removeClass('is-invalid');
            $('#createUserSubmitBtn').prop('disabled', true).text('Creazione...');
            
            $.ajax({
                url: storeUserUrl,
                type: 'POST',
                data: $(this).serialize(),
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                success: function(response) {
                    if (response.success) {
                        $('#createUserModal').modal('hide');
                        showAlert(response.message, 'success');
                        setTimeout(() => location.reload(), 1000);
                    }
                },
                error: function(xhr) {
                    const errors = xhr.responseJSON?.errors;
                    if (errors) {
                        Object.keys(errors).forEach(key => {
                            const fieldId = 'create' + key.charAt(0).toUpperCase() + key.slice(1) + 'Error';
                            $(`#${fieldId}`).removeClass('d-none').text(errors[key][0]);
                            $(`#createUser${key.charAt(0).toUpperCase() + key.slice(1)}`).addClass('is-invalid');
                        });
                    } else {
                        showAlert('Errore durante la creazione dell\'utente.', 'danger');
                    }
                },
                complete: function() {
                    $('#createUserSubmitBtn').prop('disabled', false).text('Crea Utente');
                }
            });
        });

        // Modifica utente
        function editUser(userId) {
            $.ajax({
                url: `${getUserUrlBase}/${userId}`,
                type: 'GET',
                success: function(response) {
                    if (response.success) {
                        const user = response.user;
                        $('#editUserId').val(user.id);
                        $('#editUserName').val(user.name);
                        $('#editUserEmail').val(user.email);
                        $('#editUserPassword').val('');
                        $('.text-danger').addClass('d-none').text('');
                        $('.form-control').removeClass('is-invalid');
                        $('#editUserModal').modal('show');
                    }
                },
                error: function() {
                    showAlert('Errore durante il caricamento dell\'utente.', 'danger');
                }
            });
        }

        // Salva modifiche utente
        $('#editUserForm').submit(function(e) {
            e.preventDefault();
            
            const userId = $('#editUserId').val();
            
            $('.text-danger').addClass('d-none').text('');
            $('.form-control').removeClass('is-invalid');
            $('#editUserSubmitBtn').prop('disabled', true).text('Salvataggio...');
            
            $.ajax({
                url: `${updateUserUrlBase}/${userId}`,
                type: 'PUT',
                data: $(this).serialize(),
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                success: function(response) {
                    if (response.success) {
                        $('#editUserModal').modal('hide');
                        showAlert(response.message, 'success');
                        setTimeout(() => location.reload(), 1000);
                    }
                },
                error: function(xhr) {
                    const errors = xhr.responseJSON?.errors;
                    if (errors) {
                        Object.keys(errors).forEach(key => {
                            const fieldId = 'edit' + key.charAt(0).toUpperCase() + key.slice(1) + 'Error';
                            $(`#${fieldId}`).removeClass('d-none').text(errors[key][0]);
                            $(`#editUser${key.charAt(0).toUpperCase() + key.slice(1)}`).addClass('is-invalid');
                        });
                    } else {
                        showAlert('Errore durante l\'aggiornamento.', 'danger');
                    }
                },
                complete: function() {
                    $('#editUserSubmitBtn').prop('disabled', false).text('Salva Modifiche');
                }
            });
        });

        // Toggle ruolo
        function toggleUserRole(userId) {
            if (confirm('Sei sicuro di voler cambiare il ruolo di questo utente?')) {
                $.ajax({
                    url: `${toggleRoleUrlBase}/${userId}/toggle-role`,
                    type: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    },
                    success: function(response) {
                        if (response.success) {
                            const badgeClass = response.new_role === 'admin' ? 'badge-danger' : 'badge-secondary';
                            const badgeText = response.new_role === 'admin' ? 'Admin' : 'Utente';
                            $(`#roleBadge${userId}`).removeClass('badge-danger badge-secondary').addClass(badgeClass).text(badgeText);
                            showAlert(response.message, 'success');
                        }
                    },
                    error: function(xhr) {
                        showAlert(xhr.responseJSON?.message || 'Errore durante la modifica del ruolo.', 'danger');
                    }
                });
            }
        }

        // Elimina utente
        function deleteUser(userId) {
            currentUserId = userId;
            $('#deleteUserModal').modal('show');
        }

        $('#confirmDeleteUserBtn').click(function() {
            $.ajax({
                url: `${deleteUserUrlBase}/${currentUserId}`,
                type: 'DELETE',
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                success: function(response) {
                    $('#deleteUserModal').modal('hide');
                    if (response.success) {
                        $(`tr[data-user-id="${currentUserId}"]`).fadeOut(300, function() {
                            $(this).remove();
                        });
                        showAlert(response.message, 'success');
                    }
                },
                error: function(xhr) {
                    $('#deleteUserModal').modal('hide');
                    showAlert(xhr.responseJSON?.message || 'Errore durante l\'eliminazione.', 'danger');
                }
            });
        });

        // DETTAGLI E PRENOTAZIONI

        // Visualizza dettagli utente
        function viewUserDetails(userId) {
            currentUserId = userId;
            
            $.ajax({
                url: `${getUserUrlBase}/${userId}`,
                type: 'GET',
                success: function(response) {
                    if (response.success) {
                        const user = response.user;
                        const stats = response.stats;
                        
                        $('#userDetailsName').text(user.name);
                        $('#userDetailsEmail').text(user.email);
                        
                        $('#statsActive').text(stats.active_reservations);
                        $('#statsOverdue').text(stats.overdue_reservations);
                        $('#statsCompleted').text(stats.completed_reservations);
                        $('#statsTotal').text(stats.total_reservations);
                        
                        currentReservations = user.reservations;
                        loadReservationsTable(user.reservations);
                        
                        $('#userDetailsModal').modal('show');
                    }
                },
                error: function() {
                    showAlert('Errore durante il caricamento dei dettagli.', 'danger');
                }
            });
        }

        // Carica tabella prenotazioni
        function loadReservationsTable(reservations) {
            const tbody = $('#reservationsTableBody');
            tbody.empty();
            
            if (reservations.length === 0) {
                tbody.append('<tr><td colspan="8" class="text-center text-muted">Nessuna prenotazione.</td></tr>');
                return;
            }
            
            reservations.forEach(res => {
                const book = res.book_copy.book;
                const statusBadge = getReservationStatusBadge(res.status);
                const reservedDate = new Date(res.reserved_at).toLocaleDateString('it-IT');
                const dueDate = new Date(res.due_date).toLocaleDateString('it-IT');
                
                const isOverdue = res.status === 'active' && new Date(res.due_date) < new Date();
                const dueDateClass = isOverdue ? 'text-danger font-weight-bold' : '';
                
                let actions = '';
                if (res.status === 'active') {
                    actions = `
                        <button class="btn btn-xs btn-success" onclick="completeReservation(${res.id})" title="Segna come restituito">
                            <i class="mdi mdi-check"></i>
                        </button>
                        <button class="btn btn-xs btn-info" onclick="extendReservation(${res.id})" title="Estendi prestito">
                            <i class="mdi mdi-clock-outline"></i>
                        </button>
                        <button class="btn btn-xs btn-danger" onclick="cancelReservation(${res.id})" title="Annulla">
                            <i class="mdi mdi-close"></i>
                        </button>
                    `;
                } else {
                    actions = '-';
                }
                
                const row = `
                    <tr data-reservation-id="${res.id}" data-status="${res.status}">
                        <td><strong>${book.title}</strong><br><small class="text-muted">${book.author}</small></td>
                        <td><code>${res.book_copy.barcode}</code></td>
                        <td><span class="badge badge-info">${book.category.name}</span></td>
                        <td>${reservedDate}</td>
                        <td class="${dueDateClass}">${dueDate}</td>
                        <td>${statusBadge}</td>
                        <td>${res.extended_count}</td>
                        <td>${actions}</td>
                    </tr>
                `;
                tbody.append(row);
            });
        }

        // Filtra prenotazioni
        function filterReservations(status) {
            if (status === 'all') {
                $('#reservationsTableBody tr').show();
            } else {
                $('#reservationsTableBody tr').each(function() {
                    const rowStatus = $(this).data('status');
                    if (rowStatus === status) {
                        $(this).show();
                    } else {
                        $(this).hide();
                    }
                });
            }
        }

        // Completa prenotazione
        function completeReservation(reservationId) {
            if (confirm('Confermi la restituzione del libro?')) {
                $.ajax({
                    url: `${completeReservationUrlBase}/${currentUserId}/reservations/${reservationId}/complete`,
                    type: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    },
                    success: function(response) {
                        if (response.success) {
                            showAlert(response.message, 'success');
                            viewUserDetails(currentUserId); // Ricarica
                        }
                    },
                    error: function(xhr) {
                        showAlert(xhr.responseJSON?.message || 'Errore durante la restituzione.', 'danger');
                    }
                });
            }
        }

        // Estendi prenotazione
        function extendReservation(reservationId) {
            const days = prompt('Per quanti giorni vuoi estendere il prestito?', '7');
            if (days) {
                $.ajax({
                    url: `${extendReservationUrlBase}/${currentUserId}/reservations/${reservationId}/extend`,
                    type: 'POST',
                    data: { days: days },
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    },
                    success: function(response) {
                        if (response.success) {
                            showAlert(response.message, 'success');
                            viewUserDetails(currentUserId); // Ricarica
                        }
                    },
                    error: function(xhr) {
                        showAlert(xhr.responseJSON?.message || 'Errore durante l\'estensione.', 'danger');
                    }
                });
            }
        }

        // Annulla prenotazione
        function cancelReservation(reservationId) {
            if (confirm('Sei sicuro di voler annullare questa prenotazione?')) {
                $.ajax({
                    url: `${cancelReservationUrlBase}/${currentUserId}/reservations/${reservationId}/cancel`,
                    type: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    },
                    success: function(response) {
                        if (response.success) {
                            showAlert(response.message, 'success');
                            viewUserDetails(currentUserId); // Ricarica
                        }
                    },
                    error: function(xhr) {
                        showAlert(xhr.responseJSON?.message || 'Errore durante l\'annullamento.', 'danger');
                    }
                });
            }
        }

        // UTILITY

        // Mostra alert
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

        // Get reservation status badge
        function getReservationStatusBadge(status) {
            const badges = {
                'active': '<span class="badge badge-success">Attiva</span>',
                'completed': '<span class="badge badge-info">Completata</span>',
                'cancelled': '<span class="badge badge-danger">Annullata</span>'
            };
            return badges[status] || status;
        }
</script>
    <!-- End custom js for this page-->
</body>

</html>
