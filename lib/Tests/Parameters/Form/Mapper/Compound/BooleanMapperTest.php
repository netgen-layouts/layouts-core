<?php

namespace Netgen\BlockManager\Tests\Parameters\Form\Mapper\Compound;

use Netgen\BlockManager\Parameters\Form\Type\CompoundBooleanType;
use Netgen\BlockManager\Parameters\Parameter\Compound\Boolean;
use Netgen\BlockManager\Parameters\Form\Mapper\Compound\BooleanMapper;
use PHPUnit\Framework\TestCase;

class BooleanMapperTest extends TestCase
{
    /**
     * @var \Netgen\BlockManager\Parameters\Form\Mapper\Compound\BooleanMapper
     */
    protected $mapper;

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
                'reverse' => true,
                'parameters' => array(),
                'label_prefix' => 'label',
                'property_path_prefix' => 'parameters',
            ),
            $this->mapper->mapOptions(
                new Boolean(array(), array('reverse' => true), true),
                'name',
                array(
                    'label_prefix' => 'label',
                    'property_path_prefix' => 'parameters',
                )
            )
        );
    }
}
