<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
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
        $duplicateMediaIndexes = [
            'v-zdjCxnNpE',
            'BU_sKzSbTJk',
            'Qhnaop1cuBE',
            'ftMnmPC82qM',
        ];

        foreach ($duplicateMediaIndexes as $index) {
            DB::statement("DELETE FROM media WHERE `index` = '{$index}' ORDER BY created_at ASC LIMIT 1");
        }

        Schema::table('media', function ($table) {
            $table->unique('index');
            $table->index('title');
            $table->index('user_id');
            $table->index('spotify_id');
        });

        Schema::table('media_meta', function ($table) {
            $table->primary('media_id');
            $table->index('title');
            $table->index('album_id');
            $table->index('artist_id');
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
        /*Schema::table('media', function($table) {
            $table->dropUnique('index');
            $table->dropIndex('title');
            $table->dropIndex('user_id');
            $table->dropIndex('spotify_id');
        });

        Schema::table('media_meta', function($table) {
            $table->dropPrimary('media_id');
            $table->dropIndex('title');
            $table->dropIndex('album_id');
            $table->dropIndex('artist_id');
            $table->dropIndex('spotify_id');
        });*/
    }
}
