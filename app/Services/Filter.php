<?php

namespace App\Services;

use DB;

class Filter
{
    public static function title(string $string, ?iterable $filters = null): string
    {
        $string = html_entity_decode(htmlspecialchars_decode($string, ENT_QUOTES), ENT_QUOTES | ENT_HTML5, 'UTF-8');

        // Only remove known promotional annotations; preserve remix, live, and featured-artist labels.
        $label = '(?:official|music\s+video|lyrics?\s+video|audio|video|lyrics?|visuali[sz]er|full[\s-]*screen|hd|hq|4k|720p|1080p|2160p)';
        $annotation = $label.'(?:[\s\p{Pd}/|,&+]+'.$label.')*';
        $string = preg_replace('~\[\s*'.$annotation.'\s*\]|\(\s*'.$annotation.'\s*\)~iu', ' ', $string);

        $filters ??= DB::table('title_filters')->pluck('value');

        foreach ($filters as $filter) {
            $string = str_replace($filter, '', $string);
        }

        return trim(preg_replace('/\s+/u', ' ', $string));
    }
}
