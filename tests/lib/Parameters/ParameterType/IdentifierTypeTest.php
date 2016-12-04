<?php

namespace Netgen\BlockManager\Tests\Parameters\ParameterType;

use Netgen\BlockManager\Parameters\ParameterType\IdentifierType;
use Netgen\BlockManager\Tests\Parameters\Stubs\Parameter;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Validator\Validation;

class IdentifierTypeTest extends TestCase
{
    /**
     * @covers \Netgen\BlockManager\Parameters\ParameterType\IdentifierType::getIdentifier
     */
    public function testGetIdentifier()
    {
        $type = new IdentifierType();
        $this->assertEquals('identifier', $type->getIdentifier());
    }

    /**
     * @covers \Netgen\BlockManager\Parameters\ParameterType\IdentifierType::configureOptions
     * @dataProvider validOptionsProvider
     *
     * @param array $options
     * @param array $resolvedOptions
     */
    public function testValidOptions($options, $resolvedOptions)
    {
        $parameter = $this->getParameter($options);
        $this->assertEquals($resolvedOptions, $parameter->getOptions());
    }

    /**
     * @covers \Netgen\BlockManager\Parameters\ParameterType\IdentifierType::configureOptions
     * @expectedException \Symfony\Component\OptionsResolver\Exception\InvalidArgumentException
     * @dataProvider invalidOptionsProvider
     *
     * @param array $options
     */
    public function testInvalidOptions($options)
    {
        $this->getParameter($options);
    }

    /**
     * Returns the parameter under test.
     *
     * @param array $options
     * @param bool $required
     *
     * @return \Netgen\BlockManager\Parameters\ParameterInterface
     */
    public function getParameter(array $options = array(), $required = false)
    {
        return new Parameter('name', new IdentifierType(), $options, $required);
    }

    /**
     * Provider for testing valid parameter attributes.
     *
     * @return array
     */
    public function validOptionsProvider()
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
    public function invalidOptionsProvider()
    {
        return array(
            array(
                array(
                    'undefined_value' => 'Value',
                ),
            ),
        );
    }

    /**
     * @param mixed $value
     * @param bool $required
     * @param bool $isValid
     *
     * @covers \Netgen\BlockManager\Parameters\ParameterType\IdentifierType::getValueConstraints
     * @dataProvider validationProvider
     */
    public function testValidation($value, $required, $isValid)
    {
        $type = new IdentifierType();
        $parameter = $this->getParameter(array(), $required);
        $validator = Validation::createValidator();

        $errors = $validator->validate($value, $type->getConstraints($parameter, $value));
        $this->assertEquals($isValid, $errors->count() === 0);
    }

    /**
     * Provider for testing valid parameter values.
     *
     * @return array
     */
    public function validationProvider()
    {
        return array(
            array('a123abcASD', true, true),
            array('a123abc_ASD', true, true),
            array('a123abcASD', false, true),
            array('a123abc_ASD', false, true),
            array(null, false, true),
            array(null, true, false),
            array('a123abc ASD', false, false),
            array('a123a-bcASD', false, false),
            array('a123abc.ASD', false, false),
            array('123abcASD', false, false),
        );
    }
}
