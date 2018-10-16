<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateAuthyTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('authy', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('user_id')->index()->comment('用户ID');
            $table->char('country_code',6)->comment('国号');
            $table->char('phone_number',11)->comment('手机号');
            $table->text('authy_id')->comment('Authy ID 标识');
            $table->tinyInteger('verified')->comment('是否开通 1是 0否');
            $table->timestamps();
            $table->unique(['country_code', 'phone_number']);
            $table->string('email',120)->default('')->comment('用户Email');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('authy');
    }
}
