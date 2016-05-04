<?php

namespace Netgen\BlockManager\Tests\Parameters\FormMapper\ParameterHandler;

use Netgen\BlockManager\Parameters\FormMapper\ParameterHandler\Text;
use Netgen\BlockManager\Parameters\Parameter\Text as TextParameter;

class TextTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var \Netgen\BlockManager\Parameters\FormMapper\ParameterHandler\Hidden
     */
    protected $handler;

    public function setUp()
    {
        $this->handler = new Text();
    }

    /**
     * @covers \Netgen\BlockManager\Parameters\FormMapper\ParameterHandler\Text::getFormType
     */
    public function testGetFormType()
    {
        self::assertEquals('text', $this->handler->getFormType());
    }

    /**
     * @covers \Netgen\BlockManager\Parameters\FormMapper\ParameterHandler\Text::convertOptions
     */
    public function testConvertOptions()
    {
        self::assertEquals(array(), $this->handler->convertOptions(new TextParameter()));
    }
}
