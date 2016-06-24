<?php

namespace Netgen\BlockManager\Tests\Parameters\FormMapper\ParameterHandler;

use Netgen\BlockManager\Parameters\Parameter\Range as RangeParameter;
use Netgen\BlockManager\Parameters\FormMapper\ParameterHandler\Range;
use Netgen\BlockManager\Parameters\FormMapper\ParameterHandler;
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
        self::assertEquals(RangeType::class, $this->parameterHandler->getFormType());
    }

    /**
     * @covers \Netgen\BlockManager\Parameters\FormMapper\ParameterHandler\Range::convertOptions
     */
    public function testConvertOptions()
    {
        $parameter = new RangeParameter(
            array(
                'min' => 3,
                'max' => 5,
            )
        );

        self::assertEquals(
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
