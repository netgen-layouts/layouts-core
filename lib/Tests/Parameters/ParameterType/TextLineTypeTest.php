<?php

namespace Netgen\BlockManager\Tests\Parameters\ParameterType;

use Netgen\BlockManager\Parameters\Parameter\TextLine;
use Netgen\BlockManager\Parameters\ParameterType\TextLineType;
use PHPUnit\Framework\TestCase;

class TextLineTypeTest extends TestCase
{
    /**
     * @covers \Netgen\BlockManager\Parameters\ParameterType\TextLineType::getType
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
     * @return \Netgen\BlockManager\Parameters\Parameter\TextLine
     */
    public function getParameter($options = array())
    {
        return new TextLine($options);
    }
}
