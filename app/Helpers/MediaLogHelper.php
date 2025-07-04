<?php

namespace App\Helpers;

use App\Models\MediaLog;
use Illuminate\Support\Facades\Request;

class MediaLogHelper
{
    public static function log(
        string $action,
        string $targetType,
        ?int $targetId = null,
        ?string $description = null,
        ?array $data = null
    ): void {
        MediaLog::create([
            'user_id'     => auth()->id(),
            'action'      => $action,
            'target_type' => $targetType,
            'target_id'   => $targetId,
            'description' => $description,
            'data'        => $data ? json_encode($data) : null,
            'ip'          => Request::ip(),
            'user_agent'  => Request::header('User-Agent'),
        ]);
    }
}
