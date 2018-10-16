<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateKYCRejectReasonsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('kyc_reject_reasons', function (Blueprint $table) {
            $table->increments('id');
            $table->string('reason_en',500)->default('');
            $table->string('reason_cn',500)->default('');
            $table->string('reason_tw',500)->default('');
            $table->tinyInteger('status')->default(1);
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
        Schema::dropIfExists('kyc_reject_reasons');
    }
}
