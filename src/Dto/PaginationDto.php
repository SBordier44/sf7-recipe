<?php

namespace App\Dto;

use Symfony\Component\Validator\Constraints as Assert;

readonly class PaginationDto
{
    public function __construct(
        #[Assert\Positive]
        public int $page = 1,
    ) {
    }
}
