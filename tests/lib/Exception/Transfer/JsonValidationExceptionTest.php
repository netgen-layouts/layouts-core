<?php

declare(strict_types=1);

namespace Netgen\Layouts\Tests\Exception\Transfer;

use Exception;
use Netgen\Layouts\Exception\Transfer\JsonValidationException;
use PHPUnit\Framework\TestCase;

final class JsonValidationExceptionTest extends TestCase
{
    /**
     * @covers \Netgen\Layouts\Exception\Transfer\JsonValidationException::parseError
     */
    public function testParseError(): void
    {
        $exception = JsonValidationException::parseError('Error message', 42);

        self::assertSame(
            'Provided data is not a valid JSON string: Error message (error code 42)',
            $exception->getMessage(),
        );
    }

    /**
     * @covers \Netgen\Layouts\Exception\Transfer\JsonValidationException::notAcceptable
     */
    public function testNotAcceptable(): void
    {
        $exception = JsonValidationException::notAcceptable('A reason');

        self::assertSame(
            'Provided data is not an acceptable JSON string: A reason',
            $exception->getMessage(),
        );
    }

    /**
     * @covers \Netgen\Layouts\Exception\Transfer\JsonValidationException::validationFailed
     */
    public function testValidationFailed(): void
    {
        $previousException = new Exception();
        $exception = JsonValidationException::validationFailed('Error message', $previousException);

        self::assertSame(
            'JSON data failed to validate the schema: Error message',
            $exception->getMessage(),
        );

        self::assertSame($previousException, $exception->getPrevious());
    }
}
