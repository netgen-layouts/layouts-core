<?php

declare(strict_types=1);

namespace Netgen\Layouts\Tests\Exception;

use Netgen\Layouts\Exception\BadStateException;
use PHPUnit\Framework\TestCase;

final class BadStateExceptionTest extends TestCase
{
    /**
     * @covers \Netgen\Layouts\Exception\BadStateException::__construct
     */
    public function testExceptionMessage(): void
    {
        $exception = new BadStateException('test', 'Value must be an integer.');

        self::assertSame(
            'Argument "test" has an invalid state. Value must be an integer.',
            $exception->getMessage(),
        );
    }
}
