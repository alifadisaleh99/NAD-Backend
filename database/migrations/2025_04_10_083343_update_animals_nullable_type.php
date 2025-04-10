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
            $table->dropForeign(['animal_type_id']);

            $table->unsignedBigInteger('animal_type_id')->nullable()->change();
 
            $table->foreign('animal_type_id')->references('id')->on('animal_types'); 
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {        $hasNullTertiary = DB::table('animals')->whereNull('animal_type_id')->exists();

        if ($hasNullTertiary) {
            throw new \Exception("Can't rollback: There are NULL values in 'animal_type_id'. Please fix the data before rolling back.");
        }
        
        Schema::table('animals', function (Blueprint $table) {
            $table->dropForeign(['animal_type_id']);

            $table->unsignedBigInteger('animal_type_id')->nullable(false)->change();

            $table->foreign('animal_type_id')->references('id')->on('animal_types'); 
        });
    }
};
