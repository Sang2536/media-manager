<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class MediaLogController extends Controller
{
    public function index() {
        return view('media.logs.index');
    }
}
