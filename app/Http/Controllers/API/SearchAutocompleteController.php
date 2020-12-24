<?php

namespace App\Http\Controllers\API;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use App\Http\Controllers\Controller;

class SearchAutocompleteController extends Controller
{
    private $apiUrl = "http://suggestqueries.google.com/complete/search?client=youtube&ds=yt&client=firefox&q=";

    public function getResults($query) 
    {
        $response = Http::get($this->apiUrl . $query);
        
        if($response->successful()) {
            $json = $response->json();

            // 0 is the original query, 1 is the query results from Google
            if($json[1]) {
                return response()->json([
                    'results' => $json[1]
                ], 200);
            }
        }

        return response()->json([
            'message' => 'Failed to fetch autocomplete data'
        ], 500);
    }
}
