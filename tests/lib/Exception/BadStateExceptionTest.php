<?php

declare(strict_types=1);

namespace Netgen\Layouts\Tests\Exception;

use Netgen\Layouts\Exception\BadStateException;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\TestCase;

#[CoversClass(BadStateException::class)]
final class BadStateExceptionTest extends TestCase
{
    public function testExceptionMessage(): void
    {
        $exception = new BadStateException('test', 'Value must be an integer.');

        self::assertSame(
            'Argument "test" has an invalid state. Value must be an integer.',
            $exception->getMessage(),
        );
    }
}
