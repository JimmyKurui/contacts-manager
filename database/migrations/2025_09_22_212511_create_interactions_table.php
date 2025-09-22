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
        Schema::create('interactions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('contact_id')->constrained()->onDelete('cascade');
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->enum('type', ['call', 'email', 'meeting', 'note', 'task', 'sms', 'other'])->default('note');
            $table->string('subject')->nullable();
            $table->text('description')->nullable();
            $table->datetime('ended_at');
            $table->enum('status', ['completed', 'pending', 'cancelled'])->default('completed');
            $table->json('metadata')->nullable(); // extra data like call duration, email thread id, etc.
            $table->timestamps();
            
            $table->index(['contact_id']);
            $table->index(['user_id']);
            $table->index(['type']);
            $table->index(['ended_at']);
            $table->index(['status']);
            $table->index(['contact_id', 'ended_at'], 'idx_contact_interactions_timeline');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('interactions');
    }
};
