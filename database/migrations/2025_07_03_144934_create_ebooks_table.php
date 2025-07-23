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
    Schema::create('ebooks', function (Blueprint $table) {
        $table->id();
        $table->string('title');
        $table->string('author');
        $table->string('category')->nullable();
        $table->string('description')->nullable();
        $table->string('file_path'); // path to the eBook file
        $table->string('cover_path')->nullable();
        $table->enum('format', ['pdf', 'epub']);
        $table->timestamps();
    });
}

public function down()
{
    Schema::dropIfExists('ebooks');
}

};
