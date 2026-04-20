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
        Schema::create('corporations', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('code')->unique();
            $table->string('district');
            $table->string('state');
            $table->string('logo')->nullable();
            $table->json('boundary')->nullable();
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
        Schema::dropIfExists('corporations');
    }
};
