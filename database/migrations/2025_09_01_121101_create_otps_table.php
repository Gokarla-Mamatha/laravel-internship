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
        Schema::create('otps', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('user_id')->nullable();
            $table->string('email')->nullable();
            $table->string('phone')->nullable();
            $table->string('otp', 10);
            $table->timestamp('expires_at');
            $table->boolean('is_used')->default(false);
            $table->enum('type', ['login', 'reset', 'verification'])->default('verification');
            $table->timestamps();
            
            $table->index(['email', 'type']);
            $table->index(['phone', 'type']);
            $table->index(['user_id', 'type']);
            $table->index('expires_at');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('otps');
    }
};
