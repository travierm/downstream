<?php
namespace App\Services;

use DB;
class Filter {
    public static function title($string)
    {

        $filters = DB::table('title_filters')->pluck('value');

        foreach($filters as $filter) {
            $string = str_replace($filter, "", $string);
        }

        return htmlspecialchars_decode($string, ENT_QUOTES);
    }
}

?>