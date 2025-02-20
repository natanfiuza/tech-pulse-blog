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
        Schema::create('categories', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('slug')->unique();
            $table->text('description')->nullable();       // Descrição da categoria
            $table->text('scope')->nullable();             // Abrangência
            $table->text('possible_contents')->nullable(); // Possíveis Conteúdos
            $table->text('post_suggestions')->nullable();  // Sugestões de Postagens

            $table->foreignId('parent_id')->nullable()->constrained('categories')->onDelete('set null');
            // Chave estrangeira recursiva
            // A chave estrangeira 'parent_id' referencia a própria tabela 'categories'.
            // 'nullable()' permite categorias de nível superior (sem pai).
            // 'constrained('categories')' estabelece a relação e adiciona o índice.
            // 'onDelete('set null')': Se uma categoria pai for excluída, define parent_id como NULL nas filhas.  Outras opções seriam 'cascade' (excluir filhas) ou 'restrict' (impedir exclusão da pai).
            $table->timestamps();

        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('categories');
    }
};
