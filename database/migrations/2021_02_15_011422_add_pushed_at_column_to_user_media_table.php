<?php

use App\Models\UserMedia;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddPushedAtColumnToUserMediaTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('user_media', function (Blueprint $table) {
            $table->timestamp('pushed_at')->nullable()->after('media_id');
        });

        $items = UserMedia::withTrashed()->get();

        foreach ($items as $item) {
            $item->pushed_at = $item->created_at;
            $item->save();
        }
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('user_media', function (Blueprint $table) {
            $table->dropColumn('pushed_at');
        });
    }
}
