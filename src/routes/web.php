<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\Dashboard\DashboardController;
use App\Http\Controllers\Showcase\ShowcaseController;
use App\Http\Controllers\Dashboard\BookController;
use App\Http\Controllers\Dashboard\CategoryController;
use App\Http\Controllers\Dashboard\ReservationController;
use App\Http\Controllers\Dashboard\UserController;

/*****************************
 * AUTH
 * ***************************/
Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
Route::post('/login', [AuthController::class, 'login'])->name('login.submit')->middleware('throttle:5,1');
Route::post('/register', [AuthController::class, 'register'])->name('register')->middleware('throttle:3,1');
Route::post('/logout', [AuthController::class, 'logout'])->name('logout');


/*****************************
 * DASHBOARD FOR ADMIN
 * ***************************/
Route::middleware(['auth', 'isAdmin'])->group(function () {

    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard.index');

    Route::get('/books', [BookController::class, 'index'])->name('books.index');
    Route::post('/books', [BookController::class, 'store'])->name('books.store');
    Route::get('/books/{book}/edit', [BookController::class, 'edit'])->name('books.edit');
    Route::put('/books/{book}', [BookController::class, 'update'])->name('books.update');
    Route::delete('/books/{book}', [BookController::class, 'destroy'])->name('books.destroy');

    Route::get('/books/{book}/copies', [BookController::class, 'getCopies'])->name('books.copies.index');
    Route::post('/books/{book}/copies', [BookController::class, 'storeCopy'])->name('books.copies.store');
    Route::put('/books/{book}/copies/{copy}', [BookController::class, 'updateCopy'])->name('books.copies.update');
    Route::delete('/books/{book}/copies/{copy}', [BookController::class, 'destroyCopy'])->name('books.copies.destroy');
    Route::get('/books/{book}/available-copies', [ReservationController::class, 'getAvailableCopies'])->name('books.availableCopies');

    Route::get('/categories', [CategoryController::class, 'index'])->name('categories.index');
    Route::post('/categories', [CategoryController::class, 'store'])->name('categories.store');
    Route::get('/categories/{category}/edit', [CategoryController::class, 'edit'])->name('categories.edit');
    Route::put('/categories/{category}', [CategoryController::class, 'update'])->name('categories.update');
    Route::delete('/categories/{category}', [CategoryController::class, 'destroy'])->name('categories.destroy');

    Route::get('/reservations', [ReservationController::class, 'index'])->name('reservations.index');
    Route::post('/reservations', [ReservationController::class, 'store'])->name('reservations.store');
    Route::get('/reservations/create', [ReservationController::class, 'create'])->name('reservations.create');
    Route::get('/reservations/{reservation}', [ReservationController::class, 'show'])->name('reservations.show');
    Route::put('/reservations/{reservation}', [ReservationController::class, 'update'])->name('reservations.update');
    Route::delete('/reservations/{reservation}', [ReservationController::class, 'destroy'])->name('reservations.destroy');
    Route::post('/reservations/{reservation}/complete', [ReservationController::class, 'complete'])->name('reservations.complete');
    Route::post('/reservations/{reservation}/extend', [ReservationController::class, 'extend'])->name('reservations.extend');
    Route::post('/reservations/{reservation}/cancel', [ReservationController::class, 'cancel'])->name('reservations.cancel');
    Route::post('/reservations/bulk-action', [ReservationController::class, 'bulkAction'])->name('reservations.bulkAction');
    Route::get('/reservations-overdue', [ReservationController::class, 'getOverdue'])->name('reservations.overdue');
    Route::get('/reservations-stats', [ReservationController::class, 'getStats'])->name('reservations.stats');

    Route::get('/users', [UserController::class, 'index'])->name('users.index');
    Route::post('/users', [UserController::class, 'store'])->name('users.store');
    Route::get('/users/{user}', [UserController::class, 'show'])->name('users.show');
    Route::put('/users/{user}', [UserController::class, 'update'])->name('users.update');
    Route::delete('/users/{user}', [UserController::class, 'destroy'])->name('users.destroy');
    Route::post('/users/{user}/toggle-role', [UserController::class, 'toggleRole'])->name('users.toggleRole');
    Route::get('/users/{user}/reservations', [UserController::class, 'getReservations'])->name('users.reservations');
    Route::post('/users/{user}/reservations/{reservation}/complete', [UserController::class, 'completeReservation'])->name('users.reservations.complete');
    Route::post('/users/{user}/reservations/{reservation}/extend', [UserController::class, 'extendReservation'])->name('users.reservations.extend');
    Route::post('/users/{user}/reservations/{reservation}/cancel', [UserController::class, 'cancelReservation'])->name('users.reservations.cancel');

});


/*****************************
 * SHOWCASE FOR USERS
 * ***************************/
Route::get('/', [ShowcaseController::class, 'index'])->name('showcase.index');
Route::get('/search', [BookController::class, 'search'])->name('showcase.search');

Route::prefix('showcase')->name('showcase.')->middleware('auth')->group(function () {
    // Prenotazioni
    Route::get('/my-reservations', [ShowcaseController::class, 'myReservations'])->name('my-reservations');
    Route::post('/reserve', [ShowcaseController::class, 'store'])->name('reserve');
    Route::delete('/reservation/{reservation}/cancel', [ShowcaseController::class, 'cancel'])->name('reservation.cancel');
});