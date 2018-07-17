<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateTagTables extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
    Schema::create('tags', function (Blueprint $table) {
        $table->increments("tag_id");
        $table->string("name")->distinct();
    });

    Schema::create('media_tags', function (Blueprint $table) {
        $table->increments("id");
        $table->foreign("media_id")->references('id')->on('media');
        $table->foreign("tag_id")->references('tag_id')->on("tags");
    });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        //
    }
}
