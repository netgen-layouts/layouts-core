<?php

declare(strict_types=1);

namespace Netgen\Layouts\Tests\Exception\Persistence;

use Netgen\Layouts\Exception\Persistence\TargetHandlerException;
use PHPUnit\Framework\TestCase;

final class TargetHandlerExceptionTest extends TestCase
{
    /**
     * @covers \Netgen\Layouts\Exception\Persistence\TargetHandlerException::noTargetHandler
     */
    public function testNoTargetHandler(): void
    {
        $exception = TargetHandlerException::noTargetHandler('Doctrine', 'target_type');

        self::assertSame(
            'Doctrine target handler for "target_type" target type does not exist.',
            $exception->getMessage(),
        );
    }
}
