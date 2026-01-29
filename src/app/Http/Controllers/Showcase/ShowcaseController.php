<?php

namespace App\Http\Controllers\Showcase;

use App\Http\Controllers\Controller;
use App\Models\Book;
use App\Models\BookCopy;
use App\Models\Reservation;
use App\Models\Category;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class ShowcaseController extends Controller
{

    public function index(Request $request){
        $query = Book::with(['category', 'copies'])
            ->has('copies')
            ->withCount([
                'copies as available_copies_count' => function ($q) {
                    $q->where('status', 'available');
                }
            ])
            ->having('available_copies_count', '>', 0);

        // Filtro per ricerca testuale
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('title', 'like', "%{$search}%")
                  ->orWhere('author', 'like', "%{$search}%")
                  ->orWhere('isbn', 'like', "%{$search}%")
                  ->orWhere('description', 'like', "%{$search}%");
            });
        }

        // Filtro per categoria
        if ($request->filled('category_id')) {
            $query->where('category_id', $request->category_id);
        }

        // Filtro per anno
        if ($request->filled('year')) {
            $query->where('year', $request->year);
        }

        $books = $query->orderBy('created_at', 'desc')->get();
        $categories = Category::orderBy('name')->get();
        $years = Book::distinct()->whereNotNull('year')->pluck('year')->filter()->sort()->values();

        return view('showcase.pages.showcase', compact('books', 'categories', 'years'));
    }

    /*
    * @context: Show the logged-in user's reservations.
    * @problem: none
    * @solution: Ensure the user is authenticated, retrieve their reservations with related book and category data, and order by creation date.
    * @impact: Provides users with a clear view of their active and past reservations.
    */
    public function myReservations(){
        if (!Auth::check()) {
            return redirect()->route('showcase.login')
                ->with('error', 'Devi effettuare il login per vedere le tue prenotazioni.');
        }

        $reservations = Reservation::with(['bookCopy.book.category', 'user'])
            ->where('user_id', Auth::id())
            ->orderBy('created_at', 'desc')
            ->get();

        return view('showcase.pages.my-reservations', compact('reservations'));
    }

    /*
    * @context: Create a new reservation for the authenticated user.
    * @problem: Possible race condition if multiple users attempt to reserve the same book copy simultaneously.
    * @solution: Use a transaction to find the optimal available copy, create the reservation, and update the copy status atomically.
    * @impact: Prevents double-booking and ensures consistent reservation and copy states for concurrent users.
    */
    public function store(Request $request){
        if (!Auth::check()) {
            return response()->json([
                'success' => false,
                'message' => 'Devi effettuare il login per prenotare un libro.',
                'redirect' => route('showcase.login')
            ], 401);
        }

        $validated = $request->validate([
            'book_id' => 'required|exists:books,id',
        ], [
            'book_id.required' => 'Il libro è obbligatorio.',
            'book_id.exists' => 'Il libro selezionato non esiste.',
        ]);

        try {
            DB::beginTransaction();

            $book = Book::findOrFail($validated['book_id']);

            // Trova la copia ottimale disponibile (con priorità sulla condizione)
            $optimalCopy = $book->copies()
                ->where('status', 'available')
                ->get()
                ->sortBy(function ($copy) {
                    return match ($copy->condition) {
                        'very good' => 1,
                        'good' => 2,
                        'bad' => 3,
                        default => 4,
                    };
                })
                ->first();

            if (!$optimalCopy) {
                DB::rollBack();
                return response()->json([
                    'success' => false,
                    'message' => 'Spiacente, tutte le copie di questo libro sono attualmente prenotate.'
                ], 422);
            }

            // Crea la prenotazione
            $reservation = Reservation::create([
                'user_id' => Auth::id(),
                'book_copy_id' => $optimalCopy->id,
                'reserved_at' => now(),
                'due_date' => now()->addDays(14),
                'status' => 'active',
            ]);

            // Aggiorna lo stato della copia
            $optimalCopy->update(['status' => 'loaned']);

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Libro prenotato con successo! Ritiralo entro 3 giorni.',
                'reservation' => [
                    'id' => $reservation->id,
                    'book_title' => $book->title,
                    'barcode' => $optimalCopy->barcode,
                    'due_date' => $reservation->due_date->format('d/m/Y'),
                ]
            ]);

        } catch (\Exception $e) {
            DB::rollBack();
            
            return response()->json([
                'success' => false,
                'message' => 'Errore durante la prenotazione: ' . $e->getMessage()
            ], 500);
        }
    }
}