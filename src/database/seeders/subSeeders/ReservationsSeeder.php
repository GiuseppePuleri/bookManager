<?php

namespace Database\Seeders\subSeeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use App\Models\BookCopy;
use App\Models\Reservation;
use Carbon\Carbon;

class ReservationsSeeder extends Seeder
{
    public function run(): void
    {
        $users = User::all();
        $availableCopies = BookCopy::where('status', 'available')->get();

        if ($users->isEmpty() || $availableCopies->isEmpty()) {
            $this->command->warn('Utenti o copie disponibili mancanti. Seeder Reservations annullato.');
            return;
        }

        // Numero massimo di prenotazioni da creare
        $reservationsCount = min(10, $availableCopies->count());

        $copies = $availableCopies->shuffle()->take($reservationsCount);

        foreach ($copies as $copy) {
            $user = $users->random();

            Reservation::create([
                'user_id'      => $user->id,
                'book_copy_id' => $copy->id,
                'reserved_at'  => now(),
                'due_date'     => Carbon::now()->addDays(rand(7, 21)),
                'status'       => 'active',
            ]);

            // Coerente con la store()
            $copy->update([
                'status' => 'loaned',
            ]);
        }

        $this->command->info("{$reservationsCount} prenotazioni create con successo!");
    }
}
