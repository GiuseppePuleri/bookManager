<?php

namespace App\Http\Controllers\Dashboard;

use App\Http\Controllers\Controller;
use App\Models\Book;
use App\Models\BookCopy;
use App\Models\Category;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class BookController extends Controller
{
 
    /*
    * @context: Admin catalog management and advanced search.
    * @problem: //
    * @solution: //
    * @impact: //
    */
    public function index(Request $request){
        $search = $request->input('search');
        $filter = $request->input('filter', 'all');

        $books = Book::with(['category', 'copies'])
            ->withCount([
                'copies',
                'copies as available_copies_count' => function ($query) {
                    $query->where('status', 'available');
                },
                'copies as reserved_copies_count' => function ($query) {
                    $query->where('status', 'reserved');
                },
                'copies as loaned_copies_count' => function ($query) {
                    $query->where('status', 'loaned');
                }
            ])
            ->when($search, function ($query) use ($search, $filter) {
                switch ($filter) {
                    case 'title':
                        $query->where('title', 'LIKE', "%{$search}%");
                        break;
                    
                    case 'author':
                        $query->where('author', 'LIKE', "%{$search}%");
                        break;
                    
                    case 'isbn':
                        $query->where('isbn', 'LIKE', "%{$search}%");
                        break;
                    
                    case 'category':
                        $query->whereHas('category', function ($q) use ($search) {
                            $q->where('name', 'LIKE', "%{$search}%");
                        });
                        break;
                    
                    case 'keyword':
                        // Ricerca nella description (metadati)
                        $query->where('description', 'LIKE', "%{$search}%");
                        break;
                    
                    case 'all':
                    default:
                        // Ricerca su tutti i campi rilevanti
                        $query->where(function ($q) use ($search) {
                            $q->where('title', 'LIKE', "%{$search}%")
                            ->orWhere('author', 'LIKE', "%{$search}%")
                            ->orWhere('isbn', 'LIKE', "%{$search}%")
                            ->orWhere('description', 'LIKE', "%{$search}%")
                            ->orWhere('publisher', 'LIKE', "%{$search}%")
                            ->orWhereHas('category', function ($subQ) use ($search) {
                                $subQ->where('name', 'LIKE', "%{$search}%");
                            });
                        });
                        break;
                }
            })
            ->orderBy('created_at', 'desc')
            ->get();

        $categories = Category::orderBy('name')->get();

        return view('dashboard.pages.books', compact('books', 'categories'));
    }

    public function store(Request $request){
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'isbn' => 'required|string|max:20|unique:books,isbn',
            'author' => 'required|string|max:255',
            'publisher' => 'nullable|string|max:255',
            'year' => 'nullable|integer|min:1000|max:' . (date('Y') + 1),
            'category_id' => 'required|exists:categories,id',
            'description' => 'nullable|string|max:2000',
            'cover_image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
        ], [
            'title.required' => 'Il titolo è obbligatorio.',
            'isbn.required' => 'Il codice ISBN è obbligatorio.',
            'isbn.unique' => 'Questo ISBN è già presente nel sistema.',
            'author.required' => 'L\'autore è obbligatorio.',
            'category_id.required' => 'La categoria è obbligatoria.',
            'category_id.exists' => 'La categoria selezionata non esiste.',
            'year.min' => 'L\'anno deve essere maggiore di 1000.',
            'year.max' => 'L\'anno non può essere nel futuro.',
            'cover_image.image' => 'Il file deve essere un\'immagine.',
            'cover_image.mimes' => 'L\'immagine deve essere in formato: jpeg, png, jpg, gif.',
            'cover_image.max' => 'L\'immagine non può superare 2MB.',
        ]);

        try {
            // Handle cover image upload
            if ($request->hasFile('cover_image')) {
                $path = $request->file('cover_image')->store('covers', 'public');
                $validated['cover_image'] = $path;
            }

            $book = Book::create($validated);

            return response()->json([
                'success' => true,
                'message' => 'Libro creato con successo!',
                'book' => $book->load('category')
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Errore durante la creazione del libro: ' . $e->getMessage()
            ], 500);
        }
    }

    public function edit(Book $book){
        return response()->json([
            'success' => true,
            'book' => $book->load('category')
        ]);
    }

    /*
    * @context: Book update operation with optional cover replacement.
    * @problem: Avoid leaving orphaned files or inconsistent references.
    * @solution: Validate input, delete the existing cover image if present, store the new one, and update the book record in a single controlled flow.
    * @impact: Prevents unused files in storage and keeps database references consistent.
    */
    public function update(Request $request, Book $book){
        $validated = $request->validate([
            'title'       => 'required|string|max:255',
            'isbn'        => 'required|string|max:20|unique:books,isbn,' . $book->id,
            'author'      => 'required|string|max:255',
            'publisher'   => 'nullable|string|max:255',
            'year'        => 'nullable|integer|min:1000|max:' . (date('Y') + 1),
            'category_id' => 'required|exists:categories,id',
            'description' => 'nullable|string|max:2000',
            'cover_image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
        ], [
            'title.required'       => 'Il titolo è obbligatorio.',
            'isbn.required'        => 'Il codice ISBN è obbligatorio.',
            'isbn.unique'          => 'Questo ISBN è già presente nel sistema.',
            'author.required'      => 'L\'autore è obbligatorio.',
            'category_id.required' => 'La categoria è obbligatoria.',
            'category_id.exists'   => 'La categoria selezionata non esiste.',
            'year.min'             => 'L\'anno deve essere maggiore di 1000.',
            'year.max'             => 'L\'anno non può essere nel futuro.',
            'cover_image.image'    => 'Il file deve essere un\'immagine.',
            'cover_image.mimes'    => 'L\'immagine deve essere in formato: jpeg, png, jpg, gif.',
            'cover_image.max'      => 'L\'immagine non può superare 2MB.',
        ]);

        try {
            if ($request->hasFile('cover_image')) {
                if ($book->cover_image) {
                    Storage::disk('public')->delete($book->cover_image);
                }

                $validated['cover_image'] = $request
                    ->file('cover_image')
                    ->store('covers', 'public');
            }

            $book->update($validated);

            return response()->json([
                'success' => true,
                'message' => 'Libro aggiornato con successo!',
                'book'    => $book->load('category'),
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Errore durante l\'aggiornamento del libro: ' . $e->getMessage(),
            ], 500);
        }
    }

    /*
    * @context: Book deletion with associated media cleanup.
    * @problem: Again Avoid orphaned files in the filesystem.
    * @solution: Explicitly delete the cover image from storage before removing the database record.
    * @impact: Keeps filesystem and database consistent and prevents unnecessary storage usage.
    * @alternatives: I usually create a flag 'is_delete' to keep data in db but as deleted but in this project is not necessary
    */
    public function destroy(Book $book){
        try {
            // Delete cover image if exists
            if ($book->cover_image) {
                Storage::disk('public')->delete($book->cover_image);
            }

            $book->delete();

            return response()->json([
                'success' => true,
                'message' => 'Libro eliminato con successo!'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Errore durante l\'eliminazione del libro: ' . $e->getMessage()
            ], 500);
        }
    }

    /*
    * @context: Each book could have more copies. Return it here.
    * @problem: //
    * @solution: //
    * @impact: //
    */
    public function getCopies(Book $book){
        $copies = $book->copies()->orderBy('created_at', 'desc')->get();

        return response()->json([
            'success' => true,
            'copies' => $copies,
            'book' => $book
        ]);
    }

    /*
    * @context: Creation of a "physical" book copy.
    * @problem: Book copies require strict validation. Avoid invalid states being persisted in the database.
    * @solution: Enforce a controlled set of allowed values at validation level and explicitlybind the copy to its parent book.
    * @impact: //
    */
    public function storeCopy(Request $request, Book $book){
        $validated = $request->validate([
            'condition' => 'required|in:very good,good,bad',
            'status' => 'required|in:available,reserved,loaned,maintenance',
            'notes' => 'nullable|string|max:500',
        ], [
            'condition.required' => 'Lo stato fisico è obbligatorio.',
            'condition.in' => 'Lo stato fisico selezionato non è valido.',
            'status.required' => 'Lo stato è obbligatorio.',
            'status.in' => 'Lo stato selezionato non è valido.',
            'notes.max' => 'Le note non possono superare 500 caratteri.',
        ]);

        try {
            $validated['book_id'] = $book->id;
            $copy = BookCopy::create($validated);

            return response()->json([
                'success' => true,
                'message' => 'Copia creata con successo!',
                'copy' => $copy
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Errore durante la creazione della copia: ' . $e->getMessage()
            ], 500);
        }
    }

    /*
    * @context: Update copies.
    * @problem: //
    * @solution: //
    * @impact: //
    */
    public function updateCopy(Request $request, Book $book, BookCopy $copy){
        $validated = $request->validate([
            'condition' => 'required|in:very good,good,bad',
            'status' => 'required|in:available,reserved,loaned,maintenance',
            'notes' => 'nullable|string|max:500',
        ], [
            'condition.required' => 'Lo stato fisico è obbligatorio.',
            'condition.in' => 'Lo stato fisico selezionato non è valido.',
            'status.required' => 'Lo stato è obbligatorio.',
            'status.in' => 'Lo stato selezionato non è valido.',
            'notes.max' => 'Le note non possono superare 500 caratteri.',
        ]);

        try {
            $copy->update($validated);

            return response()->json([
                'success' => true,
                'message' => 'Copia aggiornata con successo!',
                'copy' => $copy
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Errore durante l\'aggiornamento della copia: ' . $e->getMessage()
            ], 500);
        }
    }

    /*
    * @context: Delete copies.
    * @problem: //
    * @solution: //
    * @impact: //
    * @alternatives: Again. I usually create a flag 'is_delete' to keep data in db but as deleted but in this project is not necessary
    */
    public function destroyCopy(Book $book, BookCopy $copy){
        try {
            $copy->delete();

            return response()->json([
                'success' => true,
                'message' => 'Copia eliminata con successo!'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Errore durante l\'eliminazione della copia: ' . $e->getMessage()
            ], 500);
        }
    }

    /*
    * @context: User not Auth can search available books in show.index.
    * @problem: //
    * @solution: //
    * @impact: //
    */
    public function search(Request $request){
        // Validazione parametri
        $validated = $request->validate([
            'query' => 'nullable|string|max:255',
            'category_id' => 'nullable|exists:categories,id',
            'author' => 'nullable|string|max:255',
            'year_from' => 'nullable|integer|min:1900|max:' . date('Y'),
            'year_to' => 'nullable|integer|min:1900|max:' . date('Y'),
            'available_only' => 'nullable|boolean',
            'date_from' => 'nullable|date',
            'date_to' => 'nullable|date|after_or_equal:date_from',
        ]);

        // Query base con relazioni e conteggio copie disponibili
        $query = Book::with(['category', 'copies'])
            ->withCount([
                'copies as available_copies_count' => function ($query) {
                    $query->where('status', 'available');
                }
            ]);

        // Filtro: Ricerca testuale (titolo, autore, ISBN, descrizione)
        if ($request->filled('query')) {
            $searchTerm = $request->input('query'); // ✅ CORRETTO
            $query->where(function ($q) use ($searchTerm) {
                $q->where('title', 'LIKE', "%{$searchTerm}%")
                ->orWhere('author', 'LIKE', "%{$searchTerm}%")
                ->orWhere('isbn', 'LIKE', "%{$searchTerm}%")
                ->orWhere('description', 'LIKE', "%{$searchTerm}%");
            });
        }

        // Filtro: Categoria
        if ($request->filled('category_id')) {
            $query->where('category_id', $request->category_id);
        }

        // Filtro: Autore specifico
        if ($request->filled('author')) {
            $query->where('author', 'LIKE', "%{$request->author}%");
        }

        // Filtro: Anno pubblicazione (range)
        if ($request->filled('year_from')) {
            $query->where('year', '>=', $request->year_from);
        }
        if ($request->filled('year_to')) {
            $query->where('year', '<=', $request->year_to);
        }

        // Filtro: Solo libri con copie disponibili
        if ($request->filled('available_only') && $request->available_only) {
            $query->has('copies', '>=', 1)
                ->whereHas('copies', function ($q) {
                    $q->where('status', 'available');
                });
        }

        // Ordina per titolo
        $query->orderBy('title');

        // Esegui la query
        $books = $query->get();

        // Recupera tutte le categorie per i filtri
        $categories = Category::orderBy('name')->get();

        // Se è una richiesta AJAX, ritorna JSON
        if ($request->ajax() || $request->wantsJson()) {
            return response()->json([
                'success' => true,
                'count' => $books->count(),
                'books' => $books->map(function ($book) {
                    return [
                        'id' => $book->id,
                        'title' => $book->title,
                        'author' => $book->author,
                        'year' => $book->year,
                        'isbn' => $book->isbn,
                        'publisher' => $book->publisher,
                        'description' => $book->description,
                        'cover_url' => $book->cover_url,
                        'category' => [
                            'id' => $book->category->id ?? null,
                            'name' => $book->category->name ?? 'N/A',
                        ],
                        'available_copies_count' => $book->available_copies_count,
                    ];
                }),
            ]);
        }

        // Se non è AJAX, ritorna la view normale
        return view('showcase.pages.showcase', compact('books', 'categories'));
    }
}