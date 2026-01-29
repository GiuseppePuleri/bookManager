<?php

namespace App\Http\Controllers\Dashboard;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Reservation;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules\Password;

class UserController extends Controller
{

    public function index(Request $request){
        $search = $request->input('search');

        $users = User::withCount([
                'reservations',
                'reservations as active_reservations_count' => function ($query) {
                    $query->where('status', 'active');
                },
                'reservations as completed_reservations_count' => function ($query) {
                    $query->where('status', 'completed');
                }
            ])
            ->when($search, function ($query) use ($search) {
                $query->where(function ($q) use ($search) {
                    $q->where('name', 'LIKE', "%{$search}%")
                    ->orWhere('email', 'LIKE', "%{$search}%");
                });
            })
            ->orderBy('created_at', 'desc')
            ->get();

        return view('dashboard.pages.users', compact('users'));
    }

    public function show(User $user){
        $user->load([
            'reservations' => function ($query) {
                $query->with(['bookCopy.book.category'])
                      ->orderBy('created_at', 'desc');
            }
        ]);

        // Calcola statistiche
        $stats = [
            'total_reservations' => $user->reservations->count(),
            'active_reservations' => $user->reservations->where('status', 'active')->count(),
            'completed_reservations' => $user->reservations->where('status', 'completed')->count(),
            'cancelled_reservations' => $user->reservations->where('status', 'cancelled')->count(),
            'overdue_reservations' => $user->reservations->filter(function ($r) {
                return $r->isOverdue();
            })->count(),
        ];

        return response()->json([
            'success' => true,
            'user' => $user,
            'stats' => $stats
        ]);
    }

    public function getReservations(User $user, Request $request){
        $query = $user->reservations()
            ->with(['bookCopy.book.category']);

        // Filter by status if provided
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        $reservations = $query->orderBy('created_at', 'desc')->get();

        return response()->json([
            'success' => true,
            'reservations' => $reservations
        ]);
    }

    /*
    * @context: Toggle a user's role between 'admin' and 'user'.
    * @problem: Allowing users to change their own role could lead to privilege escalation.
    * @solution: Prevent the currently authenticated user from modifying their own role, and safely switch the role for other users within a try/catch block.
    * @impact: Maintains security by preventing self-escalation while allowing role management for admins.
    */
    public function toggleRole(User $user){
        // Prevent toggling own role
        if (auth()->id() === $user->id) {
            return response()->json([
                'success' => false,
                'message' => 'Non puoi modificare il tuo stesso ruolo.'
            ], 403);
        }

        try {
            $newRole = $user->role === 'admin' ? 'user' : 'admin';
            $user->update(['role' => $newRole]);

            return response()->json([
                'success' => true,
                'message' => $newRole === 'admin' 
                    ? 'Utente promosso ad amministratore.' 
                    : 'Utente declassato a utente normale.',
                'new_role' => $newRole,
                'user' => $user
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Errore durante la modifica del ruolo: ' . $e->getMessage()
            ], 500);
        }
    }

