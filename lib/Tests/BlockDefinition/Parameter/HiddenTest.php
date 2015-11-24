<?php

namespace Netgen\BlockManager\Tests\BlockDefinition\Parameter;

use Netgen\BlockManager\BlockDefinition\Parameter\Hidden;

class HiddenTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @covers \Netgen\BlockManager\BlockDefinition\Parameter\Hidden::getType
     * @covers \Netgen\BlockManager\BlockDefinition\Parameter\Hidden::getFormType
     * @covers \Netgen\BlockManager\BlockDefinition\Parameter\Hidden::configureOptions
     * @covers \Netgen\BlockManager\BlockDefinition\Parameter\Hidden::mapFormTypeOptions
     */
    public function testParameter()
    {
        $parameter = $this->getParameter(array());

        self::assertEquals('hidden', $parameter->getType());
        self::assertEquals('hidden', $parameter->getFormType());
        self::assertEquals(array(), $parameter->mapFormTypeOptions());
    }

    /**
     * @covers \Netgen\BlockManager\BlockDefinition\Parameter\Hidden::getAttributes
     * @covers \Netgen\BlockManager\BlockDefinition\Parameter\Hidden::configureOptions
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
     * @covers \Netgen\BlockManager\BlockDefinition\Parameter\Hidden::getAttributes
     * @covers \Netgen\BlockManager\BlockDefinition\Parameter\Hidden::configureOptions
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
     * @return \Netgen\BlockManager\BlockDefinition\Parameter\Hidden
     */
    public function getParameter($attributes)
    {
        return new Hidden('Test value', $attributes);
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
