<?php

namespace App\Http\Controllers\Api;

use App\Enums\StatusEnum;
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
                    'Get Api Error',
                    'User',
                    $request->user()->id ?? null,
                    StatusEnum::FAILED->value,
                    'api:crud:not_found',
                    'User not found. ',
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
                'Get Api Error',
                'User',
                $request->user()->id ?? null,
                StatusEnum::ERROR->value,
                'system:api',
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
