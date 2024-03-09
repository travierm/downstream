<?php

namespace App\Http\Controllers;

use App\Repos\UserMediaRepo;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\View;
use Mauricius\LaravelHtmx\Facades\HtmxResponse;

class CollectionController extends Controller
{
    public function __construct(private UserMediaRepo $userMediaRepo)
    {

    }

    public function getIndex(Request $request)
    {
        $userId = $request->user()->id;
        $collectionSlice = $this->userMediaRepo->getCollectionSlice($userId, 0, 16);

        return view('collection.index', [
            'items' => $collectionSlice
        ]);
    }

    public function getSlice(Request $request)
    {
        $userId = $request->user()->id;
        $start = $request->get('start', 0);
        $offset = $request->get('offset', 16);

        return HtmxResponse::renderFragment('collection.index', 'items', [
            'items' => $this->userMediaRepo->getCollectionSlice($userId, $start, $offset)
        ]);
    }
}
