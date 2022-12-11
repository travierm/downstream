<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class AddPlaylistItemsSoftDeletes extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        DB::statement('
            ALTER TABLE `playlists` 
            CHANGE COLUMN `private` `private` TINYINT(1) NOT NULL DEFAULT 0 ;
        ');

        Schema::table('playlist_items', function (Blueprint $table) {
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('playlist_items', function (Blueprint $table) {
            $table->dropSoftDeletes();
        });
    }
}
