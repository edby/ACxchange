<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateSmsAuthTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('sms_auth', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('user_id')->index()->comment('用户ID');
            $table->char('phone_number',11)->comment('手机号');
            $table->char('country_code',6)->comment('国号');
            $table->tinyInteger('verified')->comment('是否开通 1是 0否');
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
        Schema::dropIfExists('sms_auth');
    }
}
