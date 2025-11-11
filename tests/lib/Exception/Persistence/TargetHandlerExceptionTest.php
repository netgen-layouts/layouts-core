<?php

declare(strict_types=1);

namespace Netgen\Layouts\Tests\Exception\Persistence;

use Netgen\Layouts\Exception\Persistence\TargetHandlerException;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\TestCase;

#[CoversClass(TargetHandlerException::class)]
final class TargetHandlerExceptionTest extends TestCase
{
    public function testNoTargetHandler(): void
    {
        $exception = TargetHandlerException::noTargetHandler('Doctrine', 'target_type');

        self::assertSame(
            'Doctrine target handler for "target_type" target type does not exist.',
            $exception->getMessage(),
        );
    }
}
