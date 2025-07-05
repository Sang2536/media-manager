<?php

namespace App\Enums;

enum PermissionEnum: string
{
    //  Permission
    case CREATE = 'create';
    case READ = 'read';
    case UPDATE = 'update';
    case DELETE = 'delete';
    case UPLOAD = 'upload';
    case MANAGE = 'manage';
    case ADMIN = 'admin';

    public function icon(): string
    {
        return match($this) {
            self::CREATE    => '➕',
            self::READ      => '👁️',
            self::UPDATE    => '✏️',
            self::DELETE    => '🗑️',
            self::UPLOAD    => '📤',
            self::MANAGE    => '👤',
            self::ADMIN     => '👑',
        };
    }
}
