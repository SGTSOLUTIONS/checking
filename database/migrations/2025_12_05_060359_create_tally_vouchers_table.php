<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateTallyVouchersTable extends Migration
{
    public function up()
    {
        Schema::create('tally_vouchers', function (Blueprint $table) {
            $table->id();
            $table->string('tally_guid')->nullable()->unique();
            $table->string('voucher_number')->nullable();
            $table->string('voucher_type')->nullable();
            $table->date('date')->nullable();
            $table->string('party_name')->nullable();
            $table->text('narration')->nullable();
            $table->json('ledgers')->nullable(); // store ledger entries as JSON
            $table->text('raw_xml')->nullable();
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('tally_vouchers');
    }
}
