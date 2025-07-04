<?php

namespace App\Services;

use App\Enums\LogActionEnum;
use App\Models\MediaLog;
use Illuminate\Support\Facades\Request;

class MediaLogService
{
    public static function created(string $targetType, int|string|null $targetId = null, ?string $description = null, ?array $data = null): void
    {
        self::log(LogActionEnum::CREATED->value, $targetType, $targetId, $description, $data);
    }

    public static function updated(string $targetType, int|string|null $targetId = null, ?string $description = null, ?array $data = null): void
    {
        self::log(LogActionEnum::UPDATED->value, $targetType, $targetId, $description, $data);
    }

    public static function deleted(string $targetType, int|string|null $targetId = null, ?string $description = null, ?array $data = null): void
    {
        self::log(LogActionEnum::DELETED->value, $targetType, $targetId, $description, $data);
    }

    public static function restored(string $targetType, int|string|null $targetId = null, ?string $description = null, ?array $data = null): void
    {
        self::log(LogActionEnum::RESTORED->value, $targetType, $targetId, $description, $data);
    }

    public static function forceDeleted(string $targetType, int|string|null $targetId = null, ?string $description = null, ?array $data = null): void
    {
        self::log(LogActionEnum::FORCE_DELETED->value, $targetType, $targetId, $description, $data);
    }

    public static function error(string $targetType, int|string|null $targetId = null, ?string $description = null, ?array $data = null): void
    {
        self::log(LogActionEnum::ERROR->value, $targetType, $targetId, $description, $data);
    }

    public static function uploaded(string $targetType, int|string|null $targetId = null, ?string $description = null, ?array $data = null): void
    {
        self::log(LogActionEnum::UPLOADED->value, $targetType, $targetId, $description ?? 'Đã tải lên', $data);
    }

    public static function downloaded(string $targetType, int|string|null $targetId = null, ?string $description = null, ?array $data = null): void
    {
        self::log(LogActionEnum::DOWNLOADED->value, $targetType, $targetId, $description ?? 'Đã tải xuống', $data);
    }

    public static function moved(string $targetType, int|string|null $targetId = null, ?string $description = null, ?array $data = null): void
    {
        self::log(LogActionEnum::MOVED->value, $targetType, $targetId, $description ?? 'Đã di chuyển', $data);
    }

    public static function renamed(string $targetType, int|string|null $targetId = null, ?string $description = null, ?array $data = null): void
    {
        self::log(LogActionEnum::RENAMED->value, $targetType, $targetId, $description ?? 'Đã đổi tên', $data);
    }

    public static function copied(string $targetType, int|string|null $targetId = null, ?string $description = null, ?array $data = null): void
    {
        self::log(LogActionEnum::COPIED->value, $targetType, $targetId, $description ?? 'Đã sao chép', $data);
    }

    public static function dragAndDrop(string $targetType, int|string|null $targetId = null, ?string $description = null, ?array $data = null): void
    {
        self::log(LogActionEnum::DRAG_DROP->value, $targetType, $targetId, $description ?? 'Đã kéo vào', $data);
    }

    public static function favorited(string $targetType, int|string|null $targetId = null, ?string $description = null, ?array $data = null): void
    {
        self::log(LogActionEnum::FAVOURITE->value, $targetType, $targetId, $description ?? 'Đã thêm vào mục yêu thích', $data);
    }

    public static function unfavorited(string $targetType, int|string|null $targetId = null, ?string $description = null, ?array $data = null): void
    {
        self::log(LogActionEnum::UN_FAVOURITE->value, $targetType, $targetId, $description ?? 'Đã xoá khỏi mục yêu thích', $data);
    }

    public static function shared(string $targetType, int|string|null $targetId = null, ?string $description = null, ?array $data = null): void
    {
        self::log(LogActionEnum::SHARED->value, $targetType, $targetId, $description ?? 'Đã chia sẻ', $data);
    }

    public static function unshared(string $targetType, int|string|null $targetId = null, ?string $description = null, ?array $data = null): void
    {
        self::log(LogActionEnum::UNSHARE->value, $targetType, $targetId, $description ?? 'Không chia sẻ', $data);
    }

    public static function locked(string $targetType, int|string|null $targetId = null, ?string $description = null, ?array $data = null): void
    {
        self::log(LogActionEnum::LOCKED->value, $targetType, $targetId, $description ?? 'Đã khoá nội dung', $data);
    }

    public static function unlocked(string $targetType, int|string|null $targetId = null, ?string $description = null, ?array $data = null): void
    {
        self::log(LogActionEnum::UNLOCKED->value, $targetType, $targetId, $description ?? 'Đã mở khoá nội dung', $data);
    }

    public static function zip(string $targetType, int|string|null $targetId = null, ?string $description = null, ?array $data = null): void
    {
        self::log(LogActionEnum::ZIP->value, $targetType, $targetId, $description ?? 'Đã nén thành file.zip', $data);
    }

    public static function unzip(string $targetType, int|string|null $targetId = null, ?string $description = null, ?array $data = null): void
    {
        self::log(LogActionEnum::UNZIP->value, $targetType, $targetId, $description ?? 'Đã giải nén file.zip', $data);
    }

    public static function custom(string $action, string $targetType, int|string|null $targetId = null, ?string $description = null, ?array $data = null): void
    {
        self::log($action, $targetType, $targetId, $description, $data);
    }

    protected static function log(string $action, string $targetType, int|string|null $targetId, ?string $description, ?array $data): void
    {
        try {
            MediaLog::create([
                'user_id'     => auth()->id(),
                'action'      => $action,
                'target_type' => $targetType,
                'target_id'   => $targetId,
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
