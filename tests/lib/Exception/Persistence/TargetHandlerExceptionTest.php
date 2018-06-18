<?php

declare(strict_types=1);

namespace Netgen\BlockManager\Tests\Exception\Persistence;

use Netgen\BlockManager\Exception\Persistence\TargetHandlerException;
use PHPUnit\Framework\TestCase;

final class TargetHandlerExceptionTest extends TestCase
{
    /**
     * @covers \Netgen\BlockManager\Exception\Persistence\TargetHandlerException::noTargetHandler
     */
    public function testNoTargetHandler(): void
    {
        $exception = TargetHandlerException::noTargetHandler('Doctrine', 'target_type');

        $this->assertSame(
            'Doctrine target handler for "target_type" target type does not exist.',
            $exception->getMessage()
        );
    }
}
