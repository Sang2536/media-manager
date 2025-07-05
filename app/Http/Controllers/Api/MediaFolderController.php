<?php

namespace App\Http\Controllers\Api;

use App\Enums\StatusEnum;
use App\Http\Controllers\Controller;
use App\Http\Resources\MediaFolderResource;
use App\Models\MediaFolder;
use App\Services\MediaLogService;
use Illuminate\Http\Request;

class MediaFolderController extends Controller
{
    public function index(Request $request)
    {
        try {
            $folders = MediaFolder::withCount('files')
                ->with(['children', 'parent'])
                ->where('user_id', $request->user()->id)
                ->whereNull('deleted_at')
                ->paginate(20);

            return MediaFolderResource::collection($folders);
        } catch (\Exception $e) {
            MediaLogService::custom(
                'Get Api Error',
                'Media Folder',
                $request->user()->id ?? null,
                StatusEnum::ERROR->value,
                'system:api',
                'Failed to fetch folders. Error: ' . $e->getMessage(),
                [
                    'log_type' => 'system:api',
                ]
            );

            return response()->json([
                'message' => 'Failed to fetch folders.',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    public function show(Request $request, $folderId)
    {
        try {
            $folder = MediaFolder::with(['children', 'parent', 'files'])
                ->withCount('files')
                ->where('user_id', $request->user()->id)
                ->findOrFail($folderId);

            return new MediaFolderResource($folder);
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            MediaLogService::custom(
                'Get Api Error',
                'Media Folder',
                $request->user()->id ?? null,
                StatusEnum::ERROR->value,
                'system:api',
                'Folder not found. Error: ' . $e->getMessage(),
                [
                    'log_type' => 'system:api',
                ]
            );

            return response()->json([
                'message' => 'Folder not found.',
            ], 404);
        } catch (\Exception $e) {
            MediaLogService::custom(
                'Get Api Error',
                'Media Folder',
                $request->user()->id ?? null,
                StatusEnum::ERROR->value,
                'system:api',
                'Failed to fetch folder. Error: ' . $e->getMessage(),
                [
                    'log_type' => 'system:api',
                ]
            );

            return response()->json([
                'message' => 'Failed to fetch folder.',
                'error' => $e->getMessage(),
            ], 500);
        }
    }
}
