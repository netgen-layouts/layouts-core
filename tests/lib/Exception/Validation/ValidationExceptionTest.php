<?php

declare(strict_types=1);

namespace Netgen\Layouts\Tests\Exception\Validation;

use Netgen\Layouts\Exception\Validation\ValidationException;
use PHPUnit\Framework\TestCase;

final class ValidationExceptionTest extends TestCase
{
    /**
     * @covers \Netgen\Layouts\Exception\Validation\ValidationException::validationFailed
     */
    public function testValidationFailed(): void
    {
        $exception = ValidationException::validationFailed('param', 'Some error');

        self::assertSame(
            'There was an error validating "param": Some error',
            $exception->getMessage(),
        );
    }
}
