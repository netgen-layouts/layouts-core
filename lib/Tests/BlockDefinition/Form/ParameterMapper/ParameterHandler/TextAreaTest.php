<?php

namespace Netgen\BlockManager\Tests\BlockDefinition\Form\ParameterMapper\ParameterHandler;

use Netgen\BlockManager\BlockDefinition\Form\ParameterMapper\ParameterHandler\TextArea;
use Netgen\BlockManager\BlockDefinition\Parameter\TextArea as TextAreaParameter;

class TextAreaTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @covers \Netgen\BlockManager\BlockDefinition\Form\ParameterMapper\ParameterHandler\TextArea::getFormType
     */
    public function testGetFormType()
    {
        $handler = new TextArea();

        self::assertEquals('textarea', $handler->getFormType());
    }

    /**
     * @covers \Netgen\BlockManager\BlockDefinition\Form\ParameterMapper\ParameterHandler\TextArea::getFormType
     */
    public function testConvertOptions()
    {
        $handler = new TextArea();
        $parameter = new TextAreaParameter();

        self::assertEquals(array(), $handler->convertOptions($parameter));
    }
}
