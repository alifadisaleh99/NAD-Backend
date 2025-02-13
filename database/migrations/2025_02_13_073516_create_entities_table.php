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
        Schema::create('entities', function (Blueprint $table) {
            $table->id();
            $table->string('address');
            $table->string('email');
            $table->string('contact_number');
            $table->date('founding_date');
            $table->float('price_per_pet')->default(0);
            $table->integer('allowed_branches')->default(0);
            $table->integer('allowed_users')->default(0);
            $table->integer('used_branches')->default(0);
            $table->integer('used_users')->default(0);
            $table->softDeletes();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('entities');
    }
};
