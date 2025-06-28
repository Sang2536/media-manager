<?php

namespace App\Traits;

trait HasDto
{
    public function toDto(string $dtoClass): object
    {
        return new $dtoClass(...$this->validated());
    }
}