    public function store(Request $request){
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|max:255|unique:users,email',
            'password' => ['required', Password::min(8)],
            'role' => 'required|in:admin,user',
        ], [
            'name.required' => 'Il nome è obbligatorio.',
            'email.required' => 'L\'email è obbligatoria.',
            'email.email' => 'Inserisci un\'email valida.',
            'email.unique' => 'Questa email è già registrata.',
            'password.required' => 'La password è obbligatoria.',
            'password.min' => 'La password deve contenere almeno 8 caratteri.',
            'role.required' => 'Il ruolo è obbligatorio.',
            'role.in' => 'Il ruolo selezionato non è valido.',
        ]);

        try {
            $validated['password'] = Hash::make($validated['password']);
            $user = User::create($validated);

            return response()->json([
                'success' => true,
                'message' => 'Utente creato con successo!',
                'user' => $user
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Errore durante la creazione dell\'utente: ' . $e->getMessage()
            ], 500);
        }
    }

    public function update(Request $request, User $user){
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|max:255|unique:users,email,' . $user->id,
            'password' => ['nullable', Password::min(8)],
        ], [
            'name.required' => 'Il nome è obbligatorio.',
            'email.required' => 'L\'email è obbligatoria.',
            'email.email' => 'Inserisci un\'email valida.',
            'email.unique' => 'Questa email è già registrata.',
            'password.min' => 'La password deve contenere almeno 8 caratteri.',
        ]);

        try {
            if (!empty($validated['password'])) {
                $validated['password'] = Hash::make($validated['password']);
            } else {
                unset($validated['password']);
            }

            $user->update($validated);

            return response()->json([
                'success' => true,
                'message' => 'Utente aggiornato con successo!',
                'user' => $user
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Errore durante l\'aggiornamento dell\'utente: ' . $e->getMessage()
            ], 500);
        }
    }

    public function destroy(User $user){
        // Prevent deleting own account
        if (auth()->id() === $user->id) {
            return response()->json([
                'success' => false,
                'message' => 'Non puoi eliminare il tuo stesso account.'
            ], 403);
        }

        try {
            // Check for active reservations
            $activeReservations = $user->reservations()->where('status', 'active')->count();
            
            if ($activeReservations > 0) {
                return response()->json([
                    'success' => false,
                    'message' => "Impossibile eliminare. L'utente ha {$activeReservations} prenotazioni attive."
                ], 422);
            }

            $user->delete();

            return response()->json([
                'success' => true,
                'message' => 'Utente eliminato con successo!'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Errore durante l\'eliminazione dell\'utente: ' . $e->getMessage()
            ], 500);
        }
    }

    public function completeReservation(User $user, Reservation $reservation){
        // Verify reservation belongs to user
        if ($reservation->user_id !== $user->id) {
            return response()->json([
                'success' => false,
                'message' => 'Prenotazione non trovata per questo utente.'
            ], 404);
        }

        try {
            if ($reservation->complete()) {
                return response()->json([
                    'success' => true,
                    'message' => 'Libro restituito con successo!'
                ]);
            } else {
                return response()->json([
                    'success' => false,
                    'message' => 'La prenotazione non è più attiva.'
                ], 422);
            }
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Errore durante la restituzione: ' . $e->getMessage()
            ], 500);
        }
    }

    public function extendReservation(User $user, Reservation $reservation, Request $request){
        // Verify reservation belongs to user
        if ($reservation->user_id !== $user->id) {
            return response()->json([
                'success' => false,
                'message' => 'Prenotazione non trovata per questo utente.'
            ], 404);
        }

        $days = $request->input('days', 7);

        try {
            if ($reservation->extend($days)) {
                return response()->json([
                    'success' => true,
                    'message' => "Prestito esteso di {$days} giorni!",
                    'new_due_date' => $reservation->due_date->format('d/m/Y')
                ]);
            } else {
                return response()->json([
                    'success' => false,
                    'message' => 'La prenotazione non è più attiva.'
                ], 422);
            }
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Errore durante l\'estensione: ' . $e->getMessage()
            ], 500);
        }
    }

    public function cancelReservation(User $user, Reservation $reservation){
        // Verify reservation belongs to user
        if ($reservation->user_id !== $user->id) {
            return response()->json([
                'success' => false,
                'message' => 'Prenotazione non trovata per questo utente.'
            ], 404);
        }

        try {
            if ($reservation->cancel()) {
                return response()->json([
                    'success' => true,
                    'message' => 'Prenotazione annullata con successo!'
                ]);
            } else {
                return response()->json([
                    'success' => false,
                    'message' => 'La prenotazione non è più attiva.'
                ], 422);
            }
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Errore durante l\'annullamento: ' . $e->getMessage()
            ], 500);
        }
    }
}