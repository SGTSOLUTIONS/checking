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
        Schema::create('wards', function (Blueprint $table) {
            $table->id();
            $table->foreignId('corporation_id')->constrained('corporations')->onDelete('cascade');
            $table->string('ward_no');
            $table->string('drone_image')->nullable();
            $table->decimal('extent_left', 10, 6)->nullable();   // Minimum longitude (left)
            $table->decimal('extent_right', 10, 6)->nullable();  // Maximum longitude (right)
            $table->decimal('extent_top', 10, 6)->nullable();    // Maximum latitude (top)
            $table->decimal('extent_bottom', 10, 6)->nullable(); // Minimum latitude (bottom)
            $table->json('boundary')->nullable();
            $table->string('zone')->nullable(); // Zone name (e.g., East, West)
            $table->enum('status',array_column(ActiveStatusEnum::cases(), 'value'))
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
        Schema::dropIfExists('wards');
    }
};
