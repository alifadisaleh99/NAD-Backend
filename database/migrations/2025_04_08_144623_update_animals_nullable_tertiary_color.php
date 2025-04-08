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
            $table->dropForeign(['tertiary_color_id']);

            $table->unsignedBigInteger('tertiary_color_id')->nullable()->change();
 
            $table->foreign('tertiary_color_id')->references('id')->on('colors'); 
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        $hasNullTertiary = DB::table('animals')->whereNull('tertiary_color_id')->exists();

        if ($hasNullTertiary) {
            throw new \Exception("Can't rollback: There are NULL values in 'tertiary_color_id'. Please fix the data before rolling back.");
        }
        
        Schema::table('animals', function (Blueprint $table) {
            $table->dropForeign(['tertiary_color_id']);

            $table->unsignedBigInteger('tertiary_color_id')->nullable(false)->change();

            $table->foreign('tertiary_color_id')->references('id')->on('colors'); 
        });
    }
    };
