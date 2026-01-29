<?php

namespace App\Http\Controllers\Dashboard;

use App\Http\Controllers\Controller;
use App\Models\Reservation;
use App\Models\User;
use App\Models\Book;
use App\Models\BookCopy;
use Illuminate\Http\Request;
use Carbon\Carbon;

class ReservationController extends Controller
{

    /*
    * @context: List reservations with optional filtering by status and search term.
    * @problem: //
    * @solution: Build a query eager-loading related user and book data, apply status and text filters,  and retrieve statistics for the dashboard.
    * @impact: Provides admins a comprehensive view of all reservations with relevant metrics.
    */
    public function index(Request $request){
        $search = $request->input('search');
        $status = $request->input('status');

        $query = Reservation::with([
            'user',
            'bookCopy.book.category'
        ]);

        // Filtro per status
        if ($status) {
            if ($status === 'overdue') {
                // Prenotazioni in ritardo (attive ma scadute)
                $query->where('status', 'active')
                    ->where('due_date', '<', now());
            } else {
                $query->where('status', $status);
            }
        }

        // Ricerca testuale
        if ($search) {
            $query->where(function ($q) use ($search) {
                // Ricerca per nome utente
                $q->whereHas('user', function ($userQuery) use ($search) {
                    $userQuery->where('name', 'LIKE', "%{$search}%")
                            ->orWhere('email', 'LIKE', "%{$search}%");
                })
                // Ricerca per titolo libro
                ->orWhereHas('bookCopy.book', function ($bookQuery) use ($search) {
                    $bookQuery->where('title', 'LIKE', "%{$search}%")
                            ->orWhere('author', 'LIKE', "%{$search}%")
                            ->orWhere('isbn', 'LIKE', "%{$search}%");
                });
            });
        }

        $reservations = $query->orderBy('created_at', 'desc')->get();

        // Statistiche globali
        $stats = [
            'total' => Reservation::count(),
            'active' => Reservation::where('status', 'active')->count(),
            'completed' => Reservation::where('status', 'completed')->count(),
            'cancelled' => Reservation::where('status', 'cancelled')->count(),
            'overdue' => Reservation::where('status', 'active')
                                    ->where('due_date', '<', now())
                                    ->count(),
        ];

        // Lista utenti per filtro (se ne hai bisogno)
        $users = User::orderBy('name')->get();

        return view('dashboard.pages.reservations', compact('reservations', 'stats', 'users'));
    }   

    /*
    * @context: Show the form to create a new reservation.
    * @problem: none
    * @solution: Retrieve all users and books (with categories) to populate selection fields in the form.
    * @impact: Simplifies the reservation creation workflow and ensures proper data association.
    */
    public function create(){
        $users = User::orderBy('name')->get();
        $books = Book::with('category')->orderBy('title')->get();

        return view('dashboard.reservations.create', compact('users', 'books'));
    }

    /*
    * @context: Retrieve available copies of a specific book.
    * @problem: //
    * @solution: Query only copies with 'available' status and sort them by condition for optimal selection.
    * @impact: Ensures admins and users can see and select the best available copy for a reservation.
    */
    public function getAvailableCopies(Book $book){
        $copies = $book->copies()
            ->where('status', 'available')
            ->get(['id', 'barcode', 'condition'])
            ->sortBy(function ($copy) {
                return match ($copy->condition) {
                    'very good' => 1,
                    'good' => 2,
                    'bad' => 3,
                    default => 4,
                };
            })
            ->values();

        return response()->json([
            'success' => true,
            'copies' => $copies,
        ]);
    }

