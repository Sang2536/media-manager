<?php

namespace App\Enums;

enum StatusEnum: string
{
    //  Success
    case SUCCESS = 'success';
    case DONE = 'done';
    case COMPLETED = 'completed';

    // Error / Warning
    case WARNING = 'warning';

    case ERROR = 'error';
    case FAILED = 'failed';

    //  Encapsulation
    case PUBLIC = 'public';
    case PRIVATE = 'private';
    case PROTECTED = 'protected';

    public function icon(): string
    {
        return match($this) {
            self::SUCCESS       =>  '✅',
            self::DONE          =>  '✅',
            self::COMPLETED     =>  '✅',
            self::WARNING       =>  '⚠️',
            self::ERROR         =>  '❌',
            self::FAILED        =>  '❌',
            self::PUBLIC        =>  '🌐',
            self::PRIVATE       =>  '⛔',
            self::PROTECTED     =>  '🛡️',
        };
    }

    public function bagdeColor(): array {
        return match ($this) {
            self::SUCCESS       => ['bg' => 'bg-green-200',    'text' => 'text-green-700'],
            self::DONE          => ['bg' => 'bg-green-200',    'text' => 'text-green-700'],
            self::COMPLETED     => ['bg' => 'bg-green-200',    'text' => 'text-green-700'],
            self::ERROR         => ['bg' => 'bg-red-200',    'text' => 'text-red-700'],
            self::WARNING       => ['bg' => 'bg-orange-200', 'text' => 'text-orange-800'],
            self::FAILED        => ['bg' => 'bg-rose-200',   'text' => 'text-rose-800'],
            self::PUBLIC        => ['bg' => 'bg-gray-300',    'text' => 'text-gray-800'],
            self::PRIVATE       => ['bg' => 'bg-gray-300',    'text' => 'text-gray-800'],
            self::PROTECTED     => ['bg' => 'bg-gray-300',    'text' => 'text-gray-800'],
        };
    }
}
