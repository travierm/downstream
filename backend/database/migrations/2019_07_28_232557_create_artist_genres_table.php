<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateArtistGenresTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('artist_genres', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->integer('artist_id')->unsigned();
            $table->string("name");
        });

        
        Schema::table('artists', function (Blueprint $table) {
            $table->string('spotify_id')->nullable()->after('name');
        });

    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('artist_genres');
        Schema::table('artists', function (Blueprint $table) {
            $table->dropColumn('spotify_id');
        });
    }
}
