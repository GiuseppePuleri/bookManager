<?php
namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Session;
use Illuminate\Validation\Rules\Password;

class AuthController
{

    /*
    * @context: Display the login form based on user type (admin or regular user).
    * @problem: none
    * @solution: Check if 'isAdmin' is present in the request, redirect authenticated users accordingly, and return the appropriate login view.
    * @impact: Provides a clear separation between admin and user login workflows.
    */
    public function showLogin(Request $request){
        // Verifico se il parametro isAdmin è presente nell'URL e usarlo come discriminante
        if ($request->has('isAdmin')) {
            
            if (auth()->check()) {
                if (auth()->user()->role === 'admin') {
                    return redirect()->route('dashboard.index');
                }
            }
            
            return view('showcase.pages.admin-login');

        } else {

            if (auth()->check()) {
                if (auth()->user()->role === 'user') {
                    return redirect()->route('showcase.my-reservations');
                }
            }

            return view('showcase.pages.showcase-login');
        }
    }

    /*
    * @context: Handle user authentication and session creation.
    * @problem: none
    * @solution: Validate credentials, verify password, log in the user, regenerate session,and redirect based on role.
    * @impact: Ensures secure login flow with proper session management and role-based redirection.
    */
    public function login(Request $request){
        try {
            $request->validate([
                'email'    => 'required|email',
                'password' => 'required',
            ], [
                'email.required' => 'L\'email è obbligatoria.',
                'email.email' => 'Inserisci un\'email valida.',
                'password.required' => 'La password è obbligatoria.',
            ]);

            $user = User::where('email', $request->email)->first();

            if (!$user) {
                return back()->withErrors(['email' => 'Credenziali non valide!'])->withInput($request->only('email'));
            }

            // Check password
            if (!Hash::check($request->password, $user->password)) {
                return back()->withErrors(['email' => 'Credenziali non valide!'])->withInput($request->only('email'));
            }

            Auth::login($user);
            Session::regenerate();

            // Redirect in base al ruolo
            if ($user->role === 'admin') {
                return redirect()->route('dashboard.index')->with('success', 'Benvenuto, ' . $user->name . '!');
            } else {
                return redirect()->route('showcase.index')->with('success', 'Accesso effettuato con successo!');
            }

        } catch (\Illuminate\Validation\ValidationException $e) {
            // Validations failed
            throw $e;
        } catch (\Exception $e) {
            // Generic error
            \Log::error('Login error: ' . $e->getMessage());
            
            return back()->withErrors([
                'email' => 'Si è verificato un errore durante l\'accesso. Riprova.'
            ])->withInput($request->except('password'));
        }
    }

    public function register(Request $request){
        try {
            $validated = $request->validate([
                'name' => 'required|string|max:255',
                'email' => 'required|email|unique:users,email',
                'password' => ['required', 'confirmed', Password::min(8)],
            ], [
                'name.required' => 'Il nome è obbligatorio.',
                'name.max' => 'Il nome non può superare 255 caratteri.',
                'email.required' => 'L\'email è obbligatoria.',
                'email.email' => 'Inserisci un\'email valida.',
                'email.unique' => 'Questa email è già registrata.',
                'password.required' => 'La password è obbligatoria.',
                'password.confirmed' => 'Le password non coincidono.',
                'password.min' => 'La password deve essere di almeno 8 caratteri.',
            ]);

            // Crea il nuovo utente con ruolo 'user' di default
            $user = User::create([
                'name' => $validated['name'],
                'email' => $validated['email'],
                'password' => Hash::make($validated['password']),
                'role' => 'user', // Ruolo utente normale
            ]);

            // Login automatico dopo la registrazione
            Auth::login($user);
            Session::regenerate();

            return redirect()->route('showcase.index')
                ->with('success', 'Registrazione completata! Benvenuto, ' . $user->name . '!');

        } catch (\Illuminate\Validation\ValidationException $e) {
            // Validations failed
            throw $e;
        } catch (\Exception $e) {
            // Generic error
            \Log::error('Registration error: ' . $e->getMessage());
            
            return back()->withErrors([
                'email' => 'Si è verificato un errore durante la registrazione. Riprova.'
            ])->withInput($request->except('password', 'password_confirmation'));
        }
    }

    /*
    * @context: Log out the authenticated user and invalidate their session.
    * @problem: none
    * @solution: Perform logout, invalidate session, regenerate CSRF token, and handle any exceptions gracefully.
    * @impact: Ensures secure logout and prevents session fixation attacks.
    */
    public function logout(Request $request){
        try {
            Auth::logout();
            
            // Invalidate the session to prevent session fixation attacks
            Session::invalidate();
            
            // Regenerate CSRF token for security
            Session::regenerateToken();

            return redirect()->route('showcase.index')
                ->with('success', 'Logout effettuato con successo!');
 
        } catch (\Exception $e) {
            // Log logout errors for debugging
            \Log::error('Logout error: ' . $e->getMessage());
            
            // Force logout even if errors occur
            Auth::logout();
            
            return redirect()->route('showcase')->with('error', 'An error occurred during logout.');
        }
    }
}
