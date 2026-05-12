<?php

declare(strict_types=1);

namespace Module\Shared\Exception;

use Symfony\Component\HttpKernel\Exception\HttpException;
use Symfony\Component\Validator\ConstraintViolation;
use Symfony\Component\Validator\ConstraintViolationList;
use Symfony\Component\Validator\Exception\ValidationFailedException;

class AppValidationException extends HttpException
{
    public function __construct(string $field, string $message)
    {
        parent::__construct(
            422,
            $field . ': ' . $message,
            new ValidationFailedException(
                0,
                new ConstraintViolationList([
                    new ConstraintViolation($message, '', [], null, $field, null),
                ])
            )
        );
    }
}
