<?php

namespace Netgen\BlockManager\Tests\BlockDefinition\Form\ParameterMapper\ParameterHandler;

use Netgen\BlockManager\BlockDefinition\Form\ParameterMapper\ParameterHandler\Hidden;
use Netgen\BlockManager\BlockDefinition\Parameter\Hidden as HiddenParameter;

class HiddenTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @covers \Netgen\BlockManager\BlockDefinition\Form\ParameterMapper\ParameterHandler\Hidden::getFormType
     */
    public function testGetFormType()
    {
        $handler = new Hidden();

        self::assertEquals('hidden', $handler->getFormType());
    }

    /**
     * @covers \Netgen\BlockManager\BlockDefinition\Form\ParameterMapper\ParameterHandler\Hidden::getFormType
     */
    public function testConvertOptions()
    {
        $handler = new Hidden();
        $parameter = new HiddenParameter();

        self::assertEquals(array(), $handler->convertOptions($parameter));
    }
}
