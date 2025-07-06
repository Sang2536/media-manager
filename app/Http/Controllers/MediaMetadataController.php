<?php

namespace App\Http\Controllers;

use App\Helpers\MediaFileHelper;
use App\Helpers\ResponseHelper;
use App\Http\Requests\MediaMetadataRequest;
use App\Models\MediaFile;
use App\Models\MediaMetadata;
use Illuminate\Http\Request;
use Illuminate\Support\Arr;

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
        $formAttr = [
            'route'  => route('media-metadata.store'),
            'action' => 'create',
        ];

        $files = MediaFile::all();

        return view('media.metadata.form', compact('formAttr','files'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(MediaMetadataRequest $request)
    {
        try {
            $meta = Arr::get($request->validated(), 'metadata');

            foreach ($meta as $item) {
                MediaMetadata::create([
                    'media_file_id' => $request->input('media_file_id'),
                    'key'           => $item['key'],
                    'value'         => $item['value'],
                ]);
            }

            return ResponseHelper::result(true, '✅ Metadata đã được thêm thành công.', 200, route('media-metadata.index'));
        } catch (\Throwable $e) {
            return ResponseHelper::result(false, 'Lỗi cập nhật: ' . $e->getMessage(), 400);
        }
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
    public function edit(MediaMetadata $metadata)
    {
        $formAttr = [
            'route'  => route('media-metadata.update', $metadata),
            'action' => 'edit',
        ];

        $files = MediaFile::with(['folder', 'user'])->get();

        return view('media.metadata.form', compact('formAttr', 'metadata', 'files'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(MediaMetadataRequest $request, MediaMetadata $metadata)
    {
        try {
            $meta = Arr::get($request->validated(), 'metadata');

            foreach ($meta as $item) {
                $metadata->update([
                    'media_file_id' => $request->input('media_file_id'),
                    'key'           => $item['key'],
                    'value'         => $item['value'],
                ]);
            }

            return ResponseHelper::result(true, '✅ Metadata đã được cập nhật thành công.', 200, route('media-metadata.index'));
        } catch (\Throwable $e) {
            return ResponseHelper::result(false, 'Lỗi cập nhật: ' . $e->getMessage(), 400);
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(MediaMetadata $metadata)
    {
        try {
            $metadata->delete();

            return ResponseHelper::result(true, '✅ Metadata đã được xóa thành công.', 200, route('media-metadata.index'));
        } catch (\Throwable $e) {
            return ResponseHelper::result(false, 'Lỗi cập nhật: ' . $e->getMessage(), 400);
        }
    }
}
