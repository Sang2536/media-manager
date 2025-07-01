<?php

namespace App\Http\Controllers;

use App\Models\MediaLog;
use Illuminate\Http\Request;

class MediaLogController extends Controller
{
    public function index() {
        $logs = MediaLog::all();

        return view('media.logs.index', compact('logs'));
    }

    public function create() {
        //
    }

    public function store() {
        //
    }

    public function show() {
        //
    }

    public function edit() {
        //
    }

    public function update()
    {
        //
    }

    public function destroy()
    {
        //
    }
}
