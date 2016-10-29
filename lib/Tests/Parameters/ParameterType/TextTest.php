<?php

namespace Netgen\BlockManager\Tests\Parameters\ParameterType;

use Netgen\BlockManager\Parameters\ParameterDefinition\Text;
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
     * @return \Netgen\BlockManager\Parameters\ParameterDefinition\Text
     */
    public function getParameterDefinition($options = array())
    {
        return new Text($options);
    }
}
