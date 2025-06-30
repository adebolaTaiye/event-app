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
        Schema::create('user_event_registrations', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained();
            $table->foreignId('event_id')->constrained();
            $table->foreignId('ticket_type_id')->nullable()->constrained();
            $table->unsignedInteger('quantity')->nullable();
            $table->string('reference')->nullable();
            $table->unsignedBigInteger('amount')->nullable();
            $table->enum('status', ['pending', 'paid']);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('user_event_registrations');
    }
};
