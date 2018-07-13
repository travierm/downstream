<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreatePlaylistTables extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('playlists', function (Blueprint $table) {
            $table->increments('id');
            $table->string('name');
            $table->integer('created_by')->references('id')->on('users');
            $table->boolean('private');
            $table->timestamps();
        });

        Schema::create('playlist_items', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('playlist_id')->references('id')->on('playlists');
            $table->integer('media_id')->references('id')->on('media');
            $table->integer('created_by')->references('id')->on('users');
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
        Schema::dropIfExists('playlists');
        Schema::dropIfExists('playlist_items');
    }
}
