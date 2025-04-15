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
        Schema::table('entities', function (Blueprint $table) {
            $table->date('founding_date')->nullable()->change();
            $table->foreignId('phone_country_id')->nullable()->constrained('countries');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        $hasNullDate = DB::table('entities')->whereNull('founding_date')->exists();

        if ($hasNullDate) {
            throw new \Exception("Can't rollback: There are NULL values in 'founding_date'. Please fix the data before rolling back.");
        }

        Schema::table('entities', function (Blueprint $table) {
            $table->date('founding_date')->nullable(false)->change();
            $table->dropConstrainedForeignId('phone_country_id');
        });
    }
};
