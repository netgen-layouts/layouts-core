<?php

declare(strict_types=1);

namespace Netgen\Layouts\Tests\Exception;

use Netgen\Layouts\Exception\NotFoundException;
use PHPUnit\Framework\TestCase;

final class NotFoundExceptionTest extends TestCase
{
    /**
     * @covers \Netgen\Layouts\Exception\NotFoundException::__construct
     */
    public function testExceptionMessage(): void
    {
        $exception = new NotFoundException('test');

        self::assertSame('Could not find test', $exception->getMessage());
    }

    /**
     * @covers \Netgen\Layouts\Exception\NotFoundException::__construct
     */
    public function testExceptionMessageWithIdentifier(): void
    {
        $exception = new NotFoundException('test', 1);

        self::assertSame(
            'Could not find test with identifier "1"',
            $exception->getMessage(),
        );
    }
}
