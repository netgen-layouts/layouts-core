<?php

namespace Netgen\BlockManager\Tests\Parameters\FormMapper\ParameterHandler;

use Netgen\BlockManager\Parameters\ParameterDefinition\Range as RangeParameterDefinition;
use Netgen\BlockManager\Parameters\FormMapper\ParameterHandler\Range;
use Symfony\Component\Form\Extension\Core\Type\RangeType;
use PHPUnit\Framework\TestCase;

class RangeTest extends TestCase
{
    /**
     * @var \Netgen\BlockManager\Parameters\FormMapper\ParameterHandler\Range
     */
    protected $parameterHandler;

    public function setUp()
    {
        $this->parameterHandler = new Range();
    }

    /**
     * @covers \Netgen\BlockManager\Parameters\FormMapper\ParameterHandler\Range::getFormType
     */
    public function testGetFormType()
    {
        $this->assertEquals(RangeType::class, $this->parameterHandler->getFormType());
    }

    /**
     * @covers \Netgen\BlockManager\Parameters\FormMapper\ParameterHandler\Range::convertOptions
     */
    public function testConvertOptions()
    {
        $parameter = new RangeParameterDefinition(
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
