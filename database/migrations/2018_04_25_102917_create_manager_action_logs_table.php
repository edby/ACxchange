<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateManagerActionLogsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('manager_action_logs', function (Blueprint $table) {
            $table->increments('id');
            $table->tinyInteger('type')->index();
            $table->integer('author_id')->index();
            $table->string('author_name');
            $table->string('ip_address');
            $table->string('action',500);
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
        Schema::dropIfExists('manager_action_logs');
    }
}
