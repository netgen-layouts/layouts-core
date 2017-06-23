<?php

namespace Netgen\BlockManager\Tests\Exception\Layout;

use Netgen\BlockManager\Exception\Layout\TargetTypeException;
use PHPUnit\Framework\TestCase;

class TargetTypeExceptionTest extends TestCase
{
    /**
     * @covers \Netgen\BlockManager\Exception\Layout\TargetTypeException::noTargetType
     */
    public function testNoTargetType()
    {
        $exception = TargetTypeException::noTargetType('type');

        $this->assertEquals(
            'Target type "type" does not exist.',
            $exception->getMessage()
        );
    }

    /**
     * @covers \Netgen\BlockManager\Exception\Layout\TargetTypeException::noFormMapper
     */
    public function testNoFormMapper()
    {
        $exception = TargetTypeException::noFormMapper('type');

        $this->assertEquals(
            'Form mapper for "type" target type does not exist.',
            $exception->getMessage()
        );
    }
}
