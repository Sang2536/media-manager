<?php

namespace App\Enums;

enum LogModelEnum: string
{
    case MODEL = 'model';
    case MEDIA = 'media';
    case FILE = 'media_files';
    case FOLDER = 'media_folders';
    case LOG = 'media_logs';
    case METADATA = 'media_metadata';
    case TAG = 'media_tags';
    case USER = 'user';

    public function icon(): string
    {
        return match($this) {
            default => '',
            self::MODEL     =>  '📋',
            self::MEDIA     =>  '📝',
            self::FILE      =>  '🖼️',
            self::FOLDER    =>  '📁',
            self::LOG       =>  '️📜',
            self::METADATA  =>  '📄',
            self::TAG       =>  '️🏷️',
            self::USER      =>  '👤',
        };
    }

    public static function fromDescription(?string $model, ?string $describe): ?self
    {
        $base = self::toSingularLabel($model);

        if ($describe) {
            $base .= ' - ' . $describe;
        }

        return self::tryFrom($base);
    }

    private function toSingularLabel(string $value): string
    {
        $singular = rtrim($value, 's');
        return ucwords(str_replace('_', ' ', $singular));
    }
}
