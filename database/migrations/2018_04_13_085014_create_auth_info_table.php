<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateAuthInfoTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('auth_info', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('user_id')->index()->comment('用户ID');
            $table->tinyInteger('auth_type')->comment('验证类型 1:Google 2:Authy 3:SMS');
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
        Schema::dropIfExists('auth_info');
    }
}
