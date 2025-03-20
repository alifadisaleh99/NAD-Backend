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
        Schema::table('animals', function (Blueprint $table) {
            $table->dropConstrainedForeignId('entity_id');
            $table->dropConstrainedForeignId('branch_id');

            $table->unsignedBigInteger('user_id')->nullable(false)->change();           

        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('animals', function (Blueprint $table) {
            $table->foreignId('entity_id')->nullable()->constrained('entities');
            $table->foreignId('branch_id')->nullable()->constrained('branches');
            $table->unsignedBigInteger('user_id')->nullable()->change();        });
    }
};
