<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\MediaFileResource;
use App\Models\MediaFile;
use App\Services\MediaLogService;
use Illuminate\Http\Request;

class MediaFileController extends Controller
{
    public function index(Request $request)
    {
        try {
            $files = MediaFile::with(['folder', 'tag', 'metadata'])
                ->where('user_id', $request->user()->id)
                ->whereNull('deleted_at')
                ->paginate(20);

            return MediaFileResource::collection($files);
        } catch (\Exception $e) {
            MediaLogService::custom(
                'Get Api File Error',
                'Media File',
                $request->user()->id ?? null,
                'Failed to fetch media files. Error: ' . $e->getMessage(),
                [
                    'log_type' => 'system:api',
                ]
            );

            return response()->json([
                'message' => 'Failed to fetch media files.',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    public function show(Request $request, $fileId)
    {
        try {
            $file = MediaFile::with(['folder', 'tag', 'metadata'])
                ->where('user_id', $request->user()->id)
                ->findOrFail($fileId);

            return new MediaFileResource($file);
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            MediaLogService::custom(
                'Get Api File Error',
                'Media File',
                $request->user()->id ?? null,
                'Media file not found. Error: ' . $e->getMessage(),
                [
                    'log_type' => 'system:api',
                ]
            );

            return response()->json([
                'message' => 'Media file not found.',
            ], 404);
        } catch (\Exception $e) {
            MediaLogService::custom(
                'Get Api File Error',
                'Media File',
                $request->user()->id ?? null,
                'Failed to fetch media file. Error: ' . $e->getMessage(),
                [
                    'log_type' => 'system:api',
                ]
            );

            return response()->json([
                'message' => 'Failed to fetch media file.',
                'error' => $e->getMessage(),
            ], 500);
        }
    }
}
