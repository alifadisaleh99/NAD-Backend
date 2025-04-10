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
            $table->float('weight')->nullable();
            $table->enum('size', ['small', 'medium', 'large', 'extra_large'])->nullable()->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        DB::table('animals')->whereNull('size')->update(['size' => 'medium']);  

        Schema::table('animals', function (Blueprint $table) {
            $table->dropColumn('weight');
            $table->enum('size', ['small', 'medium', 'large', 'extra_large'])->change();
        });
    }
};
