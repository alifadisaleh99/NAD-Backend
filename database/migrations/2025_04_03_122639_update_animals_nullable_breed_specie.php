<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('animals', function (Blueprint $table) {
            $table->dropForeign(['animal_specie_id']);
            $table->dropForeign(['animal_breed_id']);

            $table->unsignedBigInteger('animal_specie_id')->nullable()->change();
            $table->unsignedBigInteger('animal_breed_id')->nullable()->change();
 
            $table->foreign('animal_specie_id')->references('id')->on('animal_species'); 
            $table->foreign('animal_breed_id')->references('id')->on('animal_breeds');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        $hasNullSpecie = DB::table('animals')->whereNull('animal_specie_id')->exists();
        $hasNullBreed = DB::table('animals')->whereNull('animal_breed_id')->exists();

        if ($hasNullSpecie || $hasNullBreed) {
            throw new \Exception("Can't rollback: There are NULL values in 'animal_specie_id' or 'animal_breed_id'. Please fix the data before rolling back.");
        }
        
        Schema::table('animals', function (Blueprint $table) {
            $table->dropForeign(['animal_specie_id']);
            $table->dropForeign(['animal_breed_id']);

            $table->unsignedBigInteger('animal_specie_id')->nullable(false)->change();
            $table->unsignedBigInteger('animal_breed_id')->nullable(false)->change();

            $table->foreign('animal_specie_id')->references('id')->on('animal_species'); 
            $table->foreign('animal_breed_id')->references('id')->on('animal_breeds');
        });
    }
};
