<?php

namespace Netgen\BlockManager\Tests\Parameters\ParameterType;

use Netgen\BlockManager\Parameters\Parameter\Html;
use Netgen\BlockManager\Parameters\ParameterType\Html as HtmlType;
use PHPUnit\Framework\TestCase;

class HtmlTest extends TestCase
{
    /**
     * @covers \Netgen\BlockManager\Parameters\ParameterType\Html::getType
     */
    public function testGetType()
    {
        $type = new HtmlType();
        $this->assertEquals('html', $type->getType());
    }

    /**
     * Returns the parameter under test.
     *
     * @param array $options
     *
     * @return \Netgen\BlockManager\Parameters\Parameter\Html
     */
    public function getParameter($options = array())
    {
        return new Html($options);
    }
}
