<?php

namespace Netgen\BlockManager\Tests\BlockDefinition\Parameter;

use Netgen\BlockManager\BlockDefinition\Parameter\Text;

class TextTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @covers \Netgen\BlockManager\BlockDefinition\Parameter\Text::getType
     * @covers \Netgen\BlockManager\BlockDefinition\Parameter\Text::getFormType
     * @covers \Netgen\BlockManager\BlockDefinition\Parameter\Text::configureOptions
     * @covers \Netgen\BlockManager\BlockDefinition\Parameter\Text::mapFormTypeOptions
     */
    public function testParameter()
    {
        $parameter = $this->getParameter(array());

        self::assertEquals(array(), $parameter->mapFormTypeOptions());
        self::assertEquals(
            'text',
            $parameter->getFormType()
        );
    }

    /**
     * @covers \Netgen\BlockManager\BlockDefinition\Parameter\Text::getAttributes
     * @covers \Netgen\BlockManager\BlockDefinition\Parameter\Text::configureOptions
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
     * @covers \Netgen\BlockManager\BlockDefinition\Parameter\Text::getAttributes
     * @covers \Netgen\BlockManager\BlockDefinition\Parameter\Text::configureOptions
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
     * @return \Netgen\BlockManager\BlockDefinition\Parameter\Text
     */
    public function getParameter($attributes)
    {
        return new Text('Test value', $attributes);
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
