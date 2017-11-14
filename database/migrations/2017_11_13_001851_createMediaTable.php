<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateMediaTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
      Schema::create('media', function (Blueprint $table) {
          $table->increments('id');
          $table->string('origin');
          $table->string('type');
          $table->string('subtype');
          //Index for media's origin
          $table->string('index');
          $table->json('meta');
          //Original user_id that added media to library
          $table->integer('user_id')->references('id')->on('users');
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
      Schema::dropIfExists('media');
    }
}
