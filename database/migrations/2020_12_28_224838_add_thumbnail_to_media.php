<?php

use App\Models\Media;
use App\Services\Filter;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddThumbnailToMedia extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('media', function (Blueprint $table) {
            $table->string('title')->nullable()->after('index');
            $table->string('thumbnail')->nullable()->after('title');
        });

        // update all media to use thumbnail from media_meta
        $items = Media::all();

        foreach ($items as $item) {
            $meta = $item->getMeta();

            if ($meta && $meta->thumbnail) {
                $item->title = Filter::title($meta->title);
                $item->thumbnail = $meta->thumbnail;
                $item->save();
            }
        }
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('media', function (Blueprint $table) {
            $table->dropColumn('title');
            $table->dropColumn('thumbnail');
        });
    }
}
