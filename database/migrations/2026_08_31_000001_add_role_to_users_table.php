<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Adiciona a coluna de papel (leitor/autor/admin) aos usuários.
     *
     * Novos cadastros nascem como 'leitor'; os usuários existentes (criados
     * via seed) recebem 'admin' para o blog não perder acesso ao painel.
     */
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->string('role')->default('leitor')->after('password');
        });

        DB::table('users')->update(['role' => 'admin']);
    }

    /**
     * Reverte a migração.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn('role');
        });
    }
};
