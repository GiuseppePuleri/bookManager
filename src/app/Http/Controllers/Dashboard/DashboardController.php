<?php
namespace App\Http\Controllers\Dashboard;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    /*
     * @context: Administrative dashboard overview.
     * @problem: Admins need a consolidated, read-only snapshot of library activity and usage trends.
     * @solution: Aggregate key statistics (books per category, copy statuses, top reserved books,and active reservations per user).
     * @impact: //
     */
    public function index(){
        // Books distribution by category
        $booksByCategory = DB::table('books')
            ->join('categories', 'books.category_id', '=', 'categories.id')
            ->select(
                'categories.name',
                DB::raw('COUNT(books.id) as total')
            )
            ->groupBy('categories.name')
            ->get();

        // Physical copies grouped by current status
        $copiesStatus = DB::table('book_copies')
            ->select(
                'status',
                DB::raw('COUNT(*) as total')
            )
            ->groupBy('status')
            ->get();

        // Top 5 most reserved books (based on reservation count)
        $mostReservedBooks = DB::table('reservations')
            ->join('book_copies', 'reservations.book_copy_id', '=', 'book_copies.id')
            ->join('books', 'book_copies.book_id', '=', 'books.id')
            ->select(
                'books.title',
                DB::raw('COUNT(reservations.id) as total')
            )
            ->groupBy('books.title')
            ->orderByDesc('total')
            ->limit(5)
            ->get();

        // Active reservations grouped by user
        $activeReservationsByUser = DB::table('reservations')
            ->join('users', 'reservations.user_id', '=', 'users.id')
            ->where('reservations.status', 'active')
            ->select(
                'users.name',
                DB::raw('COUNT(reservations.id) as total')
            )
            ->groupBy('users.name')
            ->get();

        return view('dashboard.pages.dashboard', compact(
            'booksByCategory',
            'copiesStatus',
            'mostReservedBooks',
            'activeReservationsByUser'
        ));
    }
}
