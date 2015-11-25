<?php

namespace Netgen\BlockManager\Tests\BlockDefinition\Parameter;

use Netgen\BlockManager\BlockDefinition\Parameter\TextArea;

class TextAreaTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @covers \Netgen\BlockManager\BlockDefinition\Parameter\TextArea::getType
     * @covers \Netgen\BlockManager\BlockDefinition\Parameter\TextArea::getFormType
     * @covers \Netgen\BlockManager\BlockDefinition\Parameter\TextArea::configureOptions
     * @covers \Netgen\BlockManager\BlockDefinition\Parameter\TextArea::mapFormTypeOptions
     */
    public function testParameter()
    {
        $parameter = $this->getParameter(array());

        self::assertEquals('textarea', $parameter->getType());
        self::assertEquals(array(), $parameter->mapFormTypeOptions());
        self::assertEquals(
            'Symfony\Component\Form\Extension\Core\Type\TextareaType',
            $parameter->getFormType()
        );
    }

    /**
     * @covers \Netgen\BlockManager\BlockDefinition\Parameter\TextArea::getAttributes
     * @covers \Netgen\BlockManager\BlockDefinition\Parameter\TextArea::configureOptions
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
     * @covers \Netgen\BlockManager\BlockDefinition\Parameter\TextArea::getAttributes
     * @covers \Netgen\BlockManager\BlockDefinition\Parameter\TextArea::configureOptions
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
     * @return \Netgen\BlockManager\BlockDefinition\Parameter\TextArea
     */
    public function getParameter($attributes)
    {
        return new TextArea('Test value', $attributes);
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
                array(),
                array(),
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
                    'undefined_value' => 'Value',
                ),
            ),
        );
    }
}
