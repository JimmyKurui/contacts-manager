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
        Schema::create('contacts', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->string('first_name');
            $table->string('last_name')->nullable();
            $table->string('email')->nullable();
            $table->string('mobile_phone', 20)->nullable();
            $table->string('work_phone', 20)->nullable();
            $table->string('company')->nullable();
            $table->string('job_title')->nullable();
            $table->text('address')->nullable();
            $table->string('city')->nullable();
            $table->string('state')->nullable();
            $table->string('country')->nullable();
            $table->string('postal_code', 20)->nullable();
            $table->date('date_of_birth')->nullable();
            $table->text('notes')->nullable();
            $table->boolean('is_favorite')->default(false);
            $table->string('avatar_path', 500)->nullable();
            $table->timestamps();
            $table->softDeletes();
            
            $table->index(['user_id']);
            $table->index(['first_name', 'last_name']);
            $table->index(['email']);
            $table->index(['company']);
            $table->index(['is_favorite']);
            $table->index(['created_at']);
            $table->index(['deleted_at']);
            
            $table->index(['user_id', 'first_name', 'last_name', 'email'], 'idx_user_search');
            $table->index(['user_id', 'is_favorite'], 'idx_user_favorites');
            
            $table->fullText(['first_name', 'last_name', 'email', 'company', 'job_title'], 'idx_contacts_search');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('contacts');
    }
};
