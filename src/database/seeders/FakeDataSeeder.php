<?php
namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Database\Seeders\subSeeders\AdminUserSeeder;
use Database\Seeders\subSeeders\CategoriesSeeder;
use Database\Seeders\subSeeders\BooksSeeder;
use Database\Seeders\subSeeders\BookCopiesSeeder;
use Database\Seeders\subSeeders\UsersSeeder;
use Database\Seeders\subSeeders\ReservationsSeeder;


class FakeDataSeeder extends Seeder
{
    public function run(): void
    {
        $this->call([
            AdminUserSeeder::class,
            CategoriesSeeder::class,
            BooksSeeder::class,
            BookCopiesSeeder::class,
            UsersSeeder::class,
            ReservationsSeeder::class,
        ]);
    }
}
//EXECUTE This Seeder: php artisan db:seed --class=Database\\Seeders\\FakeDataSeeder
