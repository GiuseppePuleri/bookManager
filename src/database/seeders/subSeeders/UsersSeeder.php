<?php

namespace Database\Seeders\subSeeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class UsersSeeder extends Seeder
{
    public function run(): void
    {

        $users = [
            [
                'name' => 'Silvio Gialli',
                'email' => 'gialli@library.test',
                'password' => 'password123',
                'role' => 'user',
            ],
            [
                'name' => 'Mario Rossi',
                'email' => 'mario.rossi@library.test',
                'password' => 'password123',
                'role' => 'user',
            ],
            [
                'name' => 'Giulia Bianchi',
                'email' => 'giulia.bianchi@library.test',
                'password' => 'password123',
                'role' => 'user',
            ],
        ];

        foreach ($users as $data) {
            User::create([
                'name'     => $data['name'],
                'email'    => $data['email'],
                'password' => Hash::make($data['password']),
                'role'     => $data['role'],
            ]);
        }

        $this->command->info('Utenti fake creati con successo!');
    }
}
