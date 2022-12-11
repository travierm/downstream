<?php

namespace App\MediaType\Transformer;

class SpotifyTrackTransformer
{
    public static function transform($raw)
    {
        $data = [];

        $data['spotify_id'] = $raw->id;
        $data['artist_name'] = @$raw->artists[0]->name;
        $data['title'] = trim($data['artist_name']).' - '.trim($raw->name);

        return $data;
    }
}
