<?php

declare(strict_types=1);

namespace Netgen\Layouts\Tests\Parameters\Form\Mapper;

use Netgen\Layouts\Parameters\Form\Mapper\ChoiceMapper;
use Netgen\Layouts\Parameters\ParameterDefinition;
use Netgen\Layouts\Parameters\ParameterType\ChoiceType as ChoiceParameterType;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;

final class ChoiceMapperTest extends TestCase
{
    private ChoiceMapper $mapper;

    protected function setUp(): void
    {
        $this->mapper = new ChoiceMapper();
    }

    /**
     * @covers \Netgen\Layouts\Parameters\Form\Mapper\ChoiceMapper::getFormType
     */
    public function testGetFormType(): void
    {
        self::assertSame(ChoiceType::class, $this->mapper->getFormType());
    }

    /**
     * @covers \Netgen\Layouts\Parameters\Form\Mapper\ChoiceMapper::mapOptions
     */
    public function testMapOptions(): void
    {
        $parameterDefinition = ParameterDefinition::fromArray(
            [
                'name' => 'name',
                'type' => new ChoiceParameterType(),
                'isRequired' => false,
                'options' => [
                    'multiple' => true,
                    'expanded' => true,
                    'options' => [
                        'Option 1' => 'option1',
                        'Option 2' => 'option2',
                    ],
                ],
            ],
        );

        self::assertSame(
            [
                'multiple' => true,
                'expanded' => true,
                'choices' => [
                    'Option 1' => 'option1',
                    'Option 2' => 'option2',
                ],
            ],
            $this->mapper->mapOptions($parameterDefinition),
        );
    }

    /**
     * @covers \Netgen\Layouts\Parameters\Form\Mapper\ChoiceMapper::mapOptions
     */
    public function testMapOptionsWithClosure(): void
    {
        $parameterDefinition = ParameterDefinition::fromArray(
            [
                'name' => 'name',
                'type' => new ChoiceParameterType(),
                'isRequired' => false,
                'options' => [
                    'multiple' => true,
                    'expanded' => true,
                    'options' => static fn (): array => [
                        'Option 1' => 'option1',
                        'Option 2' => 'option2',
                    ],
                ],
            ],
        );

        self::assertSame(
            [
                'multiple' => true,
                'expanded' => true,
                'choices' => [
                    'Option 1' => 'option1',
                    'Option 2' => 'option2',
                ],
            ],
            $this->mapper->mapOptions($parameterDefinition),
        );
    }
}
