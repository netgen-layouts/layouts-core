<?php

namespace Netgen\BlockManager\Tests\BlockDefinition\Parameter;

use Netgen\BlockManager\BlockDefinition\Parameter\Select;

class SelectTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @covers \Netgen\BlockManager\BlockDefinition\Parameter\Select::getType
     * @covers \Netgen\BlockManager\BlockDefinition\Parameter\Select::getFormType
     * @covers \Netgen\BlockManager\BlockDefinition\Parameter\Select::mapFormTypeOptions
     */
    public function testParameter()
    {
        $attributes = array(
            'multiple' => false,
            'options' => array(
                'option1' => 'Option 1',
                'option2' => 'Option 2',
            ),
        );

        $parameter = $this->getParameter($attributes);

        self::assertEquals(
            'choice',
            $parameter->getFormType()
        );
        self::assertEquals(
            array(
                'multiple' => $attributes['multiple'],
                'choices' => $attributes['options'],
            ),
            $parameter->mapFormTypeOptions()
        );
    }

    /**
     * @covers \Netgen\BlockManager\BlockDefinition\Parameter\Select::getAttributes
     * @covers \Netgen\BlockManager\BlockDefinition\Parameter\Select::configureOptions
     * @dataProvider validAttributesProvider
     *
     * @param array $attributes
     * @param array $resolvedAttributes
     */
    public function testValidAttributes($attributes, $resolvedAttributes)
    {
        $parameter = $this->getParameter($attributes);
        self::assertEquals($resolvedAttributes, $parameter->getAttributes());
    }

    /**
     * @covers \Netgen\BlockManager\BlockDefinition\Parameter\Select::getAttributes
     * @covers \Netgen\BlockManager\BlockDefinition\Parameter\Select::configureOptions
     * @expectedException \Symfony\Component\OptionsResolver\Exception\InvalidArgumentException
     * @dataProvider invalidAttributesProvider
     *
     * @param array $attributes
     */
    public function testInvalidAttributes($attributes)
    {
        if ($attributes === null) {
            $this->markTestSkipped('This parameter has no invalid values.');
        }

        $parameter = $this->getParameter($attributes);
    }

    /**
     * Returns the parameter under test.
     *
     * @param array $attributes
     *
     * @return \Netgen\BlockManager\BlockDefinition\Parameter\Select
     */
    public function getParameter($attributes)
    {
        return new Select('Test value', $attributes);
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
                    'multiple' => false,
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
            ),
            array(
                array(
                    'options' => array(),
                ),
            ),
            array(
                array(
                    'options' => array(1, 2, 3),
                ),
            ),
            array(
                array(
                    'undefined_value' => 'Value',
                ),
            ),
            array(
                array(),
            ),
        );
    }
}
