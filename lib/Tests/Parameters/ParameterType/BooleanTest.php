<?php

namespace Netgen\BlockManager\Tests\Parameters\ParameterType;

use Netgen\BlockManager\Parameters\ParameterDefinition\Boolean;
use Netgen\BlockManager\Parameters\ParameterType\Boolean as BooleanType;
use Symfony\Component\Validator\Validation;
use PHPUnit\Framework\TestCase;

class BooleanTest extends TestCase
{
    /**
     * @covers \Netgen\BlockManager\Parameters\ParameterType\Boolean::getType
     */
    public function testGetType()
    {
        $type = new BooleanType();
        $this->assertEquals('boolean', $type->getType());
    }

    /**
     * Returns the parameter under test.
     *
     * @param array $options
     * @param bool $required
     * @param mixed $defaultValue
     *
     * @return \Netgen\BlockManager\Parameters\ParameterDefinition\Boolean
     */
    public function getParameterDefinition(array $options = array(), $required = false, $defaultValue = null)
    {
        return new Boolean($options, $required, $defaultValue);
    }

    /**
     * @param mixed $value
     * @param bool $required
     * @param bool $isValid
     *
     * @covers \Netgen\BlockManager\Parameters\ParameterType\Boolean::getValueConstraints
     * @covers \Netgen\BlockManager\Parameters\ParameterType\Boolean::getRequiredConstraints
     * @dataProvider validationProvider
     */
    public function testValidation($value, $required, $isValid)
    {
        $type = new BooleanType();
        $parameterDefinition = $this->getParameterDefinition(array(), $required);
        $validator = Validation::createValidator();

        $errors = $validator->validate($value, $type->getConstraints($parameterDefinition, $value));
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
            array('12', false, false),
            array(12.3, false, false),
            array(true, false, true),
            array(false, false, true),
            array(null, false, true),
            array(true, true, true),
            array(false, true, true),
            array(null, true, false),
            array(array(), false, false),
            array(12, false, false),
        );
    }
}
