<?php

declare(strict_types=1);

namespace Netgen\Layouts\Tests\Exception;

use Netgen\Layouts\Exception\NotFoundException;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\TestCase;

#[CoversClass(NotFoundException::class)]
final class NotFoundExceptionTest extends TestCase
{
    public function testExceptionMessage(): void
    {
        $exception = new NotFoundException('test');

        self::assertSame('Could not find test', $exception->getMessage());
    }

    public function testExceptionMessageWithIdentifier(): void
    {
        $exception = new NotFoundException('test', 1);

        self::assertSame(
            'Could not find test with identifier "1"',
            $exception->getMessage(),
        );
    }
}
