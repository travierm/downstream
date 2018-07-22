<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateMediaMeta extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('artists', function (Blueprint $table) {
            $table->increments("id");
            $table->string("name");
            $table->timestamps();
        });
        
        Schema::create('albums', function (Blueprint $table) {
            $table->increments("id");
            $table->string("name");
        });

        Schema::create('media_meta', function (Blueprint $table) {
            $table->integer('media_id')->references('id')->on('media');
            $table->string("title");
            $table->integer('artist_id')->nullable();
            $table->integer('album_id')->nullable();
            $table->string("track_number")->nullable();
            $table->integer("cd_number")->nullable();
            $table->text("thumbnail")->nullable();
            $table->json("raw")->nullable();
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
        Schema::dropIfExists('media_meta');
        Schema::dropIfExists('artists');
        Schema::dropIfExists('albums');
    }
}
