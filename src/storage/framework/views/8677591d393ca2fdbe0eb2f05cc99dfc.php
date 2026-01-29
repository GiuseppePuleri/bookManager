<nav class="sidebar sidebar-offcanvas" id="sidebar">
  <ul class="nav">

    <!-- Dashboard -->
    <li class="nav-item" id="dashboard">
      <a class="nav-link" href="<?php echo e(route('dashboard.index')); ?>">
        <i class="mdi mdi-view-quilt menu-icon"></i>
        <span class="menu-title">Dashboard</span>
      </a>
    </li>

    <!-- Catalogo -->
    <li class="nav-item sidebar-category">
      <p>Catalogo</p>
      <span></span>
    </li>

    <li class="nav-item" id="books">
      <a class="nav-link" href="<?php echo e(route('books.index')); ?>">
        <i class="mdi mdi-book-open-page-variant menu-icon"></i>
        <span class="menu-title">Libri</span>
      </a>
    </li>

    <li class="nav-item" id="categories">
      <a class="nav-link" href="<?php echo e(route('categories.index')); ?>">
        <i class="mdi mdi-tag-multiple menu-icon"></i>
        <span class="menu-title">Categorie</span>
      </a>
    </li>

    <!-- Prenotazioni -->
    <li class="nav-item sidebar-category">
      <p>Gestione</p>
      <span></span>
    </li>

    <li class="nav-item" id="reservations">
      <a class="nav-link" href="<?php echo e(route('reservations.index')); ?>">
        <i class="mdi mdi-calendar-check menu-icon"></i>
        <span class="menu-title">Prenotazioni</span>
      </a>
    </li>

    <!-- Utenti -->
    <li class="nav-item" id="users">
      <a class="nav-link" href="<?php echo e(route('users.index')); ?>">
        <i class="mdi mdi-account-group menu-icon"></i>
        <span class="menu-title">Utenti</span>
      </a>
    </li>

    <!-- Exit -->
    <li class="nav-item sidebar-category">
      <p>Esci</p>
      <span></span>
    </li>

    <li class="nav-item" id="showcase">
      <a class="nav-link" href="<?php echo e(route('showcase.index')); ?>">
        <i class="mdi mdi-bookshelf menu-icon"></i>
        <span class="menu-title">Showcase</span>
      </a>
    </li>

    <li class="nav-item" id="logout">
      <a class="nav-link" href="#"
        onclick="event.preventDefault(); document.getElementById('logout-form').submit();">
        <i class="mdi mdi-exit-run menu-icon"></i>
        <span class="menu-title" style="color:#ffb4b4">Logout</span>
      </a>

      <form id="logout-form" action="<?php echo e(route('logout')); ?>" method="POST" class="d-none">
        <?php echo csrf_field(); ?>
      </form>
    </li>

  </ul>
</nav>
<?php /**PATH /var/www/resources/views/dashboard/partials/_sidebar.blade.php ENDPATH**/ ?>