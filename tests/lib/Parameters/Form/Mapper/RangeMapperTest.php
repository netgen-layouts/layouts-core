<?php

declare(strict_types=1);

namespace Netgen\BlockManager\Tests\Parameters\Form\Mapper;

use Netgen\BlockManager\Parameters\Form\Mapper\RangeMapper;
use Netgen\BlockManager\Parameters\ParameterDefinition;
use Netgen\BlockManager\Parameters\ParameterType\RangeType as RangeParameterType;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Form\Extension\Core\Type\RangeType;

final class RangeMapperTest extends TestCase
{
    /**
     * @var \Netgen\BlockManager\Parameters\Form\Mapper\RangeMapper
     */
    private $mapper;

    public function setUp(): void
    {
        $this->mapper = new RangeMapper();
    }

    /**
     * @covers \Netgen\BlockManager\Parameters\Form\Mapper\RangeMapper::getFormType
     */
    public function testGetFormType(): void
    {
        $this->assertEquals(RangeType::class, $this->mapper->getFormType());
    }

    /**
     * @covers \Netgen\BlockManager\Parameters\Form\Mapper\RangeMapper::mapOptions
     */
    public function testMapOptions(): void
    {
        $parameterDefinition = new ParameterDefinition(
            [
                'name' => 'name',
                'type' => new RangeParameterType(),
                'options' => ['min' => 3, 'max' => 5],
            ]
        );

        $this->assertEquals(
            [
                'attr' => [
                    'min' => 3,
                    'max' => 5,
                ],
            ],
            $this->mapper->mapOptions($parameterDefinition)
        );
    }
}
