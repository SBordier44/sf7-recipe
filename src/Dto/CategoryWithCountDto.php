<?php

namespace App\Dto;

readonly class CategoryWithCountDto
{
    public function __construct(
        public int $id,
        public string $name,
        public int $total
    ) {
    }
}
