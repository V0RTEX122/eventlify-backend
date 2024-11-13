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
        Schema::create('tasks', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedBigInteger('event_id');
            $table->string('title', 191);
            $table->text('description')->nullable();
            $table->dateTime('due_date');
            $table->unsignedBigInteger('assigned_to');
            $table->enum('status', ['pending', 'ongoing', 'completed'])->default('pending');
            $table->timestamps();

            // Indexes
            $table->index('event_id');
            $table->index('assigned_to');
            $table->index('status');

            // Foreign keys
            $table->foreign('event_id')->references('id')->on('events')->onDelete('cascade');
            $table->foreign('assigned_to')->references('id')->on('users')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('tasks');
    }
};
