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
        Schema::create('book_images', function (Blueprint $table) {
            $table->id();
            $table->foreignId('book_id')->constrained('books', 'book_id')->onDelete('cascade');
            $table->string('path');
            $table->timestamps();
        });
         // Drop the old image column
        Schema::table('books', function (Blueprint $table) {
            $table->dropColumn('image');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('book_images');
        Schema::table('books', function (Blueprint $table) {
            $table->string('image')->nullable()->after('description');
        });
    }
};
