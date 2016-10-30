<?php

namespace Netgen\BlockManager\Tests\Parameters\ParameterType;

use Netgen\BlockManager\Parameters\Parameter\Text;
use Netgen\BlockManager\Parameters\ParameterType\Text as TextType;
use PHPUnit\Framework\TestCase;

class TextTest extends TestCase
{
    /**
     * @covers \Netgen\BlockManager\Parameters\ParameterType\Text::getType
     */
    public function testGetType()
    {
        $type = new TextType();
        $this->assertEquals('text', $type->getType());
    }

    /**
     * Returns the parameter under test.
     *
     * @param array $options
     *
     * @return \Netgen\BlockManager\Parameters\Parameter\Text
     */
    public function getParameter($options = array())
    {
        return new Text($options);
    }
}
