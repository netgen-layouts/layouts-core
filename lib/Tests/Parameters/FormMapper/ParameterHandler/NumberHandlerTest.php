<?php

namespace Netgen\BlockManager\Tests\Parameters\FormMapper\ParameterHandler;

use Netgen\BlockManager\Parameters\FormMapper\ParameterHandler\NumberHandler;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use PHPUnit\Framework\TestCase;

class NumberTestHandler extends TestCase
{
    /**
     * @var \Netgen\BlockManager\Parameters\FormMapper\ParameterHandler\NumberHandler
     */
    protected $parameterHandler;

    public function setUp()
    {
        $this->parameterHandler = new NumberHandler();
    }

    /**
     * @covers \Netgen\BlockManager\Parameters\FormMapper\ParameterHandler\NumberHandler::getFormType
     */
    public function testGetFormType()
    {
        $this->assertEquals(NumberType::class, $this->parameterHandler->getFormType());
    }
}
