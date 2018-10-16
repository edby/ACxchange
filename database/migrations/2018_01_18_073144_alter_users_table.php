<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AlterUsersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('users', function (Blueprint $table) {
            $table->ipAddress('login_ip')->comment('当前登陆Ip');
            $table->ipAddress('old_ip')->comment('上次登陆IP');
            $table->string('pin')->default('')->comment('支付密码');
            $table->timestamp('last_login')->comment('最后登陆时间');
            $table->string('first_name')->default('')->comment('姓');
            $table->string('last_name')->default('')->comment('名');
            $table->string('nationality')->default('')->comment('国籍');
            $table->timestamp('birthday')->comment('生日');
            $table->string('region_ode')->default('')->comment('国家区号');
            $table->string('phone')->default('')->comment('手机号码');
        });
    }
    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('users', function (Blueprint $table) {
            //
        });
    }
}
