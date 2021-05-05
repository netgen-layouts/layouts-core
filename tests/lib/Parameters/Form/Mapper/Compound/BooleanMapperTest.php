<?php

declare(strict_types=1);

namespace Netgen\Layouts\Tests\Parameters\Form\Mapper\Compound;

use Netgen\Layouts\Parameters\Form\Mapper\Compound\BooleanMapper;
use Netgen\Layouts\Parameters\Form\Type\CompoundBooleanType;
use Netgen\Layouts\Parameters\ParameterDefinition;
use Netgen\Layouts\Parameters\ParameterType\Compound\BooleanType;
use PHPUnit\Framework\TestCase;

final class BooleanMapperTest extends TestCase
{
    private BooleanMapper $mapper;

    protected function setUp(): void
    {
        $this->mapper = new BooleanMapper();
    }

    /**
     * @covers \Netgen\Layouts\Parameters\Form\Mapper\Compound\BooleanMapper::getFormType
     */
    public function testGetFormType(): void
    {
        self::assertSame(CompoundBooleanType::class, $this->mapper->getFormType());
    }

    /**
     * @covers \Netgen\Layouts\Parameters\Form\Mapper\Compound\BooleanMapper::mapOptions
     */
    public function testMapOptions(): void
    {
        self::assertSame(
            [
                'mapped' => false,
                'reverse' => true,
            ],
            $this->mapper->mapOptions(
                ParameterDefinition::fromArray(
                    [
                        'name' => 'name',
                        'type' => new BooleanType(),
                        'isRequired' => false,
                        'options' => ['reverse' => true],
                    ],
                ),
            ),
        );
    }
}
