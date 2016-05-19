<?php

namespace Netgen\BlockManager\Tests\Collection;

use Netgen\BlockManager\Collection\ResultValue;
use stdClass;

class ResultValueTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @covers \Netgen\BlockManager\Collection\ResultValue::getId
     * @covers \Netgen\BlockManager\Collection\ResultValue::getType
     * @covers \Netgen\BlockManager\Collection\ResultValue::getName
     * @covers \Netgen\BlockManager\Collection\ResultValue::isVisible
     * @covers \Netgen\BlockManager\Collection\ResultValue::getObject
     */
    public function testObject()
    {
        $resultValue = new ResultValue(
            array(
                'id' => 42,
                'type' => 'type',
                'name' => 'Value name',
                'isVisible' => true,
                'object' => new stdClass(),
            )
        );

        self::assertEquals(42, $resultValue->getId());
        self::assertEquals('type', $resultValue->getType());
        self::assertEquals('Value name', $resultValue->getName());
        self::assertEquals(true, $resultValue->isVisible());
        self::assertEquals(new stdClass(), $resultValue->getObject());
    }
}
