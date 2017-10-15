<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateYoutubeVideoTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
      Schema::create('youtube_videos', function (Blueprint $table) {
          $table->increments('id');
          $table->integer('user_id');
          $table->string('vid');
          $table->string('title');
          $table->text('description')->nullable();
          $table->json('tags')->nullable();
          $table->bigInteger('view_count')->nullable();
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
      Schema::dropIfExists('youtube_videos');
    }
}
