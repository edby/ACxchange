<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateWithdrawHistoryNodeTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('withdraw_history_node', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('user_id')->default(0)->index();
            $table->string('category',20)->default('');
            $table->string('amount',64)->default('');
            $table->string('fee',64)->default('');
            $table->string('txid',192)->default('');
            $table->string('address',64)->default('');
            $table->integer('time');
            $table->integer('timereceived');
            $table->string('currency',12)->default('');
            $table->unsignedSmallInteger('confirmations')->default(0);
            $table->unsignedSmallInteger('currency_id')->default(0);
            $table->tinyInteger('status')->default(0);
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
        Schema::dropIfExists('withdraw_history_node');
    }
}
