<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddSpotifyIDColumn extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        if(!Schema::hasColumn('media_meta', 'spotify_id')) {
            Schema::table('media_meta', function (Blueprint $table) {
                $table->string('spotify_id')->nullable()->after("raw");
            });
        }
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('media_meta', function (Blueprint $table) {
            $table->dropColumn('spotify_id');
        });
    }
}
