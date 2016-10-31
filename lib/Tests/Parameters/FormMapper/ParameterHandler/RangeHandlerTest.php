<?php

namespace Netgen\BlockManager\Tests\Parameters\FormMapper\ParameterHandler;

use Netgen\BlockManager\Parameters\Parameter\Range as RangeParameter;
use Netgen\BlockManager\Parameters\FormMapper\ParameterHandler\RangeHandler;
use Symfony\Component\Form\Extension\Core\Type\RangeType;
use PHPUnit\Framework\TestCase;

class RangeTestHandler extends TestCase
{
    /**
     * @var \Netgen\BlockManager\Parameters\FormMapper\ParameterHandler\RangeHandler
     */
    protected $parameterHandler;

    public function setUp()
    {
        $this->parameterHandler = new RangeHandler();
    }

    /**
     * @covers \Netgen\BlockManager\Parameters\FormMapper\ParameterHandler\RangeHandler::getFormType
     */
    public function testGetFormType()
    {
        $this->assertEquals(RangeType::class, $this->parameterHandler->getFormType());
    }

    /**
     * @covers \Netgen\BlockManager\Parameters\FormMapper\ParameterHandler\RangeHandler::convertOptions
     */
    public function testConvertOptions()
    {
        $parameter = new RangeParameter(
            array(
                'min' => 3,
                'max' => 5,
            )
        );

        $this->assertEquals(
            array(
                'attr' => array(
                    'min' => 3,
                    'max' => 5,
                ),
            ),
            $this->parameterHandler->convertOptions($parameter)
        );
    }
}
