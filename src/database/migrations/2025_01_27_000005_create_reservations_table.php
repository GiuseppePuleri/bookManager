<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('reservations', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade');
            $table->foreignId('book_copy_id')->constrained('book_copies')->onDelete('cascade');
            $table->dateTime('reserved_at');
            $table->dateTime('due_date');
            $table->dateTime('returned_at')->nullable();
            $table->enum('status', ['active', 'completed', 'cancelled'])->default('active');
            $table->integer('extended_count')->default(0);
            $table->timestamps();
            $table->softDeletes();

            // Indici
            $table->index('user_id');
            $table->index('book_copy_id');
            $table->index('status');
            $table->index('due_date');
            $table->index(['user_id', 'status']); // Per query tipo "prenotazioni attive dell'utente"
            $table->index(['book_copy_id', 'status']); // Per verificare disponibilità
            $table->index('created_at');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('reservations');
    }
};
