<?php

declare(strict_types=1);

namespace Netgen\Layouts\Tests\Exception\Transfer;

use Netgen\Layouts\Exception\Transfer\JsonValidationException;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\TestCase;

#[CoversClass(JsonValidationException::class)]
final class JsonValidationExceptionTest extends TestCase
{
    public function testParseError(): void
    {
        $exception = JsonValidationException::parseError('Error message', 42);

        self::assertSame(
            'Provided data is not a valid JSON string: Error message (error code 42)',
            $exception->getMessage(),
        );
    }

    public function testNotAcceptable(): void
    {
        $exception = JsonValidationException::notAcceptable('A reason');

        self::assertSame(
            'Provided data is not an acceptable JSON string: A reason',
            $exception->getMessage(),
        );
    }

    public function testValidationFailed(): void
    {
        $exception = JsonValidationException::validationFailed('Error message', 'some.path');

        self::assertSame(
            'JSON data failed to validate the schema at path "some.path": Error message',
            $exception->getMessage(),
        );
    }
}
