<?php
declare(strict_types=1);

namespace Module\Shared\Exception;

use Symfony\Component\HttpKernel\Exception\HttpException;
use Symfony\Component\Validator\ConstraintViolation;
use Symfony\Component\Validator\ConstraintViolationList;
use Symfony\Component\Validator\Exception\ValidationFailedException;

class AppEntityVersionException extends HttpException
{
    public function __construct()
    {
        $message = 'Entity version in database is different than version received in API call.';
        parent::__construct(
            422,
            $message,
            new ValidationFailedException(
                0,
                new ConstraintViolationList([
                    new ConstraintViolation($message, '', [], null, 'version', null),
                ])
            )
        );
    }
}
