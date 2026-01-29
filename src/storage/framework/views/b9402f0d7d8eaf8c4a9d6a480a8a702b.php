<!DOCTYPE html>
<html lang="en">

<?php echo $__env->make('dashboard.partials._head', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
<link rel="stylesheet" href="<?php echo e(asset('css/dashboard/categories.css')); ?>">
<body>
    <div class="container-scroller d-flex">
        <!-- partial:./partials/_sidebar.html -->
        <?php echo $__env->make('dashboard.partials._sidebar', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>

        <!-- partial -->
        <div class="container-fluid page-body-wrapper">
            <!-- partial:./partials/_navbar.html -->
            <nav style="background: url('<?php echo e(asset('assets/dashboard/images/other/nav-cover.png')); ?>') center center no-repeat !important; background-size: cover;" class="navbar col-lg-12 col-12 px-0 py-0 py-lg-4 d-flex flex-row">
                <div class="navbar-menu-wrapper d-flex align-items-center justify-content-end">
                <button class="navbar-toggler navbar-toggler align-self-center" type="button" data-toggle="minimize">
                    <span class="mdi mdi-menu"></span>
                </button>
                <h4 class="font-weight-bold mb-0 d-none d-md-block mt-1"><?php echo e(auth()->user()->name ?? null); ?></h4>
                <ul class="navbar-nav navbar-nav-right">
                    <li class="nav-item">
                    <h4 class="mb-0 font-weight-bold d-none d-xl-block"><?php echo e(now()->format('d/m/Y H:i')); ?></h4>
                    </li>

                </ul>
                <button class="navbar-toggler navbar-toggler-right d-lg-none align-self-center" type="button" data-toggle="offcanvas">
                    <span class="mdi mdi-menu"></span>
                </button>
                </div>
                <div class="navbar-menu-wrapper navbar-search-wrapper d-none d-lg-flex align-items-center">
                    <form action="<?php echo e(route('categories.index')); ?>" method="GET" class="d-flex align-items-center w-100">
                        <ul class="navbar-nav mr-lg-2" style="width:50%!important">
                            <li style="width:50%!important" class="nav-item nav-search d-none d-lg-block">
                                <div class="input-group">
                                    <input type="text"
                                        name="search"
                                        class="form-control"
                                        placeholder="Cerca categoria..."
                                        aria-label="search"
                                        value="<?php echo e(request('search')); ?>">
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
                            <?php if(request('search')): ?>
                            <li class="nav-item ms-2">
                                <div class="my-3">
                                    <a href="<?php echo e(route('categories.index')); ?>" class="btn btn-secondary btn-lg font-weight-small">
                                        Reset
                                    </a>
                                </div>
                            </li>
                            <?php endif; ?>
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
                                        <h4 class="card-title mb-0">Gestione Categorie</h4>
                                        <button type="button" class="btn btn-primary btn-sm" data-toggle="modal" data-target="#categoryModal" onclick="openCreateModal()">
                                            <i class="mdi mdi-plus"></i> Nuova Categoria
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
                                                    <th>Descrizione</th>
                                                    <th>Libri associati</th>
                                                    <th>Creata il</th>
                                                    <th>Azioni</th>
                                                </tr>
                                            </thead>
                                            <tbody id="categoriesTableBody">
                                                <?php $__empty_1 = true; $__currentLoopData = $categories; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $category): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                                                <tr data-category-id="<?php echo e($category->id); ?>">
                                                    <td><?php echo e($category->id); ?></td>
                                                    <td><strong><?php echo e($category->name); ?></strong></td>
                                                    <td><?php echo e(Str::limit($category->description, 60) ?? '-'); ?></td>
                                                    <td>
                                                        <span class="badge badge-info"><?php echo e($category->books_count); ?></span>
                                                    </td>
                                                    <td><?php echo e($category->created_at->format('d/m/Y')); ?></td>
                                                    <td>
                                                        <button class="btn btn-sm btn-warning" onclick="editCategory(<?php echo e($category->id); ?>)" title="Modifica">
                                                            <i class="mdi mdi-pencil"></i>
                                                        </button>
                                                        <button class="btn btn-sm btn-danger" onclick="deleteCategory(<?php echo e($category->id); ?>)" title="Elimina">
                                                            <i class="mdi mdi-delete"></i>
                                                        </button>
                                                    </td>
                                                </tr>
                                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                                                <tr>
                                                    <td colspan="6" class="text-center text-muted">Nessuna categoria presente. Creane una per iniziare.</td>
                                                </tr>
                                                <?php endif; ?>
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Modal ADD/EDIT Category -->
                    <div class="modal fade" id="categoryModal" tabindex="-1" role="dialog" aria-labelledby="categoryModalLabel" aria-hidden="true">
                        <div class="modal-dialog" role="document">
                            <div class="modal-content">
                                <div class="modal-header">
                                    <h5 class="modal-title" id="categoryModalLabel">Nuova Categoria</h5>
                                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                        <span aria-hidden="true">&times;</span>
                                    </button>
                                </div>
                                <form id="categoryForm">
                                    <?php echo csrf_field(); ?>
                                    <input type="hidden" id="categoryId" name="category_id">
                                    <input type="hidden" id="formMethod" name="_method" value="POST">
                                    
                                    <div class="modal-body">
                                        <div class="form-group">
                                            <label for="categoryName">Nome Categoria <span class="text-danger">*</span></label>
                                            <input type="text" class="form-control" id="categoryName" name="name" required maxlength="255">
                                            <small class="form-text text-danger d-none" id="nameError"></small>
                                        </div>
                                        
                                        <div class="form-group">
                                            <label for="categoryDescription">Descrizione</label>
                                            <textarea class="form-control" id="categoryDescription" name="description" rows="4" maxlength="1000"></textarea>
                                            <small class="form-text text-muted">Massimo 1000 caratteri</small>
                                            <small class="form-text text-danger d-none" id="descriptionError"></small>
                                        </div>
                                    </div>
                                    
                                    <div class="modal-footer">
                                        <button type="button" class="btn btn-secondary" data-dismiss="modal">Annulla</button>
                                        <button type="submit" class="btn btn-primary" id="submitBtn">Salva</button>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>

                    <!-- Modal Confirm DELETE -->
                    <div class="modal fade" id="deleteModal" tabindex="-1" role="dialog" aria-labelledby="deleteModalLabel" aria-hidden="true">
                        <div class="modal-dialog" role="document">
                            <div class="modal-content">
                                <div class="modal-header bg-danger text-white">
                                    <h5 class="modal-title" id="deleteModalLabel">Conferma Eliminazione</h5>
                                    <button type="button" class="close text-white" data-dismiss="modal" aria-label="Close">
                                        <span aria-hidden="true">&times;</span>
                                    </button>
                                </div>
                                <div class="modal-body">
                                    <p>Sei sicuro di voler eliminare questa categoria?</p>
                                    <p class="text-muted">Questa azione non può essere annullata.</p>
                                </div>
                                <div class="modal-footer">
                                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Annulla</button>
                                    <button type="button" class="btn btn-danger" id="confirmDeleteBtn">Elimina</button>
                                </div>
                            </div>
                        </div>
                    </div>
                    <?php echo $__env->make('dashboard.partials._footer', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>

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
    <script src="<?php echo e(asset('js/dashboard/off-canvas.js')); ?>"></script>
    <script src="<?php echo e(asset('js/dashboard/hoverable-collapse.js')); ?>"></script>
    <script src="<?php echo e(asset('js/dashboard/template.js')); ?>"></script>
    <!-- endinject -->
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@4.6.0/dist/js/bootstrap.bundle.min.js"></script>
    <!-- endinject -->
    <!-- Active sidebar-->
    <script>
        document.addEventListener("DOMContentLoaded", function() {
            const categories = document.getElementById('categories');
            if (categories) {
                categories.classList.add('sidebar-active');
            }
        });
    </script>
    <!-- END Active sidebar-->

    <!-- Script for CRUD-->
    <script>
        let currentCategoryId = null;

        // URL helper Laravel
        const storeUrl = "<?php echo e(route('categories.store')); ?>"; // POST
        const editUrlBase = "<?php echo e(url('categories')); ?>";      // GET /{id}/edit
        const updateUrlBase = "<?php echo e(url('categories')); ?>";    // PUT /{id}
        const deleteUrlBase = "<?php echo e(url('categories')); ?>";    // DELETE /{id}

        // Apri modal per nuova categoria
        function openCreateModal() {
            $('#categoryModalLabel').text('Nuova Categoria');
            $('#categoryForm')[0].reset();
            $('#categoryId').val('');
            $('#formMethod').val('POST');
            $('.text-danger').addClass('d-none').text('');
            $('.form-control').removeClass('is-invalid');
        }

        // Modifica categoria
        function editCategory(id) {
            $.ajax({
                url: `${editUrlBase}/${id}/edit`,
                type: 'GET',
                success: function(response) {
                    if (response.success) {
                        $('#categoryModalLabel').text('Modifica Categoria');
                        $('#categoryId').val(response.category.id);
                        $('#categoryName').val(response.category.name);
                        $('#categoryDescription').val(response.category.description);
                        $('#formMethod').val('PUT');
                        $('.text-danger').addClass('d-none').text('');
                        $('.form-control').removeClass('is-invalid');
                        $('#categoryModal').modal('show');
                    }
                },
                error: function() {
                    showAlert('Errore durante il caricamento della categoria.', 'danger');
                }
            });
        }

        // Elimina categoria
        function deleteCategory(id) {
            currentCategoryId = id;
            $('#deleteModal').modal('show');
        }

        // Conferma eliminazione
        $('#confirmDeleteBtn').click(function() {
            const id = currentCategoryId;
            
            $.ajax({
                url: `${deleteUrlBase}/${id}`,
                type: 'DELETE',
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                success: function(response) {
                    $('#deleteModal').modal('hide');
                    if (response.success) {
                        $(`tr[data-category-id="${id}"]`).fadeOut(300, function() {
                            $(this).remove();
                            checkEmptyTable();
                        });
                        showAlert(response.message, 'success');
                    } else {
                        showAlert(response.message, 'danger');
                    }
                },
                error: function(xhr) {
                    $('#deleteModal').modal('hide');
                    const message = xhr.responseJSON?.message || 'Errore durante l\'eliminazione.';
                    showAlert(message, 'danger');
                }
            });
        });

        // Submit form (creazione/modifica)
        $('#categoryForm').submit(function(e) {
            e.preventDefault();
            
            const id = $('#categoryId').val();
            const method = $('#formMethod').val();
            const url = id ? `${updateUrlBase}/${id}` : storeUrl;
            
            // Reset errori
            $('.text-danger').addClass('d-none').text('');
            $('.form-control').removeClass('is-invalid');
            $('#submitBtn').prop('disabled', true).text('Salvataggio...');

            // Preparazione dati
            let formData = $(this).serialize();
            if (method === 'PUT') {
                formData += '&_method=PUT'; // Laravel richiede _method per PUT via POST
            }
            
            $.ajax({
                url: url,
                type: 'POST', // sempre POST, Laravel legge _method
                data: formData,
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                success: function(response) {
                    if (response.success) {
                        $('#categoryModal').modal('hide');
                        showAlert(response.message, 'success');
                        setTimeout(() => location.reload(), 1000);
                    }
                },
                error: function(xhr) {
                    const errors = xhr.responseJSON?.errors;
                    
                    if (errors) {
                        if (errors.name) {
                            $('#categoryName').addClass('is-invalid');
                            $('#nameError').removeClass('d-none').text(errors.name[0]);
                        }
                        if (errors.description) {
                            $('#categoryDescription').addClass('is-invalid');
                            $('#descriptionError').removeClass('d-none').text(errors.description[0]);
                        }
                    } else {
                        const message = xhr.responseJSON?.message || 'Errore durante il salvataggio.';
                        showAlert(message, 'danger');
                    }
                },
                complete: function() {
                    $('#submitBtn').prop('disabled', false).text('Salva');
                }
            });
        });

        // Mostra alert
        function showAlert(message, type) {
            const alertHtml = `
                <div class="alert alert-${type} alert-dismissible fade show" role="alert">
                    ${message}
                    <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
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

        // Controlla se la tabella è vuota
        function checkEmptyTable() {
            const rowCount = $('#categoriesTableBody tr').length;
            if (rowCount === 0) {
                const emptyRow = `
                    <tr>
                        <td colspan="6" class="text-center text-muted">Nessuna categoria presente. Creane una per iniziare.</td>
                    </tr>
                `;
                $('#categoriesTableBody').html(emptyRow);
            }
        }
    </script>

    <!-- End script for CRUD-->
    <!-- End custom js for this page-->
</body>

</html>
<?php /**PATH /var/www/resources/views/dashboard/pages/categories.blade.php ENDPATH**/ ?>