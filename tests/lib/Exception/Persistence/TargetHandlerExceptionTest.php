<?php

namespace Netgen\BlockManager\Tests\Exception\Persistence;

use Netgen\BlockManager\Exception\Persistence\TargetHandlerException;
use PHPUnit\Framework\TestCase;

class TargetHandlerExceptionTest extends TestCase
{
    /**
     * @covers \Netgen\BlockManager\Exception\Persistence\TargetHandlerException::noTargetHandler
     */
    public function testNoTargetHandler()
    {
        $exception = TargetHandlerException::noTargetHandler('Doctrine', 'target_type');

        $this->assertEquals(
            'Doctrine target handler for "target_type" target type does not exist.',
            $exception->getMessage()
        );
    }
}
