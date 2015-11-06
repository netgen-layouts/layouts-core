<?php

namespace Netgen\BlockManager\BlockDefinition\Tests;

use Netgen\BlockManager\BlockDefinition\Parameters\Hidden;
use PHPUnit_Framework_TestCase;

class HiddenTest extends PHPUnit_Framework_TestCase
{
    /**
     * @covers \Netgen\BlockManager\BlockDefinition\Parameters\Hidden::getType
     */
    public function testSetProperties()
    {
        $parameter = new Hidden('test', 'Test', array());

        self::assertEquals('hidden', $parameter->getType());
    }
}
