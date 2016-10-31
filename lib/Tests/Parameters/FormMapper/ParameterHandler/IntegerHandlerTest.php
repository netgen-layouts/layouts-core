<?php

namespace Netgen\BlockManager\Tests\Parameters\FormMapper\ParameterHandler;

use Netgen\BlockManager\Parameters\FormMapper\ParameterHandler\IntegerHandler;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use PHPUnit\Framework\TestCase;

class IntegerTestHandler extends TestCase
{
    /**
     * @var \Netgen\BlockManager\Parameters\FormMapper\ParameterHandler\IntegerHandler
     */
    protected $parameterHandler;

    public function setUp()
    {
        $this->parameterHandler = new IntegerHandler();
    }

    /**
     * @covers \Netgen\BlockManager\Parameters\FormMapper\ParameterHandler\IntegerHandler::getFormType
     */
    public function testGetFormType()
    {
        $this->assertEquals(IntegerType::class, $this->parameterHandler->getFormType());
    }
}
