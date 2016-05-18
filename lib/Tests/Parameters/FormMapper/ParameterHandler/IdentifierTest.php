<?php

namespace Netgen\BlockManager\Tests\Parameters\FormMapper\ParameterHandler;

use Netgen\BlockManager\Parameters\FormMapper\ParameterHandler\Identifier;
use Netgen\BlockManager\Parameters\Parameter\Identifier as IdentifierParameter;

class IdentifierTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var \Netgen\BlockManager\Parameters\FormMapper\ParameterHandler\Hidden
     */
    protected $handler;

    public function setUp()
    {
        $this->handler = new Identifier();
    }

    /**
     * @covers \Netgen\BlockManager\Parameters\FormMapper\ParameterHandler\Identifier::getFormType
     */
    public function testGetFormType()
    {
        self::assertEquals('text', $this->handler->getFormType());
    }

    /**
     * @covers \Netgen\BlockManager\Parameters\FormMapper\ParameterHandler\Identifier::convertOptions
     */
    public function testConvertOptions()
    {
        self::assertEquals(array(), $this->handler->convertOptions(new IdentifierParameter()));
    }
}
