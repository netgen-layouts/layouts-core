<?php

namespace Netgen\BlockManager\Tests\Parameters\FormMapper\ParameterHandler;

use Netgen\BlockManager\Parameters\FormMapper\ParameterHandler\Hidden;
use Netgen\BlockManager\Parameters\Parameter\Hidden as HiddenParameter;

class HiddenTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @covers \Netgen\BlockManager\Parameters\FormMapper\ParameterHandler\Hidden::getFormType
     */
    public function testGetFormType()
    {
        $handler = new Hidden();

        self::assertEquals('hidden', $handler->getFormType());
    }

    /**
     * @covers \Netgen\BlockManager\Parameters\FormMapper\ParameterHandler\Hidden::getFormType
     */
    public function testConvertOptions()
    {
        $handler = new Hidden();
        $parameter = new HiddenParameter();

        self::assertEquals(array(), $handler->convertOptions($parameter));
    }
}
