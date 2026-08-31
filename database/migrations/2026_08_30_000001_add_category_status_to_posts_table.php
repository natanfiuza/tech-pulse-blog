<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Adiciona categoria, status de publicação e data de publicação aos posts.
     */
    public function up(): void
    {
        Schema::table('posts', function (Blueprint $table) {
            $table->foreignId('category_id')
                ->nullable()
                ->after('content')
                ->constrained('categories')
                ->nullOnDelete();
            $table->string('status')
                ->default('publicado')
                ->after('category_id')
                ->comment('rascunho|publicado|agendado');
            $table->timestamp('published_at')
                ->nullable()
                ->after('status');
            $table->index(['status', 'published_at']);
        });

        // Backfill: posts existentes são considerados publicados na data de criação
        DB::table('posts')->whereNull('published_at')->update([
            'status' => 'publicado',
            'published_at' => DB::raw('created_at'),
        ]);
    }

    /**
     * Reverte a migração.
     */
    public function down(): void
    {
        Schema::table('posts', function (Blueprint $table) {
            $table->dropIndex(['status', 'published_at']);
            $table->dropConstrainedForeignId('category_id');
            $table->dropColumn(['status', 'published_at']);
        });
    }
};
