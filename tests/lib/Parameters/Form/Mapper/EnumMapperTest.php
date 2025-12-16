<?php

declare(strict_types=1);

namespace Netgen\Layouts\Tests\Parameters\Form\Mapper;

use Netgen\Layouts\Parameters\Form\Mapper\EnumMapper;
use Netgen\Layouts\Parameters\ParameterDefinition;
use Netgen\Layouts\Parameters\ParameterType\EnumType as EnumParameterType;
use Netgen\Layouts\Tests\Parameters\Stubs\EnumStub;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Form\Extension\Core\Type\EnumType;

#[CoversClass(EnumMapper::class)]
final class EnumMapperTest extends TestCase
{
    private EnumMapper $mapper;

    protected function setUp(): void
    {
        $this->mapper = new EnumMapper();
    }

    public function testGetFormType(): void
    {
        self::assertSame(EnumType::class, $this->mapper->getFormType());
    }

    public function testMapOptions(): void
    {
        $parameterDefinition = ParameterDefinition::fromArray(
            [
                'name' => 'name',
                'type' => new EnumParameterType(),
                'isRequired' => false,
                'options' => [
                    'class' => EnumStub::class,
                    'multiple' => true,
                    'expanded' => true,
                    'option_label_prefix' => null,
                ],
            ],
        );

        self::assertSame(
            [
                'class' => EnumStub::class,
                'multiple' => true,
                'expanded' => true,
            ],
            $this->mapper->mapOptions($parameterDefinition),
        );
    }
}
