<?php

declare(strict_types=1);

namespace Netgen\BlockManager\Tests\Parameters\Form\Mapper\Compound;

use Netgen\BlockManager\Parameters\Form\Mapper\Compound\BooleanMapper;
use Netgen\BlockManager\Parameters\Form\Type\CompoundBooleanType;
use Netgen\BlockManager\Parameters\ParameterDefinition;
use Netgen\BlockManager\Parameters\ParameterType\Compound\BooleanType;
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
            [
                'mapped' => false,
                'reverse' => true,
            ],
            $this->mapper->mapOptions(
                new ParameterDefinition(
                    [
                        'name' => 'name',
                        'type' => new BooleanType(),
                        'options' => ['reverse' => true],
                    ]
                )
            )
        );
    }
}
