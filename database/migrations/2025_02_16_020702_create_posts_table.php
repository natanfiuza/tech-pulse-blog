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
        Schema::create('posts', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users');
            $table->uuid('uuid');
            $table->string('title');
            $table->string('slug')->unique()->comment('Identificador que usa o titulo para gerar um URL mais legivel');
            $table->string('image');
            $table->text('excerpt')->comment('Trecho do post');
            $table->longText('content')->comment('Conteudo completo do post');
            $table->timestamps();

        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('posts');
    }
};
