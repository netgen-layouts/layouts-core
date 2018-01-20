<?php

namespace Netgen\BlockManager\Tests\Parameters\Form\Mapper\Compound;

use Netgen\BlockManager\Parameters\Form\Mapper\Compound\BooleanMapper;
use Netgen\BlockManager\Parameters\Form\Type\CompoundBooleanType;
use Netgen\BlockManager\Parameters\ParameterType\Compound\BooleanType;
use Netgen\BlockManager\Tests\Parameters\Stubs\Parameter;
use PHPUnit\Framework\TestCase;

final class BooleanMapperTest extends TestCase
{
    /**
     * @var \Netgen\BlockManager\Parameters\Form\Mapper\Compound\BooleanMapper
     */
    private $mapper;

    public function setUp()
    {
        $this->mapper = new BooleanMapper();
    }

    /**
     * @covers \Netgen\BlockManager\Parameters\Form\Mapper\Compound\BooleanMapper::getFormType
     */
    public function testGetFormType()
    {
        $this->assertEquals(CompoundBooleanType::class, $this->mapper->getFormType());
    }

    /**
     * @covers \Netgen\BlockManager\Parameters\Form\Mapper\Compound\BooleanMapper::mapOptions
     */
    public function testMapOptions()
    {
        $this->assertEquals(
            array(
                'mapped' => false,
                'reverse' => true,
            ),
            $this->mapper->mapOptions(
                new Parameter(
                    array(
                        'name' => 'name',
                        'type' => new BooleanType(),
                        'options' => array('reverse' => true),
                    )
                )
            )
        );
    }
}
