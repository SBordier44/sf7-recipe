<?php

namespace App\Dto;

use Symfony\Component\Validator\Constraints as Assert;

class ContactDto
{
    #[Assert\NotBlank]
    #[Assert\Length(min: 3, max: 200)]
    public string $name = '';

    #[Assert\NotBlank]
    #[Assert\Email(mode: Assert\Email::VALIDATION_MODE_STRICT)]
    public string $email = '';

    #[Assert\NotBlank]
    #[Assert\Length(min: 3, max: 1000)]
    public string $message = '';

    #[Assert\NotBlank]
    public string $service = '';
}
