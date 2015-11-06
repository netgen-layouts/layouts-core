<?php

namespace Netgen\BlockManager\BlockDefinition\Tests;

use Netgen\BlockManager\BlockDefinition\Parameters\Text;
use PHPUnit_Framework_TestCase;

class TextTest extends PHPUnit_Framework_TestCase
{
    /**
     * @covers \Netgen\BlockManager\BlockDefinition\Parameters\Text::getType
     */
    public function testSetProperties()
    {
        $parameter = new Text('test', 'Test', array());

        self::assertEquals('text', $parameter->getType());
    }
}
