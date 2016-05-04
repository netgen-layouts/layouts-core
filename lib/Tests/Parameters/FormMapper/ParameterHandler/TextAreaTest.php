<?php

namespace Netgen\BlockManager\Tests\Parameters\FormMapper\ParameterHandler;

use Netgen\BlockManager\Parameters\FormMapper\ParameterHandler\TextArea;
use Netgen\BlockManager\Parameters\Parameter\TextArea as TextAreaParameter;

class TextAreaTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var \Netgen\BlockManager\Parameters\FormMapper\ParameterHandler\Hidden
     */
    protected $handler;

    public function setUp()
    {
        $this->handler = new TextArea();
    }

    /**
     * @covers \Netgen\BlockManager\Parameters\FormMapper\ParameterHandler\TextArea::getFormType
     */
    public function testGetFormType()
    {
        self::assertEquals('textarea', $this->handler->getFormType());
    }

    /**
     * @covers \Netgen\BlockManager\Parameters\FormMapper\ParameterHandler\TextArea::convertOptions
     */
    public function testConvertOptions()
    {
        self::assertEquals(array(), $this->handler->convertOptions(new TextAreaParameter()));
    }
}
