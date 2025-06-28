<?php

namespace App\Helpers;

class ResponseHelper
{
    public static function result(
        bool $ok,
        string $message,
        int $status = 200,
        string|null $redirect = null,
        array $extra = []
    ) {
        if (request()->expectsJson()) {
            return response()->json(array_merge([
                'ok'      => $ok,
                'message' => $message,
                'code'    => $status, // hỗ trợ debug
            ], $extra), $status);
        }

        // Trường hợp thất bại → Redirect back with error
        if (! $ok) {
            return redirect()
                ->back()
                ->withErrors(['error' => $message])
                ->withInput();
        }

        // Thành công → Redirect theo URL hoặc về lại
        return $redirect
            ? redirect()->to($redirect)->with('success', $message)
            : redirect()->back()->with('success', $message);
    }

    public static function success(string $message, $redirect = null, array $extra = [])
    {
        return self::result(true, $message, 200, $redirect, $extra);
    }

    public static function error(string $message, int $status = 400)
    {
        return self::result(false, $message, $status);
    }
}
