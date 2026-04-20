<?php

use App\Enums\ActiveStatusEnum;
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
        Schema::create('teams', function (Blueprint $table) {
            $table->id();
            $table->foreignId('ward_id')->constrained('wards')->onDelete('cascade');
            $table->string('name');
            $table->string('leader_name');
             $table->foreignId('team_leader_id')
                  ->nullable()
                  ->constrained('users')
                  ->onDelete('set null');
            
            $table->string('contact_number');
            $table->enum('status', array_column(ActiveStatusEnum::cases(), 'value'))
                  ->default(ActiveStatusEnum::ACTIVE->value);
            $table->timestamps();
             $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('teams');
    }
};
