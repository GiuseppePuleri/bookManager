    <header id="header">
        <nav id="primary-header" class="navbar navbar-expand-lg py-4">
        <div class="container-fluid padding-side">
            <div class="d-flex justify-content-between align-items-center w-100">
            <button class="navbar-toggler border-0 d-flex d-lg-none order-3 p-2 shadow-none" type="button"
                data-bs-toggle="offcanvas" data-bs-target="#bdNavbar" aria-controls="bdNavbar" aria-expanded="false">
                <svg class="navbar-icon" width="60" height="60">
                <use xlink:href="#navbar-icon"></use>
                </svg>
            </button>
            <div class="header-bottom offcanvas offcanvas-end " id="bdNavbar" aria-labelledby="bdNavbarOffcanvasLabel">
                <div class="offcanvas-header px-4 pb-0">
                <button type="button" class="btn-close btn-close-black mt-2" data-bs-dismiss="offcanvas"
                    aria-label="Close" data-bs-target="#bdNavbar"></button>
                </div>
                <div class="offcanvas-body align-items-center justify-content-center">
                <ul class="navbar-nav align-items-center mb-2 mb-lg-0">
                    <li class="nav-item px-3">
                    <a class="nav-link active-index p-0" aria-current="page" href="<?php echo e(route('showcase.index')); ?>">Home</a>
                    </li>
                    <li class="nav-item px-3">
                    <a class="nav-link p-0 active-reservations" href="<?php echo e(route('showcase.my-reservations')); ?>">My Reservations</a>
                    </li>
                </ul>
                </div>
            </div>
            
            <a class="navbar-brand" flag="isAdmin" href="<?php echo e(route('login', ['isAdmin' => 1])); ?>">
                <button class="btn btn-arrow btn-primary" style="--bs-btn-padding-y:0!important">
                <span>Admin Dashboard<svg xmlns="http://www.w3.org/2000/svg" width="10" height="10" fill="currentColor" class="bi bi-exclamation-triangle" viewBox="0 0 16 16">
                    <path d="M7.938 2.016A.13.13 0 0 1 8.002 2a.13.13 0 0 1 .063.016.15.15 0 0 1 .054.057l6.857 11.667c.036.06.035.124.002.183a.2.2 0 0 1-.054.06.1.1 0 0 1-.066.017H1.146a.1.1 0 0 1-.066-.017.2.2 0 0 1-.054-.06.18.18 0 0 1 .002-.183L7.884 2.073a.15.15 0 0 1 .054-.057m1.044-.45a1.13 1.13 0 0 0-1.96 0L.165 13.233c-.457.778.091 1.767.98 1.767h13.713c.889 0 1.438-.99.98-1.767z"/>
                    <path d="M7.002 12a1 1 0 1 1 2 0 1 1 0 0 1-2 0M7.1 5.995a.905.905 0 1 1 1.8 0l-.35 3.507a.552.552 0 0 1-1.1 0z"/>
                </svg></span>
                </button>
            </a>
            </div>
        </div>
        </nav>
    </header><?php /**PATH /var/www/resources/views/showcase/partials/_header.blade.php ENDPATH**/ ?>