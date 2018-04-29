<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateTableMediaTempItem extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('media_temp_items', function (Blueprint $table) {
          $table->increments('id');
          $table->string('source');
          $table->string('source_id');
          $table->string('title', 300);
          $table->string('index', 150)->unqiue();
          $table->string('thumbnail', 500);
          $table->integer('visible')->default(0);
          $table->json('meta')->nullable();;
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
        Schema::dropIfExists('media_temp_items');
    }
}
