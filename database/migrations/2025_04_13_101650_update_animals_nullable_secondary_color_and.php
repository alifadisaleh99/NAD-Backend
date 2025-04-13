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
            $table->dropForeign(['secondary_color_id']);

            $table->unsignedBigInteger('secondary_color_id')->nullable()->change();
 
            $table->foreign('secondary_color_id')->references('id')->on('colors'); 
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        $hasNullTertiary = DB::table('animals')->whereNull('secondary_color_id')->exists();

        if ($hasNullTertiary) {
            throw new \Exception("Can't rollback: There are NULL values in 'secondary_color_id'. Please fix the data before rolling back.");
        }
        
        Schema::table('animals', function (Blueprint $table) {
            $table->dropForeign(['secondary_color_id']);

            $table->unsignedBigInteger('secondary_color_id')->nullable(false)->change();

            $table->foreign('secondary_color_id')->references('id')->on('colors'); 
        });
    }
};