    /*
    * @context: Create a new reservation for a user and a specific book copy.
    * @problem: Potential race conditions if multiple reservations are created for the same copy simultaneously.
    * @solution: Validate input, ensure the selected copy is still available, create the reservation, and immediately update the copy's status to 'loaned'.
    * @impact: Prevents double-booking and maintains consistent reservation and copy states.
    */
    public function store(Request $request){
        $validated = $request->validate([
            'user_id' => 'required|exists:users,id',
            'book_copy_id' => 'required|exists:book_copies,id',
            'due_date' => 'nullable|date|after:today',
        ], [
            'user_id.required' => 'L\'utente è obbligatorio.',
            'user_id.exists' => 'L\'utente selezionato non esiste.',
            'book_copy_id.required' => 'La copia è obbligatoria.',
            'book_copy_id.exists' => 'La copia selezionata non esiste.',
            'due_date.after' => 'La scadenza deve essere futura.',
        ]);

        try {
            // Verifica che la copia sia disponibile
            $bookCopy = BookCopy::findOrFail($validated['book_copy_id']);
            
            if ($bookCopy->status !== 'available') {
                return response()->json([
                    'success' => false,
                    'message' => 'La copia selezionata non è disponibile.'
                ], 422);
            }

            // Crea prenotazione
            $reservation = Reservation::create([
                'user_id' => $validated['user_id'],
                'book_copy_id' => $validated['book_copy_id'],
                'reserved_at' => now(),
                'due_date' => $validated['due_date'] ?? now()->addDays(14),
                'status' => 'active',
            ]);

            // Aggiorna stato copia
            $bookCopy->update(['status' => 'loaned']);

            return response()->json([
                'success' => true,
                'message' => 'Prenotazione creata con successo!',
                'reservation' => $reservation->load(['user', 'bookCopy.book'])
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Errore durante la creazione: ' . $e->getMessage()
            ], 500);
        }
    }

    public function show(Reservation $reservation){
        $reservation->load([
            'user',
            'bookCopy.book.category'
        ]);

        // Calcola informazioni aggiuntive
        $info = [
            'is_overdue' => $reservation->isOverdue(),
            'days_until_due' => $reservation->days_until_due,
            'days_overdue' => $reservation->days_overdue,
            'can_extend' => $reservation->isActive() && $reservation->extended_count < 3,
            'duration_days' => $reservation->reserved_at->diffInDays($reservation->due_date),
        ];

        return response()->json([
            'success' => true,
            'reservation' => $reservation,
            'info' => $info
        ]);
    }

    public function update(Request $request, Reservation $reservation){
        $validated = $request->validate([
            'due_date' => 'required|date',
            'status' => 'required|in:active,completed,cancelled',
        ], [
            'due_date.required' => 'La scadenza è obbligatoria.',
            'due_date.date' => 'Inserisci una data valida.',
            'status.required' => 'Lo stato è obbligatorio.',
            'status.in' => 'Lo stato selezionato non è valido.',
        ]);

        try {
            $oldStatus = $reservation->status;
            
            $reservation->update($validated);

            // Se lo stato cambia da active a completed/cancelled, libera la copia
            if ($oldStatus === 'active' && in_array($validated['status'], ['completed', 'cancelled'])) {
                $reservation->bookCopy->update(['status' => 'available']);
                
                if ($validated['status'] === 'completed') {
                    $reservation->update(['returned_at' => now()]);
                }
            }

            return response()->json([
                'success' => true,
                'message' => 'Prenotazione aggiornata con successo!',
                'reservation' => $reservation->load(['user', 'bookCopy.book'])
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Errore durante l\'aggiornamento: ' . $e->getMessage()
            ], 500);
        }
    }

    public function complete(Reservation $reservation){
        if (!$reservation->isActive()) {
            return response()->json([
                'success' => false,
                'message' => 'La prenotazione non è attiva.'
            ], 422);
        }

        try {
            $reservation->complete();

            return response()->json([
                'success' => true,
                'message' => 'Libro restituito con successo!',
                'reservation' => $reservation->load(['user', 'bookCopy.book'])
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Errore durante la restituzione: ' . $e->getMessage()
            ], 500);
        }
    }

    public function extend(Request $request, Reservation $reservation){
        $validated = $request->validate([
            'days' => 'required|integer|min:1|max:30',
        ], [
            'days.required' => 'I giorni sono obbligatori.',
            'days.integer' => 'Inserisci un numero valido.',
            'days.min' => 'Minimo 1 giorno.',
            'days.max' => 'Massimo 30 giorni.',
        ]);

        if (!$reservation->isActive()) {
            return response()->json([
                'success' => false,
                'message' => 'La prenotazione non è attiva.'
            ], 422);
        }

        // Limite estensioni
        if ($reservation->extended_count >= 3) {
            return response()->json([
                'success' => false,
                'message' => 'Limite massimo di estensioni raggiunto (3).'
            ], 422);
        }

        try {
            $reservation->extend($validated['days']);

            return response()->json([
                'success' => true,
                'message' => "Prestito esteso di {$validated['days']} giorni!",
                'new_due_date' => $reservation->due_date->format('d/m/Y'),
                'extended_count' => $reservation->extended_count
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Errore durante l\'estensione: ' . $e->getMessage()
            ], 500);
        }
    }

