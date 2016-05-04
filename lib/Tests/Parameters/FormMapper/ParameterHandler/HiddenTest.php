<?php

namespace Netgen\BlockManager\Tests\Parameters\FormMapper\ParameterHandler;

use Netgen\BlockManager\Parameters\FormMapper\ParameterHandler\Hidden;
use Netgen\BlockManager\Parameters\Parameter\Hidden as HiddenParameter;

class HiddenTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var \Netgen\BlockManager\Parameters\FormMapper\ParameterHandler\Hidden
     */
    protected $handler;

    public function setUp()
    {
        $this->handler = new Hidden();
    }

    /**
     * @covers \Netgen\BlockManager\Parameters\FormMapper\ParameterHandler\Hidden::getFormType
     */
    public function testGetFormType()
    {
        self::assertEquals('hidden', $this->handler->getFormType());
    }

    /**
     * @covers \Netgen\BlockManager\Parameters\FormMapper\ParameterHandler\Hidden::convertOptions
     */
    public function testConvertOptions()
    {
        self::assertEquals(array(), $this->handler->convertOptions(new HiddenParameter()));
    }
}
