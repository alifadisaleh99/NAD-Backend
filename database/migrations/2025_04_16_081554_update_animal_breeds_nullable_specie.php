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
        Schema::table('animal_breeds', function (Blueprint $table) {
            $table->dropForeign(['animal_specie_id']);

            $table->unsignedBigInteger('animal_specie_id')->nullable()->change();
 
            $table->foreign('animal_specie_id')->references('id')->on('animal_species'); 
        });

    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        $hasNullBreedSpecie = DB::table('animal_breeds')->whereNull('animal_specie_id')->exists();

        if ($hasNullBreedSpecie) {
            throw new \Exception("Can't rollback: There are NULL values in 'animal_specie_id'. Please fix the data before rolling back.");
        }
        
        Schema::table('animal_breeds', function (Blueprint $table) {
            $table->dropForeign(['animal_specie_id']);

            $table->unsignedBigInteger('animal_specie_id')->nullable(false)->change();

            $table->foreign('animal_specie_id')->references('id')->on('animal_species'); 
        });
    }
};
