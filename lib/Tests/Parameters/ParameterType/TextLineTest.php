<?php

namespace Netgen\BlockManager\Tests\Parameters\ParameterType;

use Netgen\BlockManager\Parameters\ParameterDefinition\TextLine;
use Netgen\BlockManager\Parameters\ParameterType\TextLine as TextLineType;
use PHPUnit\Framework\TestCase;

class TextLineTest extends TestCase
{
    /**
     * @covers \Netgen\BlockManager\Parameters\ParameterType\TextLine::getType
     */
    public function testGetType()
    {
        $type = new TextLineType();
        $this->assertEquals('text_line', $type->getType());
    }

    /**
     * Returns the parameter under test.
     *
     * @param array $options
     *
     * @return \Netgen\BlockManager\Parameters\ParameterDefinition\TextLine
     */
    public function getParameterDefinition($options = array())
    {
        return new TextLine($options);
    }
}
