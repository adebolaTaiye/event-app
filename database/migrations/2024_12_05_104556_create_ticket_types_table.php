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
        Schema::create('ticket_types', function (Blueprint $table) {
            $table->id();
            $table->foreignIdFor(\App\Models\Event::class,'event_id');
            $table->string('ticket_type')->nullable();
            $table->unsignedBigInteger('ticket_count')->nullable();
            $table->unsignedBigInteger('ticket_available')->nullable();
            $table->decimal('ticket_price',19,2)->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('ticket_types');
    }
};
