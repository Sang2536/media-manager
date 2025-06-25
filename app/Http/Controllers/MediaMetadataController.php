<?php

namespace App\Http\Controllers;

use App\Models\MediaFile;
use App\Models\MediaMetadata;
use Illuminate\Http\Request;

class MediaMetadataController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $metadata = MediaMetadata::with('file')
            ->orderByDesc('created_at')
            ->latest()
            ->paginate(12);

        return view('media.metadata.index', compact('metadata'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $files = MediaFile::all()->pluck('filename', 'id');

        return view('media.metadata.create', compact('files'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }
}
