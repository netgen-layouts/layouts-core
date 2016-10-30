<?php

namespace Netgen\BlockManager\Tests\Parameters\ParameterType;

use Netgen\BlockManager\Parameters\Parameter\Choice;
use Netgen\BlockManager\Parameters\ParameterType\Choice as ChoiceType;
use Symfony\Component\Validator\Validation;
use PHPUnit\Framework\TestCase;

class ChoiceTest extends TestCase
{
    /**
     * @covers \Netgen\BlockManager\Parameters\ParameterType\Choice::getType
     */
    public function testGetType()
    {
        $type = new ChoiceType();
        $this->assertEquals('choice', $type->getType());
    }

    /**
     * Returns the parameter under test.
     *
     * @param array $options
     * @param bool $required
     * @param mixed $defaultValue
     *
     * @return \Netgen\BlockManager\Parameters\Parameter\Choice
     */
    public function getParameter(array $options = array(), $required = false, $defaultValue = null)
    {
        return new Choice($options, $required, $defaultValue);
    }

    /**
     * @param mixed $value
     * @param bool $isValid
     *
     * @covers \Netgen\BlockManager\Parameters\ParameterType\Choice::getValueConstraints
     * @dataProvider validationProvider
     */
    public function testValidation($value, $isValid)
    {
        $type = new ChoiceType();
        $parameter = $this->getParameter(array('options' => array('One' => 1, 'Two' => 2)));
        $validator = Validation::createValidator();

        $errors = $validator->validate($value, $type->getConstraints($parameter, $value));
        $this->assertEquals($isValid, $errors->count() == 0);
    }

    /**
     * @param mixed $value
     * @param bool $isValid
     *
     * @covers \Netgen\BlockManager\Parameters\ParameterType\Choice::getValueConstraints
     * @dataProvider validationProvider
     */
    public function testValidationWithClosure($value, $isValid)
    {
        $closure = function () {
            return array('One' => 1, 'Two' => 2);
        };

        $type = new ChoiceType();
        $parameter = $this->getParameter(array('options' => $closure));
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
            array(1, true),
            array('One', false),
            array(2, true),
            array('Two', false),
            array('123abc.ASD', false),
            array(0, false),
        );
    }
}
