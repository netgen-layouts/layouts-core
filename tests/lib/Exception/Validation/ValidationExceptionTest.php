<?php

declare(strict_types=1);

namespace Netgen\Layouts\Tests\Exception\Validation;

use Netgen\Layouts\Exception\Validation\ValidationException;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\TestCase;

#[CoversClass(ValidationException::class)]
final class ValidationExceptionTest extends TestCase
{
    public function testValidationFailed(): void
    {
        $exception = ValidationException::validationFailed('param', 'Some error');

        self::assertSame(
            'There was an error validating "param": Some error',
            $exception->getMessage(),
        );
    }
}
