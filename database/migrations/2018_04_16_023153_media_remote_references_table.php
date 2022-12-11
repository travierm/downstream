<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class MediaRemoteReferencesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('media_remote_references', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('media_id')->references('id')->on('media');
            $table->string('source');
            $table->string('source_id');
            $table->string('index', 150)->unqiue();
            $table->string('title', 300);
            $table->string('thumbnail', 500);
            $table->softDeletes();
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
        Schema::dropIfExists('media_remote_references');
    }
}
