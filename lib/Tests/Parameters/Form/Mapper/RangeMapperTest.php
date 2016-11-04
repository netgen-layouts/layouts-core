<?php

namespace Netgen\BlockManager\Tests\Parameters\FormMapper\ParameterMapper;

use Netgen\BlockManager\Parameters\Parameter\Range as RangeParameter;
use Netgen\BlockManager\Parameters\Form\Mapper\RangeMapper;
use Symfony\Component\Form\Extension\Core\Type\RangeType;
use PHPUnit\Framework\TestCase;

class RangeMapperTest extends TestCase
{
    /**
     * @var \Netgen\BlockManager\Parameters\Form\Mapper\RangeMapper
     */
    protected $mapper;

    public function setUp()
    {
        $this->mapper = new RangeMapper();
    }

    /**
     * @covers \Netgen\BlockManager\Parameters\Form\Mapper\RangeMapper::getFormType
     */
    public function testGetFormType()
    {
        $this->assertEquals(RangeType::class, $this->mapper->getFormType());
    }

    /**
     * @covers \Netgen\BlockManager\Parameters\Form\Mapper\RangeMapper::mapOptions
     */
    public function testMapOptions()
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
            $this->mapper->mapOptions($parameter, 'name', array())
        );
    }
}
