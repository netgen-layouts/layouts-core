<?php

declare(strict_types=1);

namespace Netgen\Layouts\Tests\Parameters\Form\Mapper;

use Netgen\Layouts\Parameters\Form\Mapper\RangeMapper;
use Netgen\Layouts\Parameters\ParameterDefinition;
use Netgen\Layouts\Parameters\ParameterType\RangeType as RangeParameterType;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Form\Extension\Core\Type\RangeType;

#[CoversClass(RangeMapper::class)]
final class RangeMapperTest extends TestCase
{
    private RangeMapper $mapper;

    protected function setUp(): void
    {
        $this->mapper = new RangeMapper();
    }

    public function testGetFormType(): void
    {
        self::assertSame(RangeType::class, $this->mapper->getFormType());
    }

    public function testMapOptions(): void
    {
        $parameterDefinition = ParameterDefinition::fromArray(
            [
                'name' => 'name',
                'type' => new RangeParameterType(),
                'options' => ['min' => 3, 'max' => 5],
            ],
        );

        self::assertSame(
            [
                'attr' => [
                    'min' => 3,
                    'max' => 5,
                ],
            ],
            $this->mapper->mapOptions($parameterDefinition),
        );
    }
}
