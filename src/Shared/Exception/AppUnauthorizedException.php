<?php

declare(strict_types=1);

namespace Module\Shared\Exception;

use Symfony\Component\HttpKernel\Exception\UnauthorizedHttpException;

class AppUnauthorizedException extends UnauthorizedHttpException
{
    public function __construct(string $message)
    {
        parent::__construct('JWT', $message);
    }
}
