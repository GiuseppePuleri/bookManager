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
        Schema::create('books', function (Blueprint $table) {
            $table->id();
            $table->string('title', 255);
            $table->string('isbn', 20)->unique();
            $table->text('description')->nullable();
            $table->string('author', 255);
            $table->string('publisher', 255)->nullable();
            $table->year('year')->nullable();
            $table->foreignId('category_id')->constrained('categories')->onDelete('cascade');
            $table->string('cover_image', 255)->nullable();
            $table->timestamps();
            $table->softDeletes();

            // Indici
            $table->index('title');
            $table->index('author');
            $table->index('year');
            $table->index('category_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('books');
    }
};
