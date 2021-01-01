<?php
namespace App\Services;

use Cache;

class Filter {
    public static function title($string)
    {
        $filters = Cache::get('filters.title', []);

        foreach($filters as $filter) {
            $string = str_replace($filter, "", $string);
        }

        return htmlspecialchars_decode($string, ENT_QUOTES);
    }
}

?>