<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Cria a tabela de solicitações de perfil de autor.
     * Um leitor preenche um formulário e um admin aprova ou rejeita.
     */
    public function up(): void
    {
        Schema::create('solicitacoes_autor', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users')->cascadeOnDelete();
            $table->text('motivacao');
            $table->enum('status', ['pendente', 'aprovada', 'rejeitada'])->default('pendente');
            $table->foreignId('admin_id')->nullable()->constrained('users')->nullOnDelete();
            $table->text('admin_nota')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Desfaz a criação da tabela.
     */
    public function down(): void
    {
        Schema::dropIfExists('solicitacoes_autor');
    }
};
