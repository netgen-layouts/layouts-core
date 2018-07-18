<?php

declare(strict_types=1);

namespace Netgen\BlockManager\Tests\Parameters\Form\Mapper;

use Netgen\BlockManager\Parameters\Form\Mapper\NumberMapper;
use Netgen\BlockManager\Parameters\ParameterDefinition;
use Netgen\BlockManager\Parameters\ParameterType\NumberType as NumberParameterType;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Form\Extension\Core\Type\NumberType;

final class NumberMapperTest extends TestCase
{
    /**
     * @var \Netgen\BlockManager\Parameters\Form\Mapper\NumberMapper
     */
    private $mapper;

    public function setUp(): void
    {
        $this->mapper = new NumberMapper();
    }

    /**
     * @covers \Netgen\BlockManager\Parameters\Form\Mapper\NumberMapper::getFormType
     */
    public function testGetFormType(): void
    {
        $this->assertSame(NumberType::class, $this->mapper->getFormType());
    }

    /**
     * @covers \Netgen\BlockManager\Parameters\Form\Mapper\NumberMapper::mapOptions
     */
    public function testMapOptions(): void
    {
        $parameterDefinition = ParameterDefinition::fromArray(
            [
                'type' => new NumberParameterType(),
                'options' => [
                    'scale' => 6,
                ],
            ]
        );

        $this->assertSame(
            [
                'scale' => 6,
            ],
            $this->mapper->mapOptions($parameterDefinition)
        );
    }
}
