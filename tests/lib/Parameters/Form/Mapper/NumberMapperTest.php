<?php

namespace Netgen\BlockManager\Tests\Parameters\Form\Mapper;

use Netgen\BlockManager\Parameters\Form\Mapper\NumberMapper;
use Netgen\BlockManager\Parameters\Parameter;
use Netgen\BlockManager\Parameters\ParameterType\NumberType as NumberParameterType;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Form\Extension\Core\Type\NumberType;

final class NumberMapperTest extends TestCase
{
    /**
     * @var \Netgen\BlockManager\Parameters\Form\Mapper\NumberMapper
     */
    private $mapper;

    public function setUp()
    {
        $this->mapper = new NumberMapper();
    }

    /**
     * @covers \Netgen\BlockManager\Parameters\Form\Mapper\NumberMapper::getFormType
     */
    public function testGetFormType()
    {
        $this->assertEquals(NumberType::class, $this->mapper->getFormType());
    }

    /**
     * @covers \Netgen\BlockManager\Parameters\Form\Mapper\NumberMapper::mapOptions
     */
    public function testMapOptions()
    {
        $parameter = new Parameter(
            array(
                'type' => new NumberParameterType(),
                'options' => array(
                    'scale' => 6,
                ),
            )
        );

        $this->assertEquals(
            array(
                'scale' => 6,
            ),
            $this->mapper->mapOptions($parameter)
        );
    }
}
