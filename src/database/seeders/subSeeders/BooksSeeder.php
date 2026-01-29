<?php

namespace Database\Seeders\subSeeders; 

use Illuminate\Database\Seeder;
use App\Models\Book;
use App\Models\Category;
use Illuminate\Support\Str;

class BooksSeeder extends Seeder
{
    public function run(): void{
        $categoryIds = \App\Models\Category::pluck('id')->toArray();

        if (empty($categoryIds)) {
            $this->command->warn('Nessuna categoria trovata. Seeder annullato.');
            return;
        }

        $books = [
            [
                'title' => 'Il codice del tempo',
                'author' => 'Marco Bianchi',
                'publisher' => 'Edizioni Aurora',
                'year' => 2018,
                'description' => 'Un thriller tra scienza e destino.'
            ],
            [
                'title' => 'Ombre digitali',
                'author' => 'Laura Conti',
                'publisher' => 'TechBooks',
                'year' => 2021,
                'description' => 'Un viaggio nel lato oscuro della rete.'
            ],
            [
                'title' => 'Laravel senza segreti',
                'author' => 'Giuseppe Verdi',
                'publisher' => 'DevPress',
                'year' => 2023,
                'description' => 'Guida pratica allo sviluppo con Laravel.'
            ],
            [
                'title' => 'Architettura del software',
                'author' => 'Paolo Neri',
                'publisher' => 'IT Publishing',
                'year' => 2019,
                'description' => 'Principi e pattern per sistemi complessi.'
            ],
            [
                'title' => 'La mente algoritmica',
                'author' => 'Sara Rinaldi',
                'publisher' => 'NeuralBooks',
                'year' => 2020,
                'description' => 'Come gli algoritmi influenzano il pensiero umano.'
            ],
            [
                'title' => 'PHP moderno',
                'author' => 'Luca Ferri',
                'publisher' => 'CodeHouse',
                'year' => 2022,
                'description' => 'Best practice e performance in PHP.'
            ],
            [
                'title' => 'Database senza paura',
                'author' => 'Alessandro Russo',
                'publisher' => 'DataPress',
                'year' => 2017,
                'description' => 'SQL e modellazione spiegati semplice.'
            ],
            [
                'title' => 'Intelligenza artificiale applicata',
                'author' => 'Francesco De Luca',
                'publisher' => 'AI Publishing',
                'year' => 2023,
                'description' => 'Applicazioni pratiche dell’IA nel mondo reale.'
            ],
            [
                'title' => 'Clean Code in pratica',
                'author' => 'Matteo Galli',
                'publisher' => 'CodeHouse',
                'year' => 2016,
                'description' => 'Scrivere codice leggibile, manutenibile ed efficace.'
            ],
            [
                'title' => 'Sicurezza informatica essenziale',
                'author' => 'Davide Moretti',
                'publisher' => 'SecurePress',
                'year' => 2021,
                'description' => 'Fondamenti di cybersecurity per sviluppatori.'
            ],
            [
                'title' => 'Design pattern spiegati bene',
                'author' => 'Elena Fontana',
                'publisher' => 'IT Publishing',
                'year' => 2018,
                'description' => 'I principali pattern illustrati con esempi concreti.'
            ],
            [
                'title' => 'Backend scalabile',
                'author' => 'Andrea Lombardi',
                'publisher' => 'DevPress',
                'year' => 2022,
                'description' => 'Costruire API robuste e sistemi ad alta affidabilità.'
            ],
            [
                'title' => 'Versionamento con Git',
                'author' => 'Nicola Parisi',
                'publisher' => 'CodeHouse',
                'year' => 2019,
                'description' => 'Workflow professionali per team di sviluppo.'
            ],
            [
                'title' => 'Algoritmi e strutture dati',
                'author' => 'Federica Greco',
                'publisher' => 'NeuralBooks',
                'year' => 2015,
                'description' => 'Le basi teoriche per programmatori consapevoli.'
            ],
        ];


        for ($i = 1; $i <= 14; $i++) {

            $base = $books[($i - 1) % count($books)];

            \App\Models\Book::create([
                'title' => $base['title'] . " #{$i}",
                'isbn' => 'ISBN-' . strtoupper(\Illuminate\Support\Str::random(13)),
                'author' => $base['author'],
                'publisher' => $base['publisher'],
                'year' => $base['year'],
                'category_id' => $categoryIds[array_rand($categoryIds)],
                'description' => $base['description'],
                'cover_image' => "covers/book_{$i}.jpg",
            ]);
        }

        $this->command->info('14 libri fittizi creati con successo!');
    }
}
