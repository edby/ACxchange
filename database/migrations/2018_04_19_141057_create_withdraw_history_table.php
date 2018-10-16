<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateWithdrawHistoryTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('withdraw_history', function (Blueprint $table) {
            $table->increments('id');
            $table->string('currency',12)->index()->default('');
            $table->string('address')->default('');
            $table->integer('user_id')->index()->default(0);
            $table->decimal('amount',20,8)->default(0);
            $table->decimal('max_fee',20,8)->default(0);
            $table->decimal('btc_amount',20,8)->default(0);
            $table->tinyInteger('status')->default(0);
            $table->string('token',191)->default('')->index();
            $table->string('sign')->default('');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('withdraw_history');
    }
}
