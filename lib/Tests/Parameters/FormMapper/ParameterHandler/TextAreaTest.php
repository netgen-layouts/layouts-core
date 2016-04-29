<?php

namespace Netgen\BlockManager\Tests\Parameters\FormMapper\ParameterHandler;

use Netgen\BlockManager\Parameters\FormMapper\ParameterHandler\TextArea;
use Netgen\BlockManager\Parameters\Parameter\TextArea as TextAreaParameter;

class TextAreaTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @covers \Netgen\BlockManager\Parameters\FormMapper\ParameterHandler\TextArea::getFormType
     */
    public function testGetFormType()
    {
        $handler = new TextArea();

        self::assertEquals('textarea', $handler->getFormType());
    }

    /**
     * @covers \Netgen\BlockManager\Parameters\FormMapper\ParameterHandler\TextArea::getFormType
     */
    public function testConvertOptions()
    {
        $handler = new TextArea();
        $parameter = new TextAreaParameter();

        self::assertEquals(array(), $handler->convertOptions($parameter));
    }
}
