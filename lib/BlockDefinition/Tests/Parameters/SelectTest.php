<?php

namespace Netgen\BlockManager\BlockDefinition\Tests;

use Netgen\BlockManager\BlockDefinition\Parameters\Select;
use PHPUnit_Framework_TestCase;

class SelectTest extends PHPUnit_Framework_TestCase
{
    /**
     * @covers \Netgen\BlockManager\BlockDefinition\Parameters\Select::getType
     */
    public function testSetProperties()
    {
        $parameter = new Select('test', 'Test', array());

        self::assertEquals('select', $parameter->getType());
    }
}
