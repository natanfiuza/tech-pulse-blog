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
        Schema::create('user_profiles', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users')->cascadeOnDelete();
            $table->string('username')->unique();
            $table->text('bio')->nullable();
            $table->json('social_links')->nullable();
            $table->boolean('public_profile_enabled')->default(true);
            $table->boolean('show_email')->default(false);
            $table->boolean('show_name')->default(true);
            $table->boolean('show_author_box')->default(true);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('user_profiles');
    }
};
