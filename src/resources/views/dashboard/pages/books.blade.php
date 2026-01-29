<!DOCTYPE html>
<html lang="en">

@include('dashboard.partials._head')
<link rel="stylesheet" href="{{ asset('css/dashboard/books.css') }}">

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
                    <form action="{{ route('books.index') }}" method="GET" class="d-flex align-items-center w-100">
                        <ul class="navbar-nav mr-lg-2" style="width:50%!important">
                            <li style="width:50%!important" class="nav-item nav-search d-none d-lg-block">
                                <div class="input-group">
                                    <input type="text"
                                        name="search"
                                        class="form-control"
                                        placeholder="Cerca..."
                                        aria-label="search"
                                        value="{{ request('search') }}">
                                </div>
                            </li>
                        </ul>

                        <ul class="navbar-nav navbar-nav-right d-flex align-items-center">
                            <!-- Select filtri -->
                            <li class="nav-item me-2">
                                <select name="filter" class="form-control">
                                    <option value="all" {{ request('filter') == 'all' ? 'selected' : '' }}>Tutti i campi</option>
                                    <option value="title" {{ request('filter') == 'title' ? 'selected' : '' }}>Titolo</option>
                                    <option value="author" {{ request('filter') == 'author' ? 'selected' : '' }}>Autore</option>
                                    <option value="publisher" {{ request('filter') == 'publisher' ? 'selected' : '' }}>Editore</option>
                                    <option value="category" {{ request('filter') == 'category' ? 'selected' : '' }}>Categoria</option>
                                    <option value="isbn" {{ request('filter') == 'isbn' ? 'selected' : '' }}>ISBN</option>
                                    <option value="keyword" {{ request('filter') == 'keyword' ? 'selected' : '' }}>Parola chiave</option>
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
                            
                            <!-- Bottone reset (opzionale ma utile) -->
                            @if(request('search') || request('filter'))
                            <li class="nav-item ms-2">
                                <div class="my-3">
                                    <a href="{{ route('books.index') }}" class="btn btn-secondary btn-lg font-weight-small">
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
                                        <h4 class="card-title mb-0">Gestione Libri</h4>
                                        <button type="button" class="btn btn-primary btn-sm" onclick="openCreateBookModal()">
                                            <i class="mdi mdi-plus"></i> Nuovo Libro
                                        </button>
                                    </div>

                                    <!-- Alert per messaggi -->
                                    <div id="alertContainer"></div>

                                    <div class="table-responsive">
                                        <table class="table table-hover">
                                            <thead>
                                                <tr>
                                                    <th>ID</th>
                                                    <th>Copertina</th>
                                                    <th>Titolo</th>
                                                    <th>Autore</th>
                                                    <th>ISBN</th>
                                                    <th>Categoria</th>
                                                    <th>Anno</th>
                                                    <th>Copie totali</th>
                                                    <th>Disponibili</th>
                                                    <th>Prenotate</th>
                                                    <th>Azioni</th>
                                                </tr>
                                            </thead>
                                            <tbody id="booksTableBody">
                                                @forelse($books as $book)
                                                <tr data-book-id="{{ $book->id }}">
                                                    <td>{{ $book->id }}</td>
                                                    <td>
                                                        <img src="{{ $book->cover_image ? asset('storage/' . $book->cover_image) : asset('storage/covers/default.jpg') }}" 
                                                            alt="{{ $book->title }}" 
                                                            style="height:80px; width:auto; object-fit:cover; border-radius:0!important">
                                                    </td>
                                                    <td><strong>{{ $book->title }}</strong></td>
                                                    <td>{{ $book->author }}</td>
                                                    <td><code>{{ $book->isbn }}</code></td>
                                                    <td>
                                                        <span class="badge badge-info">{{ $book->category->name }}</span>
                                                    </td>
                                                    <td>{{ $book->year ?? '-' }}</td>
                                                    <td>
                                                        <span class="badge badge-secondary">{{ $book->copies_count }}</span>
                                                    </td>
                                                    <td>
                                                        <span class="badge badge-success">{{ $book->available_copies_count }}</span>
                                                    </td>
                                                    <td>
                                                        <span class="badge badge-warning">{{ $book->reserved_copies_count }}</span>
                                                    </td>
                                                    <td>
                                                        <button class="btn btn-sm btn-warning" onclick="editBook({{ $book->id }})" title="Modifica libro">
                                                            <i class="mdi mdi-pencil"></i>
                                                        </button>
                                                        <button class="btn btn-sm btn-info" onclick="manageCopies({{ $book->id }})" title="Gestisci copie">
                                                            <i class="mdi mdi-book-multiple"></i>
                                                        </button>
                                                        <button class="btn btn-sm btn-danger" onclick="deleteBook({{ $book->id }})" title="Elimina libro">
                                                            <i class="mdi mdi-delete"></i>
                                                        </button>
                                                    </td>
                                                </tr>
                                                @empty
                                                <tr>
                                                    <td colspan="11" class="text-center text-muted">Nessun libro presente. Creane uno per iniziare.</td>
                                                </tr>
                                                @endforelse
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Modal Add/edit Libro -->
                    <div class="modal fade" id="bookModal" tabindex="-1" role="dialog" aria-labelledby="bookModalLabel" aria-hidden="true">
                        <div class="modal-dialog modal-lg" role="document">
                            <div class="modal-content">
                                <div class="modal-header">
                                    <h5 class="modal-title" id="bookModalLabel">Nuovo Libro</h5>
                                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                        <span aria-hidden="true">&times;</span>
                                    </button>
                                </div>
                                <form id="bookForm" enctype="multipart/form-data">
                                    @csrf
                                    <input type="hidden" id="bookId" name="book_id">
                                    <input type="hidden" id="bookFormMethod" name="_method" value="POST">
                                    
                                    <div class="modal-body">
                                        <div class="row">
                                            <!-- Colonna sinistra -->
                                            <div class="col-md-8">
                                                <div class="form-group">
                                                    <label for="bookTitle">Titolo <span class="text-danger">*</span></label>
                                                    <input type="text" class="form-control" id="bookTitle" name="title" required maxlength="255">
                                                    <small class="form-text text-danger d-none" id="titleError"></small>
                                                </div>

                                                <div class="row">
                                                    <div class="col-md-6">
                                                        <div class="form-group">
                                                            <label for="bookAuthor">Autore <span class="text-danger">*</span></label>
                                                            <input type="text" class="form-control" id="bookAuthor" name="author" required maxlength="255">
                                                            <small class="form-text text-danger d-none" id="authorError"></small>
                                                        </div>
                                                    </div>
                                                    <div class="col-md-6">
                                                        <div class="form-group">
                                                            <label for="bookIsbn">ISBN <span class="text-danger">*</span></label>
                                                            <input type="text" class="form-control" id="bookIsbn" name="isbn" required maxlength="20">
                                                            <small class="form-text text-danger d-none" id="isbnError"></small>
                                                        </div>
                                                    </div>
                                                </div>

                                                <div class="row">
                                                    <div class="col-md-6">
                                                        <div class="form-group">
                                                            <label for="bookPublisher">Editore</label>
                                                            <input type="text" class="form-control" id="bookPublisher" name="publisher" maxlength="255">
                                                            <small class="form-text text-danger d-none" id="publisherError"></small>
                                                        </div>
                                                    </div>
                                                    <div class="col-md-6">
                                                        <div class="form-group">
                                                            <label for="bookYear">Anno</label>
                                                            <input type="number" class="form-control" id="bookYear" name="year" min="1000" max="{{ date('Y') + 1 }}">
                                                            <small class="form-text text-danger d-none" id="yearError"></small>
                                                        </div>
                                                    </div>
                                                </div>

                                                <div class="form-group">
                                                    <label for="bookCategory">Categoria <span class="text-danger">*</span></label>
                                                    <select class="form-control" id="bookCategory" name="category_id" required>
                                                        <option value="">Seleziona una categoria</option>
                                                        @foreach($categories as $category)
                                                            <option value="{{ $category->id }}">{{ $category->name }}</option>
                                                        @endforeach
                                                    </select>
                                                    <small class="form-text text-danger d-none" id="categoryIdError"></small>
                                                </div>

                                                <div class="form-group">
                                                    <label for="bookDescription">Descrizione</label>
                                                    <textarea class="form-control" id="bookDescription" name="description" rows="3" maxlength="2000"></textarea>
                                                    <small class="form-text text-muted">Massimo 2000 caratteri</small>
                                                    <small class="form-text text-danger d-none" id="descriptionError"></small>
                                                </div>
                                            </div>

                                            <!-- Colonna destra - Upload copertina -->
                                            <div class="col-md-4">
                                                <div class="form-group">
                                                    <label>Copertina</label>
                                                    <div class="text-center">
                                                        <div id="coverPreview" class="mb-2" style="min-height: 200px; border: 2px dashed #ddd; border-radius: 8px; padding: 10px;">
                                                            <img id="coverImage" src="{{ asset('images/default-book-cover.png') }}" alt="Copertina" style="max-width: 100%; max-height: 200px; object-fit: cover;">
                                                        </div>
                                                        <input type="file" class="d-none" id="bookCoverImage" name="cover_image" accept="image/*" onchange="previewCover(event)">
                                                        <button type="button" class="btn btn-sm btn-outline-primary" onclick="$('#bookCoverImage').click()">
                                                            <i class="mdi mdi-upload"></i> Carica immagine
                                                        </button>
                                                        <button type="button" class="btn btn-sm btn-outline-danger" id="removeCoverBtn" style="display:none;" onclick="removeCover()">
                                                            <i class="mdi mdi-delete"></i> Rimuovi
                                                        </button>
                                                    </div>
                                                    <small class="form-text text-muted">Formati: JPG, PNG, GIF. Max 2MB</small>
                                                    <small class="form-text text-danger d-none" id="coverImageError"></small>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    
                                    <div class="modal-footer">
                                        <button type="button" class="btn btn-secondary" data-dismiss="modal">Annulla</button>
                                        <button type="submit" class="btn btn-primary" id="bookSubmitBtn">Salva</button>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>

                    <!-- Modal manage Copie -->
                    <div class="modal fade" id="copiesModal" tabindex="-1" role="dialog" aria-labelledby="copiesModalLabel" aria-hidden="true">
                        <div class="modal-dialog modal-xl" role="document">
                            <div class="modal-content">
                                <div class="modal-header">
                                    <h5 class="modal-title" id="copiesModalLabel">Gestione Copie</h5>
                                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                        <span aria-hidden="true">&times;</span>
                                    </button>
                                </div>
                                <div class="modal-body">
                                    <div class="d-flex justify-content-between align-items-center mb-3">
                                        <div>
                                            <h6 id="copiesBookTitle" class="mb-0"></h6>
                                            <small class="text-muted" id="copiesBookInfo"></small>
                                        </div>
                                        <button type="button" class="btn btn-primary btn-sm" onclick="openCreateCopyModal()">
                                            <i class="mdi mdi-plus"></i> Nuova Copia
                                        </button>
                                    </div>

                                    <div class="table-responsive">
                                        <table class="table table-sm">
                                            <thead>
                                                <tr>
                                                    <th>Barcode</th>
                                                    <th>Stato Fisico</th>
                                                    <th>Disponibilità</th>
                                                    <th>Note</th>
                                                    <th>Creata il</th>
                                                    <th>Azioni</th>
                                                </tr>
                                            </thead>
                                            <tbody id="copiesTableBody">
                                                <tr>
                                                    <td colspan="6" class="text-center text-muted">Caricamento...</td>
                                                </tr>
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Modal add/Edit Copia -->
                    <div class="modal fade" id="copyModal" tabindex="-1" role="dialog" aria-labelledby="copyModalLabel" aria-hidden="true">
                        <div class="modal-dialog" role="document">
                            <div class="modal-content">
                                <div class="modal-header">
                                    <h5 class="modal-title" id="copyModalLabel">Nuova Copia</h5>
                                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                        <span aria-hidden="true">&times;</span>
                                    </button>
                                </div>
                                <form id="copyForm">
                                    @csrf
                                    <input type="hidden" id="copyId" name="copy_id">
                                    <input type="hidden" id="copyFormMethod" name="_method" value="POST">
                                    
                                    <div class="modal-body">
                                        <div class="form-group">
                                            <label for="copyCondition">Stato Fisico <span class="text-danger">*</span></label>
                                            <select class="form-control" id="copyCondition" name="condition" required>
                                                <option value="very good">Ottimo</option>
                                                <option value="good" selected>Buono</option>
                                                <option value="bad">Discreto</option>
                                            </select>
                                            <small class="form-text text-danger d-none" id="conditionError"></small>
                                        </div>

                                        <div class="form-group">
                                            <label for="copyStatus">Disponibilità <span class="text-danger">*</span></label>
                                            <select class="form-control" id="copyStatus" name="status" required>
                                                <option value="available" selected>Disponibile</option>
                                                <option value="reserved">Prenotato</option>
                                                <option value="loaned">In prestito</option>
                                                <option value="maintenance">Manutenzione</option>
                                            </select>
                                            <small class="form-text text-danger d-none" id="statusError"></small>
                                        </div>

                                        <div class="form-group">
                                            <label for="copyNotes">Note Interne</label>
                                            <textarea class="form-control" id="copyNotes" name="notes" rows="3" maxlength="500"></textarea>
                                            <small class="form-text text-muted">Massimo 500 caratteri</small>
                                            <small class="form-text text-danger d-none" id="notesError"></small>
                                        </div>

                                        <div class="alert alert-info" id="barcodeInfo" style="display:none;">
                                            <strong>Barcode:</strong> <span id="barcodeDisplay"></span>
                                        </div>
                                    </div>
                                    
                                    <div class="modal-footer">
                                        <button type="button" class="btn btn-secondary" data-dismiss="modal">Annulla</button>
                                        <button type="submit" class="btn btn-primary" id="copySubmitBtn">Salva</button>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>

                    <!-- Modal confirm delete Libro -->
                    <div class="modal fade" id="deleteBookModal" tabindex="-1" role="dialog">
                        <div class="modal-dialog" role="document">
                            <div class="modal-content">
                                <div class="modal-header bg-danger text-white">
                                    <h5 class="modal-title">Conferma Eliminazione</h5>
                                    <button type="button" class="close text-white" data-dismiss="modal">
                                        <span>&times;</span>
                                    </button>
                                </div>
                                <div class="modal-body">
                                    <p>Sei sicuro di voler eliminare questo libro?</p>
                                    <p class="text-muted">Verranno eliminate anche tutte le copie associate. Questa azione non può essere annullata.</p>
                                </div>
                                <div class="modal-footer">
                                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Annulla</button>
                                    <button type="button" class="btn btn-danger" id="confirmDeleteBookBtn">Elimina</button>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Modal confirm delete Copia -->
                    <div class="modal fade" id="deleteCopyModal" tabindex="-1" role="dialog">
                        <div class="modal-dialog" role="document">
                            <div class="modal-content">
                                <div class="modal-header bg-danger text-white">
                                    <h5 class="modal-title">Conferma Eliminazione</h5>
                                    <button type="button" class="close text-white" data-dismiss="modal">
                                        <span>&times;</span>
                                    </button>
                                </div>
                                <div class="modal-body">
                                    <p>Sei sicuro di voler eliminare questa copia?</p>
                                    <p class="text-muted">Questa azione non può essere annullata.</p>
                                </div>
                                <div class="modal-footer">
                                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Annulla</button>
                                    <button type="button" class="btn btn-danger" id="confirmDeleteCopyBtn">Elimina</button>
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

    <!-- Active sidebar-->
    <script>
        document.addEventListener("DOMContentLoaded", function() {
            const books = document.getElementById('books');
            if (books) {
                books.classList.add('sidebar-active');
            }
        });
    </script>
    <!-- END Active sidebar-->
    <!-- CRUD Books and Copies-->
    <script>
        let currentBookId = null;
        let currentCopyId = null;

        // URL helper Laravel
        const storeBookUrl = "{{ route('books.store') }}";
        const editBookUrlBase = "{{ url('books') }}";
        const updateBookUrlBase = "{{ url('books') }}";
        const deleteBookUrlBase = "{{ url('books') }}";
        const getCopiesUrlBase = "{{ url('books') }}";
        const storeCopyUrlBase = "{{ url('books') }}";
        const updateCopyUrlBase = "{{ url('books') }}";
        const deleteCopyUrlBase = "{{ url('books') }}";

        // BOOK

        // Apri modal per nuovo libro
        function openCreateBookModal() {
            $('#bookModalLabel').text('Nuovo Libro');
            $('#bookForm')[0].reset();
            $('#bookId').val('');
            $('#bookFormMethod').val('POST');
            $('#coverImage').attr('src', '{{ asset("images/default-book-cover.png") }}');
            $('#removeCoverBtn').hide();
            $('.text-danger').addClass('d-none').text('');
            $('.form-control').removeClass('is-invalid');
            $('#bookModal').modal('show');
        }

        // Modifica libro
        function editBook(id) {
            $.ajax({
                url: `${editBookUrlBase}/${id}/edit`,
                type: 'GET',
                success: function(response) {
                    if (response.success) {
                        const book = response.book;
                        $('#bookModalLabel').text('Modifica Libro');
                        $('#bookId').val(book.id);
                        $('#bookTitle').val(book.title);
                        $('#bookAuthor').val(book.author);
                        $('#bookIsbn').val(book.isbn);
                        $('#bookPublisher').val(book.publisher);
                        $('#bookYear').val(book.year);
                        $('#bookCategory').val(book.category_id);
                        $('#bookDescription').val(book.description);
                        
                        if (book.cover_image) {
                            $('#coverImage').attr('src', `/storage/${book.cover_image}`);
                            $('#removeCoverBtn').show();
                        } else {
                            $('#coverImage').attr('src', '{{ asset("images/default-book-cover.png") }}');
                            $('#removeCoverBtn').hide();
                        }
                        
                        $('#bookFormMethod').val('PUT');
                        $('.text-danger').addClass('d-none').text('');
                        $('.form-control').removeClass('is-invalid');
                        $('#bookModal').modal('show');
                    }
                },
                error: function() {
                    showAlert('Errore durante il caricamento del libro.', 'danger');
                }
            });
        }

        // Elimina libro
        function deleteBook(id) {
            currentBookId = id;
            $('#deleteBookModal').modal('show');
        }

        // Conferma eliminazione libro
        $('#confirmDeleteBookBtn').click(function() {
            const id = currentBookId;
            
            $.ajax({
                url: `${deleteBookUrlBase}/${id}`,
                type: 'DELETE',
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                success: function(response) {
                    $('#deleteBookModal').modal('hide');
                    if (response.success) {
                        $(`tr[data-book-id="${id}"]`).fadeOut(300, function() {
                            $(this).remove();
                            checkEmptyTable();
                        });
                        showAlert(response.message, 'success');
                    }
                },
                error: function(xhr) {
                    $('#deleteBookModal').modal('hide');
                    showAlert(xhr.responseJSON?.message || 'Errore durante l\'eliminazione.', 'danger');
                }
            });
        });

        // Submit form libro
        $('#bookForm').submit(function(e) {
            e.preventDefault();
            
            const id = $('#bookId').val();
            const method = $('#bookFormMethod').val();
            const url = id ? `${updateBookUrlBase}/${id}` : storeBookUrl;
            
            const formData = new FormData(this);
            
            // Reset errori
            $('.text-danger').addClass('d-none').text('');
            $('.form-control').removeClass('is-invalid');
            $('#bookSubmitBtn').prop('disabled', true).text('Salvataggio...');
            
            $.ajax({
                url: url,
                type: 'POST',
                data: formData,
                processData: false,
                contentType: false,
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                success: function(response) {
                    if (response.success) {
                        $('#bookModal').modal('hide');
                        showAlert(response.message, 'success');
                        setTimeout(() => location.reload(), 1000);
                    }
                },
                error: function(xhr) {
                    const errors = xhr.responseJSON?.errors;
                    
                    if (errors) {
                        Object.keys(errors).forEach(key => {
                            const fieldId = key.replace('_', '') + 'Error';
                            $(`#${fieldId}`).removeClass('d-none').text(errors[key][0]);
                            $(`[name="${key}"]`).addClass('is-invalid');
                        });
                    } else {
                        showAlert('Errore durante il salvataggio.', 'danger');
                    }
                },
                complete: function() {
                    $('#bookSubmitBtn').prop('disabled', false).text('Salva');
                }
            });
        });

        // Preview copertina
        function previewCover(event) {
            const file = event.target.files[0];
            if (file) {
                const reader = new FileReader();
                reader.onload = function(e) {
                    $('#coverImage').attr('src', e.target.result);
                    $('#removeCoverBtn').show();
                }
                reader.readAsDataURL(file);
            }
        }

        // Rimuovi copertina
        function removeCover() {
            $('#bookCoverImage').val('');
            $('#coverImage').attr('src', '{{ asset("images/default-book-cover.png") }}');
            $('#removeCoverBtn').hide();
        }

        // BOOKCopies

        // Gestisci copie
        function manageCopies(bookId) {
            currentBookId = bookId;
            
            $.ajax({
                url: `${getCopiesUrlBase}/${bookId}/copies`,
                type: 'GET',
                success: function(response) {
                    if (response.success) {
                        const book = response.book;
                        $('#copiesBookTitle').text(book.title);
                        $('#copiesBookInfo').text(`${book.author} - ISBN: ${book.isbn}`);
                        
                        loadCopiesTable(response.copies);
                        $('#copiesModal').modal('show');
                    }
                },
                error: function() {
                    showAlert('Errore durante il caricamento delle copie.', 'danger');
                }
            });
        }

        // Carica tabella copie
        function loadCopiesTable(copies) {
            const tbody = $('#copiesTableBody');
            tbody.empty();
            
            if (copies.length === 0) {
                tbody.append('<tr><td colspan="6" class="text-center text-muted">Nessuna copia presente.</td></tr>');
                return;
            }
            
            copies.forEach(copy => {
                const statusBadge = getStatusBadge(copy.status);
                const conditionLabel = getConditionLabel(copy.condition);
                const date = new Date(copy.created_at).toLocaleDateString('it-IT');
                
                const row = `
                    <tr data-copy-id="${copy.id}">
                        <td><code>${copy.barcode}</code></td>
                        <td>${conditionLabel}</td>
                        <td>${statusBadge}</td>
                        <td>${copy.notes || '-'}</td>
                        <td>${date}</td>
                        <td>
                            <button class="btn btn-sm btn-warning" onclick="editCopy(${copy.id})" title="Modifica">
                                <i class="mdi mdi-pencil"></i>
                            </button>
                            <button class="btn btn-sm btn-danger" onclick="deleteCopy(${copy.id})" title="Elimina">
                                <i class="mdi mdi-delete"></i>
                            </button>
                        </td>
                    </tr>
                `;
                tbody.append(row);
            });
        }

        // Apri modal per nuova copia
        function openCreateCopyModal() {
            $('#copyModalLabel').text('Nuova Copia');
            $('#copyForm')[0].reset();
            $('#copyId').val('');
            $('#copyFormMethod').val('POST');
            $('#barcodeInfo').hide();
            $('.text-danger').addClass('d-none').text('');
            $('.form-control').removeClass('is-invalid');
            $('#copyModal').modal('show');
        }

        // Modifica copia
        function editCopy(copyId) {
            // Trova la copia nell'array attuale
            $.ajax({
                url: `${getCopiesUrlBase}/${currentBookId}/copies`,
                type: 'GET',
                success: function(response) {
                    const copy = response.copies.find(c => c.id === copyId);
                    if (copy) {
                        $('#copyModalLabel').text('Modifica Copia');
                        $('#copyId').val(copy.id);
                        $('#copyCondition').val(copy.condition);
                        $('#copyStatus').val(copy.status);
                        $('#copyNotes').val(copy.notes);
                        $('#barcodeDisplay').text(copy.barcode);
                        $('#barcodeInfo').show();
                        $('#copyFormMethod').val('PUT');
                        $('.text-danger').addClass('d-none').text('');
                        $('.form-control').removeClass('is-invalid');
                        $('#copyModal').modal('show');
                    }
                }
            });
        }

        // Elimina copia
        function deleteCopy(copyId) {
            currentCopyId = copyId;
            $('#deleteCopyModal').modal('show');
        }

        // Conferma eliminazione copia
        $('#confirmDeleteCopyBtn').click(function() {
            const copyId = currentCopyId;
            
            $.ajax({
                url: `${deleteCopyUrlBase}/${currentBookId}/copies/${copyId}`,
                type: 'DELETE',
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                success: function(response) {
                    $('#deleteCopyModal').modal('hide');
                    if (response.success) {
                        $(`#copiesTableBody tr[data-copy-id="${copyId}"]`).fadeOut(300, function() {
                            $(this).remove();
                            if ($('#copiesTableBody tr').length === 0) {
                                $('#copiesTableBody').append('<tr><td colspan="6" class="text-center text-muted">Nessuna copia presente.</td></tr>');
                            }
                        });
                        showAlert(response.message, 'success');
                    }
                },
                error: function(xhr) {
                    $('#deleteCopyModal').modal('hide');
                    showAlert(xhr.responseJSON?.message || 'Errore durante l\'eliminazione.', 'danger');
                }
            });
        });

        // Submit form copia
        $('#copyForm').submit(function(e) {
            e.preventDefault();
            
            const copyId = $('#copyId').val();
            const method = $('#copyFormMethod').val();
            const url = copyId 
                ? `${updateCopyUrlBase}/${currentBookId}/copies/${copyId}`
                : `${storeCopyUrlBase}/${currentBookId}/copies`;
            
            // Reset errori
            $('.text-danger').addClass('d-none').text('');
            $('.form-control').removeClass('is-invalid');
            $('#copySubmitBtn').prop('disabled', true).text('Salvataggio...');
            
            $.ajax({
                url: url,
                type: method,
                data: $(this).serialize(),
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                success: function(response) {
                    if (response.success) {
                        $('#copyModal').modal('hide');
                        showAlert(response.message, 'success');
                        // Ricarica le copie
                        manageCopies(currentBookId);
                    }
                },
                error: function(xhr) {
                    const errors = xhr.responseJSON?.errors;
                    
                    if (errors) {
                        Object.keys(errors).forEach(key => {
                            $(`#${key}Error`).removeClass('d-none').text(errors[key][0]);
                            $(`[name="${key}"]`).addClass('is-invalid');
                        });
                    } else {
                        showAlert('Errore durante il salvataggio.', 'danger');
                    }
                },
                complete: function() {
                    $('#copySubmitBtn').prop('disabled', false).text('Salva');
                }
            });
        });

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

        // Controlla tabella vuota
        function checkEmptyTable() {
            if ($('#booksTableBody tr').length === 0) {
                $('#booksTableBody').html('<tr><td colspan="11" class="text-center text-muted">Nessun libro presente. Creane uno per iniziare.</td></tr>');
            }
        }

        // Get status badge
        function getStatusBadge(status) {
            const badges = {
                'available': '<span class="badge badge-success">Disponibile</span>',
                'reserved': '<span class="badge badge-warning">Prenotato</span>',
                'loaned': '<span class="badge badge-info">In prestito</span>',
                'maintenance': '<span class="badge badge-danger">Manutenzione</span>'
            };
            return badges[status] || status;
        }

        // Get condition label
        function getConditionLabel(condition) {
            const labels = {
                'very good': 'Ottimo',
                'good': 'Buono',
                'bad': 'Discreto'
            };
            return labels[condition] || condition;
        }
    </script>
    <!-- end CRUD Books and Copies-->
    <!-- End custom js for this page-->
</body>

</html>
