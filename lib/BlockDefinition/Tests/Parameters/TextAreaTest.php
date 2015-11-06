<?php

namespace Netgen\BlockManager\BlockDefinition\Tests;

use Netgen\BlockManager\BlockDefinition\Parameters\TextArea;
use PHPUnit_Framework_TestCase;

class TextAreaTest extends PHPUnit_Framework_TestCase
{
    /**
     * @covers \Netgen\BlockManager\BlockDefinition\Parameters\TextArea::getType
     */
    public function testSetProperties()
    {
        $parameter = new TextArea('test', 'Test', array());

        self::assertEquals('textarea', $parameter->getType());
    }
}
