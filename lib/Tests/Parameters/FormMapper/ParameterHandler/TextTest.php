<?php

namespace Netgen\BlockManager\Tests\Parameters\FormMapper\ParameterHandler;

use Netgen\BlockManager\Parameters\FormMapper\ParameterHandler\Text;
use Netgen\BlockManager\Parameters\Parameter\Text as TextParameter;

class TextTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @covers \Netgen\BlockManager\Parameters\FormMapper\ParameterHandler\Text::getFormType
     */
    public function testGetFormType()
    {
        $handler = new Text();

        self::assertEquals('text', $handler->getFormType());
    }

    /**
     * @covers \Netgen\BlockManager\Parameters\FormMapper\ParameterHandler\Text::convertOptions
     */
    public function testConvertOptions()
    {
        $handler = new Text();
        $parameter = new TextParameter();

        self::assertEquals(array(), $handler->convertOptions($parameter));
    }
}
