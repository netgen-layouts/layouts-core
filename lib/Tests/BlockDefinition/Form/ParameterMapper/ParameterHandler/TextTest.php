<?php

namespace Netgen\BlockManager\Tests\BlockDefinition\Form\ParameterMapper\ParameterHandler;

use Netgen\BlockManager\BlockDefinition\Form\ParameterMapper\ParameterHandler\Text;
use Netgen\BlockManager\BlockDefinition\Parameter\Text as TextParameter;

class TextTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @covers \Netgen\BlockManager\BlockDefinition\Form\ParameterMapper\ParameterHandler\Text::getFormType
     */
    public function testGetFormType()
    {
        $handler = new Text();

        self::assertEquals('text', $handler->getFormType());
    }

    /**
     * @covers \Netgen\BlockManager\BlockDefinition\Form\ParameterMapper\ParameterHandler\Text::getFormType
     */
    public function testConvertOptions()
    {
        $handler = new Text();
        $parameter = new TextParameter();

        self::assertEquals(array(), $handler->convertOptions($parameter));
    }
}
