<?php

use App\Enums\ActiveStatusEnum;
use App\Enums\GenderEnum;
use App\Enums\RoleEnum;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('corporation_users', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('email')->unique();
            $table->timestamp('email_verified_at')->nullable();
            $table->string('password');
            $table->enum('role', array_column(RoleEnum::cases(), 'value'))->default(RoleEnum::DC->value);
            $table->string('profile')->nullable();
            $table->string('phone')->nullable();
            $table->foreignId('corporation_id')->constrained('corporations')->onDelete('cascade');
            $table->string('city')->nullable();
            $table->enum('gender', array_column(GenderEnum::cases(), 'value'))->nullable();
            $table->string('date_of_birth')->nullable();
            $table->enum('status', array_column(ActiveStatusEnum::cases(), 'value'))
                ->default(ActiveStatusEnum::INACTIVE->value);
            $table->string('storage_path')->nullable();
            $table->rememberToken();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('corporation_users');
    }
};
