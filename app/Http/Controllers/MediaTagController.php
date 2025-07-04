<?php

namespace App\Http\Controllers;

use App\Helpers\ResponseHelper;
use App\Http\Requests\MediaTagRequest;
use App\Models\MediaTag;

class MediaTagController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $tags = MediaTag::all();

        return view('media.tags.index', compact('tags'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $action = 'create';
        $route = route('media-tags.store');

        return view('media.tags.form-modal', compact('action', 'route'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(MediaTagRequest $request)
    {
        try {
            $names = explode(',', $request->input('names'));

            foreach ($names as $name) {
                $trimmed = trim($name);

                if (!empty($trimmed)) {
                    MediaTag::firstOrCreate(
                        ['name' => $trimmed],
                        ['slug' => str($trimmed)->slug()],
//                        'color' => $request->input('color'),
                    );
                }
            }

            return ResponseHelper::result(
                true,
                '📤 Các tag đã được tạo thành công',
                200,
                route('media-tags.index')
            );
        } catch (\Throwable $e) {
            return ResponseHelper::result(
                false,
                '❌ Lỗi khi tạo tag: ' . $e->getMessage(),
                400
            );
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
    public function edit(string $id)
    {
        $tag = MediaTag::where('id', $id)->first();
        $action = 'edit';
        $route = route('media-tags.update', $tag);

        return view('media.tags.form-modal', compact('tag','action', 'route'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(MediaTagRequest $request, MediaTag $tag)
    {
        try {
            $data = $request->validated();
            $data['slug'] = str($data['name'])->slug();

            $tag->update($data);

            return ResponseHelper::result(
                true,
                '✅ Tag đã được cập nhật thành công',
                200,
                route('media-tags.index')
            );
        } catch (\Throwable $e) {
            return ResponseHelper::result(
                false,
                '❌ Lỗi khi cập nhật tag: ' . $e->getMessage(),
                400
            );
        }
    }


    /**
     * Remove the specified resource from storage.
     */
    public function destroy(MediaTag $tag)
    {
        try {
            $tag->delete();

            return ResponseHelper::result(
                true,
                '🗑️ Tag đã được xoá thành công',
                200,
                route('media-tags.index')
            );
        } catch (\Throwable $e) {
            return ResponseHelper::result(
                false,
                '❌ Lỗi khi xoá tag: ' . $e->getMessage(),
                400
            );
        }
    }
}
