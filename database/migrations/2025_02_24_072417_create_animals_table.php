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
        Schema::create('animals', function (Blueprint $table) {
            $table->id();
            $table->string('owner_type');
            $table->foreignId('user_id')->nullable()->constrained('users');
            $table->foreignId('entity_id')->nullable()->constrained('entities');
            $table->foreignId('branch_id')->nullable()->constrained('branches');
            $table->boolean('status')->default(true);
            $table->string('link')->nullable();
            $table->foreignId('category_id')->constrained('categories');
            $table->foreignId('animal_type_id')->constrained('animal_types');
            $table->foreignId('animal_specie_id')->constrained('animal_species');
            $table->foreignId('animal_breed_id')->constrained('animal_breeds');
            $table->foreignId('primary_color_id')->constrained('colors');
            $table->string('primary_color');
            $table->foreignId('secondary_color_id')->constrained('colors');
            $table->string('secondary_color');
            $table->foreignId('tertiary_color_id')->constrained('colors');
            $table->string('tertiary_color');
            $table->enum('age', ['young', 'adult', 'senior']);
            $table->enum('gender', ['male', 'female']);
            $table->enum('size', ['small', 'medium', 'large', 'extra_large']);

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('animals');
    }
};
