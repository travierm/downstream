<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class ArtistController extends Controller
{
    public function getIndex()
    {
        return view('artists.index');
    }
}
