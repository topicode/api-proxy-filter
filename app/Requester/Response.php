<?php

namespace App\Requester;

readonly class Response
{
    public function __construct(
        public string $response,
        public int $status = 200,
    ) {}
}
