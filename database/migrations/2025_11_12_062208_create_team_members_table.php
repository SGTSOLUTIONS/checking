<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('team_members', function (Blueprint $table) {
            $table->id();
            $table->foreignId('team_id')->constrained()->onDelete('cascade');
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->enum('role', ['surveyor', 'assistant'])->default('surveyor');
            $table->enum('status', ['active', 'inactive'])->default('active');
            $table->timestamps();
            
            // Prevent duplicate members in same team
            $table->unique(['team_id', 'user_id']);
        });
    }

    public function down()
    {
        Schema::dropIfExists('team_members');
    }
};