<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddMediaTableIndexes extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('media', function($table) {
            $table->unique('index');
            $table->index('title');
            $table->index('user_id');
            $table->index('spotify_id');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('media', function($table) {
            $table->dropUnique('index');
            $table->dropIndex('title');
            $table->dropIndex('user_id');
            $table->dropIndex('spotify_id');
        });
    }
}
