<?php

namespace Netgen\BlockManager\Tests\Parameters\FormMapper\ParameterHandler;

use Netgen\BlockManager\Parameters\FormMapper\ParameterHandler\Number;
use Netgen\BlockManager\Parameters\FormMapper\ParameterHandler;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use PHPUnit\Framework\TestCase;

class NumberTest extends TestCase
{
    /**
     * @var \Netgen\BlockManager\Parameters\FormMapper\ParameterHandler\Number
     */
    protected $parameterHandler;

    public function setUp()
    {
        $this->parameterHandler = new Number();
    }

    /**
     * @covers \Netgen\BlockManager\Parameters\FormMapper\ParameterHandler\Number::getFormType
     */
    public function testGetFormType()
    {
        self::assertEquals(NumberType::class, $this->parameterHandler->getFormType());
    }
}
