<?php

namespace Netgen\BlockManager\Tests\Parameters\ParameterType;

use Netgen\BlockManager\Parameters\ParameterType\HtmlType;
use Netgen\BlockManager\Tests\Parameters\Stubs\Parameter;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Validator\Validation;

class HtmlTypeTest extends TestCase
{
    /**
     * @covers \Netgen\BlockManager\Parameters\ParameterType\HtmlType::getIdentifier
     */
    public function testGetIdentifier()
    {
        $type = new HtmlType();
        $this->assertEquals('html', $type->getIdentifier());
    }

    /**
     * @covers \Netgen\BlockManager\Parameters\ParameterType\HtmlType::configureOptions
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
     * @covers \Netgen\BlockManager\Parameters\ParameterType\HtmlType::configureOptions
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
     * @return \Netgen\BlockManager\Parameters\ParameterInterface
     */
    public function getParameter($options = array())
    {
        return new Parameter(
            array(
                'name' => 'name',
                'type' => new HtmlType(),
                'options' => $options,
            )
        );
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
     * @covers \Netgen\BlockManager\Parameters\ParameterType\HtmlType::getValueConstraints
     * @covers \Netgen\BlockManager\Parameters\ParameterType\HtmlType::getRequiredConstraints
     * @dataProvider validationProvider
     */
    public function testValidation($value, $isValid)
    {
        $type = new HtmlType();
        $parameter = $this->getParameter();
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
            array('test', true),
            array(null, true),
            array(12.3, false),
            array(12, false),
            array(true, false),
            array(false, false),
            array(array(), false),
        );
    }
}
