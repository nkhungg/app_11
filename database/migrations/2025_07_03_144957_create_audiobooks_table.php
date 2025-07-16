<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
public function up()
{
    Schema::create('audiobooks', function (Blueprint $table) {
        $table->id();
        $table->foreignId('product_id')->constrained()->onDelete('cascade');
        $table->string('file_path'); // path to the audio file
        $table->string('duration')->nullable(); // optional: total play time
        $table->timestamps();
    });
}

public function down()
{
    Schema::dropIfExists('audiobooks');
}
};
