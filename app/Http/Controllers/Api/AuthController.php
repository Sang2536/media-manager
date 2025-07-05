<?php

namespace App\Http\Controllers\Api;

use App\Enums\StatusEnum;
use App\Http\Controllers\Controller;
use App\Http\Requests\UserRequest;
use App\Http\Resources\UserResource;
use App\Models\User;
use App\Services\MediaLogService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;

class AuthController extends Controller
{
    public function register(Request $request)
    {
        // Validation
        $validator = Validator::make($request->all(), UserRequest::rules(), UserRequest::messages());

        if ($validator->fails()) {
            MediaLogService::custom(
                'Register Error',
                'User',
                $request->user()->id ?? null,
                StatusEnum::FAILED->value,
                'api:crud:validator',
                'Validation errors. Errors: ' . $validator->errors()->messages(),
                [
                    'log_type' => 'system:api',
                ]
            );

            return response()->json([
                'success' => false,
                'message' => 'Validation errors',
                'errors' => $validator->errors()
            ], 422);
        }

        // Tạo user mới
        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password), // Hash password
        ]);

        // Tạo token cho user
        $token = $user->createToken('auth_token')->plainTextToken;

        return response()->json([
            'success' => true,
            'message' => 'User registered successfully',
            'data' => [
                'user' => new UserResource($user),
                'token' => $token,
                'token_type' => 'Bearer'
            ]
        ], 201);
    }

    public function login(Request $request)
    {
        // Validation
        $validator = Validator::make($request->all(), UserRequest::rules(), UserRequest::messages());

        if ($validator->fails()) {
            MediaLogService::custom(
                'Login Error',
                'User',
                $request->user()->id ?? null,
                StatusEnum::FAILED->value,
                'api:crud:validator',
                'Validation errors. Errors: ' . $validator->errors()->messages(),
                [
                    'log_type' => 'system:api',
                ]
            );

            return response()->json([
                'success' => false,
                'message' => 'Validation errors',
                'errors' => $validator->errors()
            ], 422);
        }

        // Tìm user
        $user = User::where('email', $request->email)->first();

        // Kiểm tra user và password
        if (!$user || !Hash::check($request->password, $user->password)) {
            MediaLogService::custom(
                'Login Error',
                'User',
                $request->user()->id ?? null,
                StatusEnum::FAILED->value,
                'api:crud:validator',
                'Validation errors. Errors: ' . $validator->errors()->messages(),
                [
                    'log_type' => 'system:api',
                ]
            );

            return response()->json([
                'success' => false,
                'message' => 'Invalid credentials'
            ], 401);
        }

        // Tạo token
        $token = $user->createToken('auth_token')->plainTextToken;

        return response()->json([
            'success' => true,
            'message' => 'Login successful',
            'data' => [
                'user' => new UserResource($user),
                'token' => $token,
                'token_type' => 'Bearer'
            ]
        ]);
    }

    public function logout(Request $request)
    {
        // Xóa token hiện tại
        $request->user()->currentAccessToken()->delete();

        return response()->json([
            'success' => true,
            'message' => 'Logged out successfully'
        ]);
    }

    public function me(Request $request)
    {
        return response()->json([
            'success' => true,
            'data' => new UserResource($request->user())
        ]);
    }
}
