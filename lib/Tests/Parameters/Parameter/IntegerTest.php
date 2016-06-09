<?php

namespace Netgen\BlockManager\Tests\Parameters\Parameter;

use Netgen\BlockManager\Parameters\Parameter\Integer;
use Symfony\Component\Validator\Validation;
use PHPUnit\Framework\TestCase;

class IntegerTest extends TestCase
{
    /**
     * @covers \Netgen\BlockManager\Parameters\Parameter\Integer::getType
     */
    public function testGetType()
    {
        $parameter = $this->getParameter();
        self::assertEquals('integer', $parameter->getType());
    }

    /**
     * @covers \Netgen\BlockManager\Parameters\Parameter\Integer::getOptions
     * @covers \Netgen\BlockManager\Parameters\Parameter\Integer::configureOptions
     * @dataProvider validOptionsProvider
     *
     * @param array $options
     * @param array $resolvedOptions
     */
    public function testValidOptions($options, $resolvedOptions)
    {
        $parameter = $this->getParameter($options);
        self::assertEquals($resolvedOptions, $parameter->getOptions());
    }

    /**
     * @covers \Netgen\BlockManager\Parameters\Parameter\Integer::getOptions
     * @covers \Netgen\BlockManager\Parameters\Parameter\Integer::configureOptions
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
     *
     * @return \Netgen\BlockManager\Parameters\Parameter\Integer
     */
    public function getParameter(array $options = array())
    {
        return new Integer($options);
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
     * @param bool $isValid
     *
     * @covers \Netgen\BlockManager\Parameters\Parameter\Integer::getParameterConstraints
     * @dataProvider validationProvider
     */
    public function testValidation($value, $isValid)
    {
        $parameter = $this->getParameter();
        $validator = Validation::createValidator();

        $errors = $validator->validate($value, $parameter->getConstraints());
        self::assertEquals($isValid, $errors->count() == 0);
    }

    /**
     * Provider for testing valid parameter values.
     *
     * @return array
     */
    public function validationProvider()
    {
        return array(
            array('12', false),
            array(12.3, false),
            array(true, false),
            array(array(), false),
            array(12, true),
            array(0, true),
            array(-12, true),
        );
    }
}
