<?php
declare(strict_types=1);

namespace Module\Shared\OpenApi;

class UnauthorizedErrorSchema
{
    public string $type = 'https://tools.ietf.org/html/rfc2616#section-10';
    public string $title = 'An error occurred';
    public int $status = 401;
    public string $detail = 'Unauthorized';
}