    public function cancel(Reservation $reservation){
        if (!$reservation->isActive()) {
            return response()->json([
                'success' => false,
                'message' => 'La prenotazione non è attiva.'
            ], 422);
        }

        try {
            $reservation->cancel();

            return response()->json([
                'success' => true,
                'message' => 'Prenotazione annullata con successo!',
                'reservation' => $reservation->load(['user', 'bookCopy.book'])
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Errore durante l\'annullamento: ' . $e->getMessage()
            ], 500);
        }
    }

    public function destroy(Reservation $reservation){
        // Solo prenotazioni completate o annullate possono essere eliminate
        if ($reservation->status === 'active') {
            return response()->json([
                'success' => false,
                'message' => 'Non puoi eliminare una prenotazione attiva. Annullala prima.'
            ], 422);
        }

        try {
            $reservation->delete();

            return response()->json([
                'success' => true,
                'message' => 'Prenotazione eliminata con successo!'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Errore durante l\'eliminazione: ' . $e->getMessage()
            ], 500);
        }
    }

    /*
    * @context: Retrieve all active reservations that are overdue.
    * @problem: //
    * @solution: Query reservations with status 'active' and due_date in the past, eager-loading related user and book data.
    * @impact: Provides admins with an up-to-date list of overdue reservations for monitoring and follow-up actions.
    */
    public function getOverdue(){
        $overdueReservations = Reservation::with([
            'user',
            'bookCopy.book.category'
        ])
        ->where('status', 'active')
        ->where('due_date', '<', now())
        ->orderBy('due_date', 'asc')
        ->get();

        return response()->json([
            'success' => true,
            'reservations' => $overdueReservations,
            'count' => $overdueReservations->count()
        ]);
    }

    /*
    * @context: Get statistics for dashboard.
    * @problem: //
    * @solution: //
    * @impact: //
    */
    public function getStats(){
        $stats = [
            'total' => Reservation::count(),
            'active' => Reservation::where('status', 'active')->count(),
            'completed' => Reservation::where('status', 'completed')->count(),
            'cancelled' => Reservation::where('status', 'cancelled')->count(),
            'overdue' => Reservation::where('status', 'active')
                                    ->where('due_date', '<', now())
                                    ->count(),
            'expiring_soon' => Reservation::where('status', 'active')
                                          ->whereBetween('due_date', [now(), now()->addDays(3)])
                                          ->count(),
            'extended_count' => Reservation::where('extended_count', '>', 0)->count(),
            'avg_duration' => Reservation::where('status', 'completed')
                                         ->selectRaw('AVG(DATEDIFF(returned_at, reserved_at)) as avg')
                                         ->value('avg'),
        ];

        return response()->json([
            'success' => true,
            'stats' => $stats
        ]);
    }

    public function bulkAction(Request $request){
        $validated = $request->validate([
            'action' => 'required|in:complete,cancel',
            'reservation_ids' => 'required|array|min:1',
            'reservation_ids.*' => 'exists:reservations,id',
        ], [
            'action.required' => 'L\'azione è obbligatoria.',
            'action.in' => 'Azione non valida.',
            'reservation_ids.required' => 'Seleziona almeno una prenotazione.',
            'reservation_ids.array' => 'Formato non valido.',
        ]);

        try {
            $reservations = Reservation::whereIn('id', $validated['reservation_ids'])
                                      ->where('status', 'active')
                                      ->get();

            $count = 0;
            foreach ($reservations as $reservation) {
                if ($validated['action'] === 'complete') {
                    if ($reservation->complete()) {
                        $count++;
                    }
                } elseif ($validated['action'] === 'cancel') {
                    if ($reservation->cancel()) {
                        $count++;
                    }
                }
            }

            $actionLabel = $validated['action'] === 'complete' ? 'completate' : 'annullate';

            return response()->json([
                'success' => true,
                'message' => "{$count} prenotazioni {$actionLabel} con successo!",
                'processed' => $count
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Errore durante l\'operazione: ' . $e->getMessage()
            ], 500);
        }
    }
}