<?php
namespace App\Media;

use DB;;
use App\User;
use App\UserMedia;
use App\Media;
use App\Media\Youtube;
use YouTubeService;

interface MediaType
{
    public function getById($id);
}

class Item {
    public $types = ["youtube"];

    public function __construct($mediaType, $sourceId)
    {
        if(!in_array($mediaType, $this->type)) {
            throw new Exception("$mediaType is not a valid media type");
        }

    }
}