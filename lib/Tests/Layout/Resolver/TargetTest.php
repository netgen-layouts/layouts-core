<?php

namespace Netgen\BlockManager\Tests\Layout\Resolver;

use Netgen\BlockManager\Layout\Resolver\Target;

class TargetTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @covers \Netgen\BlockManager\Layout\Resolver\Target::__construct
     * @covers \Netgen\BlockManager\Layout\Resolver\Target::getIdentifier
     * @covers \Netgen\BlockManager\Layout\Resolver\Target::getValues
     */
    public function testConstructor()
    {
        $target = new Target(
            array(
                'identifier' => 'target',
                'values' => array('value'),
            )
        );

        self::assertEquals('target', $target->getIdentifier());
        self::assertEquals(array('value'), $target->getValues());
    }
}
