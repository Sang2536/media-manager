<?php

namespace App\Enums;

enum LogActionEnum: string
{
    case CREATED = 'created';
    case UPDATED = 'updated';
    case DELETED = 'deleted';
    case FORCE_DELETED = 'force_deleted';
    case RESTORED = 'restored';
    case ERROR  = 'error';
    case DOWNLOADED = 'downloaded';
    case UPLOADED = 'uploaded';
    case RENAMED = 'renamed';
    case COPIED = 'copied';
    case MOVED = 'moved';
    case SHARED = 'shared';
    case UNSHARE = 'unshare';
    case FAVOURITE = 'favourite';
    case UN_FAVOURITE = 'un_favourite';
    case LOCKED = 'locked';
    case UNLOCKED = 'unlocked';
    case DRAG_DROP = 'drag_drop';
    case ZIP = 'zip';
    case UNZIP = 'unzip';

    public function icon(): string
    {
        return match($this) {
            default => '',
            self::CREATED       => '➕',
            self::UPDATED       => '✏️',
            self::DELETED      => '🗑️',
            self::FORCE_DELETED => '🚫',
            self::RESTORED      => '♻️',

            self::ERROR        => '❌',
            self::DOWNLOADED       => '📥',
            self::UPLOADED       => '📤',
            self::RENAMED       => '✍️',
            self::COPIED    =>  '📋',
            self::MOVED => '↔️',
            self::DRAG_DROP => '🖱',

            self::SHARED => '🔗',
            self::UNSHARE => '',
            self::FAVOURITE => '❤️',
            self::UN_FAVOURITE => '❤',
            self::LOCKED => '🔒',
            self::UNLOCKED => '🔓',

            self::ZIP => '📦',
            self::UNZIP => '',
        };
    }

    public function badgeColor(): array
    {
        return match($this) {
            default             => ['bg' => 'bg-gray-100', 'text' => 'text-gray-700'],
            self::CREATED       => ['bg' => 'bg-green-100',  'text' => 'text-green-700'],
            self::UPDATED       => ['bg' => 'bg-yellow-100', 'text' => 'text-yellow-800'],
            self::DELETED       => ['bg' => 'bg-red-100',    'text' => 'text-red-700'],
            self::FORCE_DELETED => ['bg' => 'bg-black',      'text' => 'text-white'],
            self::RESTORED      => ['bg' => 'bg-emerald-100','text' => 'text-emerald-700'],

            self::ERROR         => ['bg' => 'bg-red-200',   'text' => 'text-red-700'],
            self::DOWNLOADED    => ['bg' => 'bg-blue-100',   'text' => 'text-blue-700'],
            self::UPLOADED      => ['bg' => 'bg-cyan-100',   'text' => 'text-cyan-700'],
            self::COPIED        => ['bg' => 'bg-amber-100',   'text' => 'text-amber-700'],
            self::MOVED         => ['bg' => 'bg-yellow-100',  'text' => 'text-yellow-700'],
            self::DRAG_DROP     => ['bg' => 'bg-lime-100',   'text' => 'text-lime-700'],
            self::RENAMED       => ['bg' => 'bg-indigo-100', 'text' => 'text-indigo-700'],
        };
    }

    public static function fromAction(?string $action): ?self
    {
        $base = str($action)->before('_')->lower();
        return self::tryFrom($base);
    }
}
