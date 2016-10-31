<?php

namespace Netgen\BlockManager\Tests\Parameters\FormMapper\ParameterHandler;

use Netgen\BlockManager\Parameters\FormMapper\ParameterHandler\BooleanHandler;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use PHPUnit\Framework\TestCase;

class BooleanTestHandler extends TestCase
{
    /**
     * @var \Netgen\BlockManager\Parameters\FormMapper\ParameterHandler\BooleanHandler
     */
    protected $parameterHandler;

    public function setUp()
    {
        $this->parameterHandler = new BooleanHandler();
    }

    /**
     * @covers \Netgen\BlockManager\Parameters\FormMapper\ParameterHandler\BooleanHandler::getFormType
     */
    public function testGetFormType()
    {
        $this->assertEquals(CheckboxType::class, $this->parameterHandler->getFormType());
    }
}
