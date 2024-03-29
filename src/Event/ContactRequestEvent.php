<?php

namespace App\Event;

use App\Dto\ContactDto;

readonly class ContactRequestEvent
{
    public function __construct(public ContactDto $data)
    {
    }
}
