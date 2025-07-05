<?php

namespace App\Traits;

use App\Enums\LogActionEnum;
use App\Enums\StatusEnum;
use App\Services\MediaLogService;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\Log;

trait LogsModelEvents
{
    public static function bootLogsModelEvents(): void
    {
        static::created(fn($model) =>
            self::tryLog(fn() =>
                MediaLogService::custom(
                    action: LogActionEnum::CREATED->value,
                    targetType: class_basename($model),
                    targetId: $model->id,
                    status: StatusEnum::SUCCESS->value,
                    type: 'crud',
                    description: LogActionEnum::CREATED->value,
                    data: $model->toArray()
                ),
                $model,
                LogActionEnum::CREATED->value . '_error'
            )
        );

        static::updated(fn($model) =>
            self::tryLog(fn() =>
                MediaLogService::custom(
                    action: LogActionEnum::UPDATED->value,
                    targetType: class_basename($model),
                    targetId: $model->id,
                    status: StatusEnum::SUCCESS->value,
                    type: 'crud',
                    description: LogActionEnum::UPDATED->value,
                    data: $model->getChanges()
                ),
                $model,
                LogActionEnum::UPDATED->value . '_error'
            )
        );

        static::deleted(fn($model) =>
            self::tryLog(fn() =>
                MediaLogService::custom(
                    action: LogActionEnum::DELETED->value,
                    targetType: class_basename($model),
                    targetId: $model->id,
                    status: StatusEnum::SUCCESS->value,
                    type: 'crud',
                    description: LogActionEnum::DELETED->value . ' (soft)',
                    data: $model->toArray()
                ),
                $model,
                LogActionEnum::DELETED->value . '_error'
            )
        );

        if (self::supportsSoftDeletes()) {
            static::restored(fn($model) =>
                self::tryLog(fn() =>
                    MediaLogService::custom(
                        action: LogActionEnum::RESTORED->value,
                        targetType: class_basename($model),
                        targetId: $model->id,
                        status: StatusEnum::SUCCESS->value,
                        type: 'crud',
                        description: LogActionEnum::RESTORED->value,
                        data: $model->toArray()
                    ),
                    $model,
                    LogActionEnum::RESTORED->value . '_error'
                )
            );

            static::forceDeleted(fn($model) =>
                self::tryLog(fn() =>
                    MediaLogService::custom(
                        action: LogActionEnum::FORCE_DELETED->value,
                        targetType: class_basename($model),
                        targetId: $model->id,
                        status: StatusEnum::SUCCESS->value,
                        type: 'crud',
                        description: LogActionEnum::FORCE_DELETED->value . ' (force)',
                        data: $model->toArray()
                    ),
                    $model,
                    LogActionEnum::FORCE_DELETED->value . '_error'
                )
            );
        }
    }

    protected static function tryLog(callable $callback, $model, string $errorAction): void
    {
        try {
            $callback();
        } catch (\Throwable $e) {
            // Ghi vào log Laravel
            Log::warning("❌ Failed to log '{$errorAction}' event", [
                'model'    => class_basename($model),
                'model_id' => $model->id ?? null,
                'error'    => $e->getMessage(),
            ]);

            // Ghi vào bảng media_logs
            try {
                MediaLogService::custom(
                    action: $errorAction,
                    targetType: class_basename($model),
                    targetId: $model->id ?? null,
                    status: StatusEnum::ERROR->value,
                    type: 'crud',
                    description: "Lỗi khi ghi log tự động: $errorAction .\n Error: " . $e->getMessage(),
                    data: [
                        'error'      => $e->getMessage(),
                        'trace'      => collect($e->getTrace())->take(3)->toArray(),
                        'model_data' => method_exists($model, 'toArray') ? $model->toArray() : null,
                    ]
                );
            } catch (\Throwable $inner) {
                Log::warning("❌ Failed to log '{$errorAction}' into media_logs", [
                    'model'    => class_basename($model),
                    'model_id' => $model->id ?? null,
                    'error'    => $inner->getMessage(),
                ]);
            }
        }
    }

    protected static function supportsSoftDeletes(): bool
    {
        return in_array(SoftDeletes::class, class_uses_recursive(static::class));
    }
}
