<?php

declare(strict_types=1);

namespace Netgen\Layouts\Tests\Exception;

use Netgen\Layouts\Exception\InvalidArgumentException;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\TestCase;

#[CoversClass(InvalidArgumentException::class)]
final class InvalidArgumentExceptionTest extends TestCase
{
    public function testExceptionMessage(): void
    {
        $exception = new InvalidArgumentException('test', 'Value must be an integer.');

        self::assertSame(
            'Argument "test" has an invalid value. Value must be an integer.',
            $exception->getMessage(),
        );
    }
}
