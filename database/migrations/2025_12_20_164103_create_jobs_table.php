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
        Schema::create('jobs', function (Blueprint $table) {
    $table->id();

    $table->foreignId('recruiter_id')
        ->constrained('users')
        ->cascadeOnDelete();

    $table->string('title');
    $table->text('description');
    $table->enum('difficulty', ['easy', 'medium', 'hard']);
    $table->integer('working_hours');
    $table->decimal('payment', 10, 2);
    $table->boolean('is_part_time')->default(true);

    $table->timestamps();
});

    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('jobs');
    }
};
