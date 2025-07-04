<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\UserResource;
use App\Models\User;
use Illuminate\Http\Request;
use App\Services\MediaLogService;

class UserController extends Controller
{
    public function show(Request $request)
    {
        try {
            $user = User::with(['files', 'folders'])
                ->withCount(['files', 'folders'])
                ->where('id', $request->user()->id)
                ->first();

            if (!$user) {
                MediaLogService::custom(
                    'Get Api User Error',
                    'User',
                    $request->user()->id ?? null,
                    'User not found.',
                    [
                        'log_type' => 'system:api',
                    ]
                );

                return response()->json([
                    'message' => 'User not found.'
                ], 404);
            }

            return new UserResource($user);

        } catch (\Exception $e) {
            MediaLogService::custom(
                'Get Api User Error',
                'User',
                $request->user()->id ?? null,
                'Something went wrong. Error: ' . $e->getMessage(),
                [
                    'log_type' => 'system:api',
                ]
            );

            return response()->json([
                'message' => 'Something went wrong.',
                'error' => $e->getMessage(),
            ], 500);
        }
    }
}
