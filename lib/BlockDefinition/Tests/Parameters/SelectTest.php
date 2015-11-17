<?php

namespace Netgen\BlockManager\BlockDefinition\Tests\Parameters;

use Netgen\BlockManager\BlockDefinition\Parameters\Select;

class SelectTest extends ParameterTest
{
    /**
     * @covers \Netgen\BlockManager\BlockDefinition\Parameters\Select::getType
     * @covers \Netgen\BlockManager\BlockDefinition\Parameters\Select::getFormType
     * @covers \Netgen\BlockManager\BlockDefinition\Parameters\Select::configureOptions
     * @covers \Netgen\BlockManager\BlockDefinition\Parameters\Select::mapFormTypeOptions
     */
    public function testParameter()
    {
        $attributes = array(
            'options' => array(
                'option1' => 'Option 1',
                'option2' => 'Option 2',
            ),
        );

        $parameter = $this->getParameter($attributes);

        self::assertEquals('select', $parameter->getType());
        self::assertEquals('choice', $parameter->getFormType());
        self::assertEquals(
            array(
                'multiple' => false,
                'choices' => $attributes['options'],
            ),
            $parameter->mapFormTypeOptions()
        );
    }

    public function getParameter($attributes)
    {
        return new Select('test', 'Test', $attributes, 'Test value');
    }

    /**
     * Provider for testing valid parameter attributes.
     *
     * @return array
     */
    public function validAttributesProvider()
    {
        return array(
            array(
                array(
                    'options' => array(
                        'option1' => 'Option 1',
                        'option2' => 'Option 2',
                    ),
                ),
                array(
                    'multiple' => false,
                    'options' => array(
                        'option1' => 'Option 1',
                        'option2' => 'Option 2',
                    ),
                ),
            ),
            array(
                array(
                    'multiple' => true,
                    'options' => array(
                        'option1' => 'Option 1',
                        'option2' => 'Option 2',
                    ),
                ),
                array(
                    'multiple' => true,
                    'options' => array(
                        'option1' => 'Option 1',
                        'option2' => 'Option 2',
                    ),
                ),
            ),
        );
    }

    /**
     * Provider for testing invalid parameter attributes.
     *
     * @return array
     */
    public function invalidAttributesProvider()
    {
        return array(
            array(
                array(
                    'multiple' => 'true',
                    'options' => array(
                        'option1' => 'Option 1',
                        'option2' => 'Option 2',
                    ),
                ),
            ),
            array(
                array(
                    'options' => 'options',
                ),
                array(
                    'options' => array(),
                ),
                array(
                    'options' => array(1, 2, 3),
                ),
                array(
                    array(),
                ),
            ),
        );
    }
}
