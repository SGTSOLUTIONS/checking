<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
   public function up()
{
    Schema::table('wards', function (Blueprint $table) {
        $table->double('extent_left')->change();
        $table->double('extent_right')->change();
        $table->double('extent_top')->change();
        $table->double('extent_bottom')->change();
    });
}

public function down()
{
    Schema::table('wards', function (Blueprint $table) {
        $table->decimal('extent_left', 10, 6)->change();
        $table->decimal('extent_right', 10, 6)->change();
        $table->decimal('extent_top', 10, 6)->change();
        $table->decimal('extent_bottom', 10, 6)->change();
    });
}

};
