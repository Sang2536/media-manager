<?php

namespace App\Observers;

use App\Services\MediaLogService;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Log;

class MediaModelObserver
{
    protected string $targetType;

    public function __construct(string $targetType)
    {
        $this->targetType = $targetType;
    }

    public static function for(string $targetType): self
    {
        return new self($targetType);
    }

    public function created(Model $model): void
    {
        try {
            MediaLogService::created(
                targetType: $this->targetType,
                targetId: $model->id,
                description: 'Tạo mới',
                data: ['data' => $model->toArray()]
            );
        } catch (\Throwable $e) {
            $this->logError('created', $e, $model);
        }
    }

    public function updated(Model $model): void
    {
        try {
            if ($model->wasChanged()) {
                MediaLogService::updated(
                    targetType: $this->targetType,
                    targetId: $model->id,
                    description: 'Cập nhật',
                    data: [
                        'before' => $model->getOriginal(),
                        'after' => $model->getChanges(),
                    ]
                );
            }
        } catch (\Throwable $e) {
            $this->logError('updated', $e, $model);
        }
    }

    public function deleted(Model $model): void
    {
        try {
            MediaLogService::deleted(
                targetType: $this->targetType,
                targetId: $model->id,
                description: 'Xoá',
                data: ['data' => $model->toArray()]
            );
        } catch (\Throwable $e) {
            $this->logError('deleted', $e, $model);
        }
    }

    protected function logError(string $action, \Throwable $e, Model $model): void
    {
        Log::error("Error in MediaModelObserver [$action]: " . $e->getMessage(), [
            'model_type' => get_class($model),
            'model_id' => $model->id ?? null,
            'target_type' => $this->targetType,
            'trace' => $e->getTraceAsString(),
        ]);
    }
}
