<?php

namespace App\Services;

use App\Models\MediaLog;
use Illuminate\Support\Facades\Request;

class MediaLogService
{
    public static function custom(string $action, string $targetType, int|string|null $targetId = null, ?string $status, ?string $type, ?string $description = null, ?array $data = null): void
    {
        self::log($action, $targetType, $targetId, $status, $type, $description, $data);
    }

    protected static function log(string $action, string $targetType, int|string|null $targetId, ?string $status, ?string $type, ?string $description, ?array $data): void
    {
        try {
            MediaLog::create([
                'user_id'     => auth()->id(),
                'action'      => $action,
                'target_type' => $targetType,
                'target_id'   => $targetId,
                'status'      => $status,
                'type'        => $type,
                'description' => $description,
                'data'        => $data ? json_encode($data, JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT) : null,
                'ip'          => Request::ip(),
                'user_agent'  => Request::header('User-Agent'),
            ]);
        } catch (\Throwable $e) {
            // Không throw lỗi khi log thất bại – tránh ảnh hưởng hệ thống
            \Log::warning("Failed to write media log: " . $e->getMessage(), [
                'action' => $action,
                'target_type' => $targetType,
                'target_id' => $targetId,
            ]);
        }
    }
}
