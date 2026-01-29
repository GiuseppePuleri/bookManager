<?php

namespace Database\Seeders\subSeeders; 

use Illuminate\Database\Seeder;
use App\Models\Category;

class CategoriesSeeder extends Seeder
{
    public function run(): void
    {
        $categories = [
            [
                'name' => 'Programmazione',
                'description' => 'Libri dedicati allo sviluppo software e ai linguaggi di programmazione.'
            ],
            [
                'name' => 'Intelligenza Artificiale',
                'description' => 'Algoritmi, machine learning e applicazioni dell’IA.'
            ],
            [
                'name' => 'Sicurezza Informatica',
                'description' => 'Protezione dei sistemi, reti e applicazioni.'
            ],
        ];

        foreach ($categories as $category) {
            Category::firstOrCreate(
                ['name' => $category['name']],
                ['description' => $category['description']]
            );
        }

        $this->command->info('Categorie create con successo!');
    }
}
