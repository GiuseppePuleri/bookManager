<?php
namespace App\Http\Controllers\Dashboard;

use App\Models\Category;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class CategoryController 
{

    public function index(Request $request){
        $search = $request->input('search');

        $categories = Category::withCount('books')
            ->when($search, function ($query) use ($search) {
                $query->where(function ($q) use ($search) {
                    $q->where('name', 'LIKE', "%{$search}%")
                    ->orWhere('description', 'LIKE', "%{$search}%");
                });
            })
            ->orderBy('created_at', 'desc')
            ->get();

        return view('dashboard.pages.categories', compact('categories'));
    }

    public function store(Request $request){
        $validated = $request->validate([
            'name' => 'required|string|max:255|unique:categories,name',
            'description' => 'nullable|string|max:1000',
        ], [
            'name.required' => 'Il nome della categoria è obbligatorio.',
            'name.unique' => 'Questa categoria esiste già.',
            'name.max' => 'Il nome non può superare 255 caratteri.',
            'description.max' => 'La descrizione non può superare 1000 caratteri.',
        ]);

        try {
            Category::create($validated);

            return response()->json([
                'success' => true,
                'message' => 'Categoria creata con successo!'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Errore durante la creazione della categoria.'
            ], 500);
        }
    }

    /*
    * @context: Update an existing category.
    * @problem: //
    * @solution:
    * @impact: Maintains data integrity and keeps category information accurate.
    */
    public function edit(Category $category){
        return response()->json([
            'success' => true,
            'category' => $category
        ]);
    }

    public function update(Request $request, Category $category){
        $validated = $request->validate([
            'name' => 'required|string|max:255|unique:categories,name,' . $category->id,
            'description' => 'nullable|string|max:1000',
        ], [
            'name.required' => 'Il nome della categoria è obbligatorio.',
            'name.unique' => 'Questa categoria esiste già.',
            'name.max' => 'Il nome non può superare 255 caratteri.',
            'description.max' => 'La descrizione non può superare 1000 caratteri.',
        ]);

        try {
            $category->update($validated);

            return response()->json([
                'success' => true,
                'message' => 'Categoria aggiornata con successo!'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Errore durante l\'aggiornamento della categoria.'
            ], 500);
        }
    }

    /*
    * @context: Delete a category after ensuring no books are associated.
    * @problem: Deleting a category with existing books would break referential integrity.
    * @solution: Check for related books before deletion and block if any exist.
    * @impact: Preserves database consistency and prevents orphaned book records.
    */
    public function destroy(Category $category){
        try {
            // Verify reletads book to category
            $booksCount = $category->books()->count();
            
            if ($booksCount > 0) {
                return response()->json([
                    'success' => false,
                    'message' => "Impossibile eliminare. Ci sono {$booksCount} libri associati a questa categoria."
                ], 422);
            }

            $category->delete();

            return response()->json([
                'success' => true,
                'message' => 'Categoria eliminata con successo!'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Errore durante l\'eliminazione della categoria.'
            ], 500);
        }
    }
}