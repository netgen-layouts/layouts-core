<?php

declare(strict_types=1);

namespace Netgen\BlockManager\Tests\Exception;

use Netgen\BlockManager\Exception\BadStateException;
use PHPUnit\Framework\TestCase;

final class BadStateExceptionTest extends TestCase
{
    /**
     * @covers \Netgen\BlockManager\Exception\BadStateException::__construct
     */
    public function testExceptionMessage(): void
    {
        $exception = new BadStateException('test', 'Value must be an integer.');

        self::assertSame(
            'Argument "test" has an invalid state. Value must be an integer.',
            $exception->getMessage()
        );
    }
}
