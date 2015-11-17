<?php

namespace Netgen\BlockManager\BlockDefinition\Tests\Parameters;

use PHPUnit_Framework_TestCase;

abstract class ParameterTest extends PHPUnit_Framework_TestCase
{
    /**
     * @covers \Netgen\BlockManager\BlockDefinition\Parameter::getAttributes
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
     * @covers \Netgen\BlockManager\BlockDefinition\Parameter::getAttributes
     * @expectedException \Symfony\Component\OptionsResolver\Exception\InvalidOptionsException
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
     * @covers \Netgen\BlockManager\BlockDefinition\Parameter::getAttributes
     * @expectedException \Symfony\Component\OptionsResolver\Exception\UndefinedOptionsException
     */
    public function testUndefinedAttributes()
    {
        $parameter = $this->getParameter(array('undefined_value' => 'Value'));
    }

    /**
     * Returns the parameter under test
     *
     * @param mixed $attributes
     *
     * @return mixed
     */
    abstract public function getParameter($attributes);

    /**
     * Provider for testing valid parameter attributes
     *
     * @return array
     */
    abstract public function validAttributesProvider();

    /**
     * Provider for testing invalid parameter attributes
     *
     * @return array
     */
    abstract public function invalidAttributesProvider();
}
