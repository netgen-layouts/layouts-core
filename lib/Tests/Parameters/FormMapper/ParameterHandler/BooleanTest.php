<?php

namespace Netgen\BlockManager\Tests\Parameters\FormMapper\ParameterHandler;

use Netgen\BlockManager\Parameters\FormMapper\ParameterHandler\Boolean;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use PHPUnit\Framework\TestCase;

class BooleanTest extends TestCase
{
    /**
     * @var \Netgen\BlockManager\Parameters\FormMapper\ParameterHandler\Boolean
     */
    protected $parameterHandler;

    public function setUp()
    {
        $this->parameterHandler = new Boolean();
    }

    /**
     * @covers \Netgen\BlockManager\Parameters\FormMapper\ParameterHandler\Boolean::getFormType
     */
    public function testGetFormType()
    {
        $this->assertEquals(CheckboxType::class, $this->parameterHandler->getFormType());
    }
}
