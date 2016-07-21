<?php

namespace Netgen\BlockManager\Tests\Parameters\Parameter;

use Netgen\BlockManager\Parameters\Parameter\Identifier;
use Symfony\Component\Validator\Validation;
use PHPUnit\Framework\TestCase;

class IdentifierTest extends TestCase
{
    /**
     * @covers \Netgen\BlockManager\Parameters\Parameter\Identifier::getType
     */
    public function testGetType()
    {
        $parameter = $this->getParameter();
        $this->assertEquals('identifier', $parameter->getType());
    }

    /**
     * @covers \Netgen\BlockManager\Parameters\Parameter\Identifier::getOptions
     * @covers \Netgen\BlockManager\Parameters\Parameter\Identifier::configureOptions
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
     * @covers \Netgen\BlockManager\Parameters\Parameter\Identifier::getOptions
     * @covers \Netgen\BlockManager\Parameters\Parameter\Identifier::configureOptions
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
     * @return \Netgen\BlockManager\Parameters\Parameter\Identifier
     */
    public function getParameter(array $options = array(), $required = false)
    {
        return new Identifier($options, $required);
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
     * @covers \Netgen\BlockManager\Parameters\Parameter\Identifier::getValueConstraints
     * @dataProvider validationProvider
     */
    public function testValidation($value, $required, $isValid)
    {
        $parameter = $this->getParameter(array(), $required);
        $validator = Validation::createValidator();

        $errors = $validator->validate($value, $parameter->getConstraints());
        $this->assertEquals($isValid, $errors->count() == 0);
    }

    /**
     * Provider for testing valid parameter values.
     *
     * @return array
     */
    public function validationProvider()
    {
        return array(
            array('123abcASD', true, true),
            array('123abc_ASD', true, true),
            array(null, true, false),
            array('123abcASD', false, true),
            array('123abc_ASD', false, true),
            array(null, false, true),
            array('123abc ASD', false, false),
            array('123a-bcASD', false, false),
            array('123abc.ASD', false, false),
        );
    }
}
