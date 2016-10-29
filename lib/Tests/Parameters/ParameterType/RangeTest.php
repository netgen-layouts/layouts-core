<?php

namespace Netgen\BlockManager\Tests\Parameters\ParameterType;

use Netgen\BlockManager\Parameters\ParameterDefinition\Range;
use Netgen\BlockManager\Parameters\ParameterType\Range as RangeType;
use Symfony\Component\Validator\Validation;
use PHPUnit\Framework\TestCase;

class RangeTest extends TestCase
{
    /**
     * @covers \Netgen\BlockManager\Parameters\ParameterType\Range::getType
     */
    public function testGetType()
    {
        $type = new RangeType();
        $this->assertEquals('range', $type->getType());
    }

    /**
     * Returns the parameter under test.
     *
     * @param array $options
     * @param bool $required
     * @param mixed $defaultValue
     *
     * @return \Netgen\BlockManager\Parameters\ParameterDefinition\Range
     */
    public function getParameterDefinition(array $options = array(), $required = false, $defaultValue = null)
    {
        return new Range($options, $required, $defaultValue);
    }

    /**
     * @param mixed $value
     * @param bool $required
     * @param bool $isValid
     *
     * @covers \Netgen\BlockManager\Parameters\ParameterType\Range::getValueConstraints
     * @dataProvider validationProvider
     */
    public function testValidation($value, $required, $isValid)
    {
        $type = new RangeType();
        $parameterDefinition = $this->getParameterDefinition(array('min' => 5, 'max' => 10), $required);
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
            array(true, false, false),
            array(array(), false, false),
            array(12, false, false),
            array(12.3, false, false),
            array(0, false, false),
            array(-12, false, false),
            array(5, false, true),
            array(7, false, true),
            array(7.5, false, true),
            array(10, false, true),
            array(null, false, true),
            array(5, true, true),
            array(7, true, true),
            array(7.5, true, true),
            array(10, true, true),
            array(null, true, false),
        );
    }
}
