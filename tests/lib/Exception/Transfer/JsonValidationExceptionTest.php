<?php

declare(strict_types=1);

namespace Netgen\BlockManager\Tests\Exception\Transfer;

use Exception;
use Netgen\BlockManager\Exception\Transfer\JsonValidationException;
use PHPUnit\Framework\TestCase;

final class JsonValidationExceptionTest extends TestCase
{
    /**
     * @covers \Netgen\BlockManager\Exception\Transfer\JsonValidationException::parseError
     */
    public function testParseError(): void
    {
        $exception = JsonValidationException::parseError('Error message', 42);

        $this->assertEquals(
            'Provided data is not a valid JSON string: Error message (error code 42)',
            $exception->getMessage()
        );
    }

    /**
     * @covers \Netgen\BlockManager\Exception\Transfer\JsonValidationException::notAcceptable
     */
    public function testNotAcceptable(): void
    {
        $exception = JsonValidationException::notAcceptable('A reason');

        $this->assertEquals(
            'Provided data is not an acceptable JSON string: A reason',
            $exception->getMessage()
        );
    }

    /**
     * @covers \Netgen\BlockManager\Exception\Transfer\JsonValidationException::validationFailed
     */
    public function testValidationFailed(): void
    {
        $exception = JsonValidationException::validationFailed('Error message', new Exception());

        $this->assertEquals(
            'JSON data failed to validate the schema: Error message',
            $exception->getMessage()
        );

        $this->assertEquals(new Exception(), $exception->getPrevious());
    }
}
