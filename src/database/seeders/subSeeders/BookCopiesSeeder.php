<?php

namespace Database\Seeders\subSeeders;

use Illuminate\Database\Seeder;
use App\Models\Book;
use App\Models\BookCopy;

class BookCopiesSeeder extends Seeder
{
    public function run(): void
    {
        $books = Book::all();

        if ($books->isEmpty()) {
            $this->command->warn('Nessun libro trovato. Seeder BookCopies annullato.');
            return;
        }

        $conditions = ['very good', 'good', 'bad'];

        foreach ($books as $book) {

            // Numero casuale di copie per libro (es. 1–5)
            $copiesCount = rand(1, 5);

            for ($i = 1; $i <= $copiesCount; $i++) {
                BookCopy::create([
                    'book_id'   => $book->id,
                    'condition' => $conditions[array_rand($conditions)],
                    'status'    => 'available',
                    'notes'     => rand(0, 1)
                        ? 'Copia generata automaticamente dal seeder.'
                        : null,
                ]);
            }
        }

        $this->command->info('Copie disponibili generate con successo per tutti i libri!');
    }
}
