<?php

declare(strict_types=1);

namespace Netgen\BlockManager\Tests\Exception;

use Netgen\BlockManager\Exception\InvalidArgumentException;
use PHPUnit\Framework\TestCase;

final class InvalidArgumentExceptionTest extends TestCase
{
    /**
     * @covers \Netgen\BlockManager\Exception\InvalidArgumentException::__construct
     */
    public function testExceptionMessage(): void
    {
        $exception = new InvalidArgumentException('test', 'Value must be an integer.');

        $this->assertSame(
            'Argument "test" has an invalid value. Value must be an integer.',
            $exception->getMessage()
        );
    }
}
