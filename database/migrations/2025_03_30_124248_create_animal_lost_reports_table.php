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
        Schema::create('animal_lost_reports', function (Blueprint $table) {
            $table->id();
            $table->foreignId('animal_id')->constrained('animals');
            $table->boolean('mark_as_public')->default(true);
            $table->date('seen_at')->nullable();
            $table->string('address')->nullable();
            $table->softDeletes();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('animal_lost_reports');
    }
};
