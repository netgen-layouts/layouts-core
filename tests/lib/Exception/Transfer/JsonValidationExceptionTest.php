<?php

namespace Netgen\BlockManager\Tests\Exception\Transfer;

use Exception;
use Netgen\BlockManager\Exception\Transfer\JsonValidationException;
use PHPUnit\Framework\TestCase;

final class JsonValidationExceptionTest extends TestCase
{
    /**
     * @covers \Netgen\BlockManager\Exception\Transfer\JsonValidationException::validationFailed
     */
    public function testVersionNotAccepted()
    {
        $exception = JsonValidationException::validationFailed('Error message', new Exception());

        $this->assertEquals(
            'JSON data failed to validate the schema: Error message',
            $exception->getMessage()
        );

        $this->assertEquals(new Exception(), $exception->getPrevious());
    }
}
