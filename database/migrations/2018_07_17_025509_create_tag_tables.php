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
        $table->increments("id");
        $table->string("name")->distinct();
        $table->timestamps();
    });

    Schema::create('media_tags', function (Blueprint $table) {
        $table->increments("id");
        $table->integer("media_id")->references('id')->on("media");
        $table->integer("tag_id")->references('id')->on("tags");
    });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('tags');
        Schema::dropIfExists('media_tags');
    }
}
