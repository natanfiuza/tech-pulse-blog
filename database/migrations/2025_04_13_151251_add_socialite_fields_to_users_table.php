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
        Schema::table('users', function (Blueprint $table) {
            $table->string('google_id')->nullable()->unique()->after('id'); // ID do Google
            $table->string('avatar')->nullable()->after('email');           // Avatar do Google
            $table->string('password')->nullable()->change();               // Tornar senha nula
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn('google_id');
            $table->dropColumn('avatar');
            $table->string('password')->nullable(false)->change(); // Reverter senha para não nula
        });
    }
};
