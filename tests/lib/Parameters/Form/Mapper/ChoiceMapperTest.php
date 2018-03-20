<?php

namespace Netgen\BlockManager\Tests\Parameters\Form\Mapper;

use Netgen\BlockManager\Form\ChoicesAsValuesTrait;
use Netgen\BlockManager\Parameters\Form\Mapper\ChoiceMapper;
use Netgen\BlockManager\Parameters\ParameterType\ChoiceType as ChoiceParameterType;
use Netgen\BlockManager\Tests\Parameters\Stubs\ParameterDefinition;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;

final class ChoiceMapperTest extends TestCase
{
    use ChoicesAsValuesTrait;

    /**
     * @var \Netgen\BlockManager\Parameters\Form\Mapper\ChoiceMapper
     */
    private $mapper;

    public function setUp()
    {
        $this->mapper = new ChoiceMapper();
    }

    /**
     * @covers \Netgen\BlockManager\Parameters\Form\Mapper\ChoiceMapper::getFormType
     */
    public function testGetFormType()
    {
        $this->assertEquals(ChoiceType::class, $this->mapper->getFormType());
    }

    /**
     * @covers \Netgen\BlockManager\Parameters\Form\Mapper\ChoiceMapper::mapOptions
     */
    public function testMapOptions()
    {
        $parameterDefinition = new ParameterDefinition(
            array(
                'name' => 'name',
                'type' => new ChoiceParameterType(),
                'options' => array(
                    'multiple' => true,
                    'expanded' => true,
                    'options' => array(
                        'Option 1' => 'option1',
                        'Option 2' => 'option2',
                    ),
                ),
            )
        );

        $this->assertEquals(
            array(
                'multiple' => true,
                'expanded' => true,
                'choices' => array(
                    'Option 1' => 'option1',
                    'Option 2' => 'option2',
                ),
            ) + $this->getChoicesAsValuesOption(),
            $this->mapper->mapOptions($parameterDefinition)
        );
    }

    /**
     * @covers \Netgen\BlockManager\Parameters\Form\Mapper\ChoiceMapper::mapOptions
     */
    public function testMapOptionsWithClosure()
    {
        $parameterDefinition = new ParameterDefinition(
            array(
                'name' => 'name',
                'type' => new ChoiceParameterType(),
                'options' => array(
                    'multiple' => true,
                    'expanded' => true,
                    'options' => function () {
                        return array(
                            'Option 1' => 'option1',
                            'Option 2' => 'option2',
                        );
                    },
                ),
            )
        );

        $this->assertEquals(
            array(
                'multiple' => true,
                'expanded' => true,
                'choices' => array(
                    'Option 1' => 'option1',
                    'Option 2' => 'option2',
                ),
            ) + $this->getChoicesAsValuesOption(),
            $this->mapper->mapOptions($parameterDefinition)
        );
    }
}
