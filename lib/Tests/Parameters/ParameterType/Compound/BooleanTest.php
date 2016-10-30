<?php

namespace Netgen\BlockManager\Tests\Parameters\ParameterType\Compound;

use Netgen\BlockManager\Parameters\Parameter\Compound\Boolean;
use Netgen\BlockManager\Parameters\ParameterType\Compound\Boolean as BooleanType;
use Symfony\Component\Validator\Validation;
use PHPUnit\Framework\TestCase;

class BooleanTest extends TestCase
{
    /**
     * @covers \Netgen\BlockManager\Parameters\ParameterType\Compound\Boolean::getType
     */
    public function testGetType()
    {
        $type = new BooleanType();
        $this->assertEquals('compound_boolean', $type->getType());
    }

    /**
     * Returns the parameter under test.
     *
     * @param array $options
     * @param bool $required
     * @param mixed $defaultValue
     *
     * @return \Netgen\BlockManager\Parameters\Parameter\Compound\Boolean
     */
    public function getParameter(array $options = array(), $required = false, $defaultValue = null)
    {
        return new Boolean(array(), $options, $required, $defaultValue);
    }

    /**
     * @param mixed $value
     * @param bool $required
     * @param bool $isValid
     *
     * @covers \Netgen\BlockManager\Parameters\ParameterType\Compound\Boolean::getValueConstraints
     * @covers \Netgen\BlockManager\Parameters\ParameterType\Compound\Boolean::getRequiredConstraints
     * @dataProvider validationProvider
     */
    public function testValidation($value, $required, $isValid)
    {
        $type = new BooleanType();
        $parameter = $this->getParameter(array(), $required);
        $validator = Validation::createValidator();

        $errors = $validator->validate($value, $type->getConstraints($parameter, $value));
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
