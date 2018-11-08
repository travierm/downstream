<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateGlobalQueueTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('global_queue', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('active')->default(0);
            $table->integer('media_id')->references('id')->on('media');
            $table->integer('user_id')->references('id')->on('users');
            $table->dateTime('active_at')->nullable();
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
        Schema::dropIfExists('global_queue');
    }
}
