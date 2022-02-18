<?php
namespace App\Services;

use App\MediaType\YoutubeVideo;
use App\Models\Media;
use App\Models\UserMedia;
use Madcoda\Youtube\Facades\Youtube;

function get_error($ErrorExaction)
{
    $myObj = new \stdClass();
    $myObj->error = true;
    $myObj->msg = $ErrorExaction;

    $myObj->madeBy = "A.El-zahaby";
    $myObj->instagram = "egy.js";
    $myJSON = json_encode($myObj, JSON_PRETTY_PRINT);
    echo $myJSON;
    exit;
}

function get_log($dump)
{
    if (isset($_GET['log'])) {
        var_dump($dump) . '\n\n\n';
    }

}

function getVideoStream($videoId)
{
    $vars = [];

    if ($videoId) {
        // parse_str(parse_url($_GET['url'], PHP_URL_QUERY), $vars);
        $id = $videoId;

        $dt = file_get_contents("https://www.youtube.com/get_video_info?video_id=$id&el=embedded&ps=default&eurl=&gl=US&hl=en");
        if (strpos($dt, 'status=fail') !== false) {

            $x = explode("&", $dt);
            $t = array();
            $g = array();
            $h = array();

            foreach ($x as $r) {
                $c = explode("=", $r);
                $n = $c[0];
                $v = $c[1];
                $y = urldecode($v);
                $t[$n] = $v;
            }

            $x = explode("&", $dt);
            foreach ($x as $r) {
                $c = explode("=", $r);
                $n = $c[0];
                $v = $c[1];
                $h[$n] = urldecode($v);
            }
            $g[] = $h;
            $g[0]['error'] = true;
            $g[0]['instagram'] = "egy.js";
            $g[0]['apiMadeBy'] = 'El-zahaby';
            echo json_encode($g, JSON_PRETTY_PRINT);

        } else {

            $x = explode("&", $dt);
            $t = array();
            $g = array();
            $h = array();

            foreach ($x as $r) {
                $c = explode("=", $r);
                $n = $c[0];
                $v = $c[1];
                $y = urldecode($v);
                $t[$n] = $v;
            }
            $streams = explode(',', urldecode($t['url_encoded_fmt_stream_map']));
//        if(empty($streams[0])){ get_error('ops! this video has something wrong! :( '); }
            if (empty($streams[0])) {
                get_log($streams);
            }
            foreach ($streams as $dt) {
                $x = explode("&", $dt);
                foreach ($x as $r) {
                    $c = explode("=", $r);
                    if ($c[0] == 'itag') { // reference:  https://superuser.com/q/1386658
                        switch ($c[1]) {
                            case '18':
                                $h['mimeType'] = "mp4";
                                $h['width'] = "640";
                                $h['height'] = "360";
                                $h['qualityLabel'] = '360p';
                                break;
                            case '22':
                                $h['mimeType'] = "mp4";
                                $h['width'] = "1280";
                                $h['height'] = "720";
                                $h['qualityLabel'] = '720p';
                                break;
                            case '43':
                                $h['mimeType'] = "webm";
                                $h['width'] = "640";
                                $h['height'] = "360";
                                $h['qualityLabel'] = '360p';
                                break;
                            default:
                                $h['mimeType'] = null;
                                $h['width'] = null;
                                $h['height'] = null;
                                $h['qualityLabel'] = '';
                        }
                    }
                    $n = $c[0]; /* => */$v = $c[1];
                    $h[$n] = urldecode($v);

                }
                $g[] = $h;
            }
            echo json_encode($g, JSON_PRETTY_PRINT);
            // var_dump( $g[1]["quality"],true);
        }
    } else {

        get_error("Ops, there is no youtube link!");

    }

}

class YoutubeService
{
    public static function getVideoById($id)
    {
        $result = Youtube::getVideoInfo($id);

        getVideoStream($id);

        return YoutubeVideo::createFromSearchResult($result);
    }

    public static function updateMediaIdOnVideos($videos)
    {
        $videos = collect($videos);
        $matchedMediaItems = Media::whereIn('index', $videos->pluck('videoId'))->get();

        $updatedVideos = [];
        foreach ($videos as $video) {
            foreach ($matchedMediaItems as $media) {
                if ($media->index == $video->videoId) {
                    $video->mediaId = $media->id;
                }
            }

            $updatedVideos[] = $video;
        }

        return $updatedVideos;
    }

    public static function updateCollectedOnVideos($videos, $userId)
    {
        $collectedMediaIds = UserMedia::where('user_id', $userId)->pluck('media_id');
        $collectedVideoIndexes = Media::whereIn('id', $collectedMediaIds)->pluck('index')->all();

        $updatedVideos = [];
        foreach ($videos as $video) {
            $video->collected = in_array($video->videoId, $collectedVideoIndexes);

            $updatedVideos[] = $video;
        }

        return $updatedVideos;
    }

    public static function searchByQuery($query, $maxResults = 12)
    {
        $results = Youtube::searchAdvanced([
            'q' => $query,
            'type' => 'video',
            'part' => 'id,snippet',
            'maxResults' => $maxResults,
            // 'videoCategoryId' => 10, // Search for music only
        ]);

        if (count($results) <= 0) {
            return [];
        }

        $videos = [];
        foreach ($results as $raw) {
            $video = YoutubeVideo::createFromSearchResult($raw);
            $videos[] = $video;
        }

        return $videos;
    }
}
