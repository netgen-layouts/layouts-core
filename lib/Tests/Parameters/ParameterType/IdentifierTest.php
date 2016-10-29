<?php

namespace Netgen\BlockManager\Tests\Parameters\ParameterType;

use Netgen\BlockManager\Parameters\ParameterDefinition\Identifier;
use Netgen\BlockManager\Parameters\ParameterType\Identifier as IdentifierType;
use Symfony\Component\Validator\Validation;
use PHPUnit\Framework\TestCase;

class IdentifierTest extends TestCase
{
    /**
     * @covers \Netgen\BlockManager\Parameters\ParameterType\Identifier::getType
     */
    public function testGetType()
    {
        $type = new IdentifierType();
        $this->assertEquals('identifier', $type->getType());
    }

    /**
     * Returns the parameter under test.
     *
     * @param array $options
     * @param bool $required
     *
     * @return \Netgen\BlockManager\Parameters\ParameterDefinition\Identifier
     */
    public function getParameterDefinition(array $options = array(), $required = false)
    {
        return new Identifier($options, $required);
    }

    /**
     * @param mixed $value
     * @param bool $required
     * @param bool $isValid
     *
     * @covers \Netgen\BlockManager\Parameters\ParameterType\Identifier::getValueConstraints
     * @dataProvider validationProvider
     */
    public function testValidation($value, $required, $isValid)
    {
        $type = new IdentifierType();
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
