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
        Schema::create('surveyor_ward_accesses', function (Blueprint $table) {
             $table->id();
            $table->foreignId('surveyor_id')->constrained('users')->onDelete('cascade');
            $table->foreignId('ward_id')->constrained('wards')->onDelete('cascade');
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('surveyor_ward_accesses');
    }
};
