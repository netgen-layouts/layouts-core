<?php

declare(strict_types=1);

namespace Netgen\Layouts\Tests\Parameters\Form\Mapper;

use Netgen\Layouts\Parameters\Form\Mapper\NumberMapper;
use Netgen\Layouts\Parameters\ParameterDefinition;
use Netgen\Layouts\Parameters\ParameterType\NumberType as NumberParameterType;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Form\Extension\Core\Type\NumberType;

final class NumberMapperTest extends TestCase
{
    private NumberMapper $mapper;

    protected function setUp(): void
    {
        $this->mapper = new NumberMapper();
    }

    /**
     * @covers \Netgen\Layouts\Parameters\Form\Mapper\NumberMapper::getFormType
     */
    public function testGetFormType(): void
    {
        self::assertSame(NumberType::class, $this->mapper->getFormType());
    }

    /**
     * @covers \Netgen\Layouts\Parameters\Form\Mapper\NumberMapper::mapOptions
     */
    public function testMapOptions(): void
    {
        $parameterDefinition = ParameterDefinition::fromArray(
            [
                'type' => new NumberParameterType(),
                'options' => [
                    'scale' => 6,
                ],
            ],
        );

        self::assertSame(
            [
                'scale' => 6,
            ],
            $this->mapper->mapOptions($parameterDefinition),
        );
    }
}
