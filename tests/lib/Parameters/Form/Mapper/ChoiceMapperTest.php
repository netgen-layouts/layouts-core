<?php

declare(strict_types=1);

namespace Netgen\BlockManager\Tests\Parameters\Form\Mapper;

use Netgen\BlockManager\Form\ChoicesAsValuesTrait;
use Netgen\BlockManager\Parameters\Form\Mapper\ChoiceMapper;
use Netgen\BlockManager\Parameters\ParameterDefinition;
use Netgen\BlockManager\Parameters\ParameterType\ChoiceType as ChoiceParameterType;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;

final class ChoiceMapperTest extends TestCase
{
    use ChoicesAsValuesTrait;

    /**
     * @var \Netgen\BlockManager\Parameters\Form\Mapper\ChoiceMapper
     */
    private $mapper;

    public function setUp(): void
    {
        $this->mapper = new ChoiceMapper();
    }

    /**
     * @covers \Netgen\BlockManager\Parameters\Form\Mapper\ChoiceMapper::getFormType
     */
    public function testGetFormType(): void
    {
        $this->assertSame(ChoiceType::class, $this->mapper->getFormType());
    }

    /**
     * @covers \Netgen\BlockManager\Parameters\Form\Mapper\ChoiceMapper::mapOptions
     */
    public function testMapOptions(): void
    {
        $parameterDefinition = ParameterDefinition::fromArray(
            [
                'name' => 'name',
                'type' => new ChoiceParameterType(),
                'options' => [
                    'multiple' => true,
                    'expanded' => true,
                    'options' => [
                        'Option 1' => 'option1',
                        'Option 2' => 'option2',
                    ],
                ],
            ]
        );

        $this->assertSame(
            [
                'multiple' => true,
                'expanded' => true,
                'choices' => [
                    'Option 1' => 'option1',
                    'Option 2' => 'option2',
                ],
            ] + $this->getChoicesAsValuesOption(),
            $this->mapper->mapOptions($parameterDefinition)
        );
    }

    /**
     * @covers \Netgen\BlockManager\Parameters\Form\Mapper\ChoiceMapper::mapOptions
     */
    public function testMapOptionsWithClosure(): void
    {
        $parameterDefinition = ParameterDefinition::fromArray(
            [
                'name' => 'name',
                'type' => new ChoiceParameterType(),
                'options' => [
                    'multiple' => true,
                    'expanded' => true,
                    'options' => function (): array {
                        return [
                            'Option 1' => 'option1',
                            'Option 2' => 'option2',
                        ];
                    },
                ],
            ]
        );

        $this->assertSame(
            [
                'multiple' => true,
                'expanded' => true,
                'choices' => [
                    'Option 1' => 'option1',
                    'Option 2' => 'option2',
                ],
            ] + $this->getChoicesAsValuesOption(),
            $this->mapper->mapOptions($parameterDefinition)
        );
    }
}
