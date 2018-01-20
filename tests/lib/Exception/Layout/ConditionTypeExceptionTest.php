<?php

namespace Netgen\BlockManager\Tests\Exception\Layout;

use Netgen\BlockManager\Exception\Layout\ConditionTypeException;
use PHPUnit\Framework\TestCase;

final class ConditionTypeExceptionTest extends TestCase
{
    /**
     * @covers \Netgen\BlockManager\Exception\Layout\ConditionTypeException::noConditionType
     */
    public function testNoConditionType()
    {
        $exception = ConditionTypeException::noConditionType('type');

        $this->assertEquals(
            'Condition type "type" does not exist.',
            $exception->getMessage()
        );
    }

    /**
     * @covers \Netgen\BlockManager\Exception\Layout\ConditionTypeException::noFormMapper
     */
    public function testNoFormMapper()
    {
        $exception = ConditionTypeException::noFormMapper('type');

        $this->assertEquals(
            'Form mapper for "type" condition type does not exist.',
            $exception->getMessage()
        );
    }
}
