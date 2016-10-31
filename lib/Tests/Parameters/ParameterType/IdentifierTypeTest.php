<?php

namespace Netgen\BlockManager\Tests\Parameters\ParameterType;

use Netgen\BlockManager\Parameters\Parameter\Identifier;
use Netgen\BlockManager\Parameters\ParameterType\IdentifierType;
use Symfony\Component\Validator\Validation;
use PHPUnit\Framework\TestCase;

class IdentifierTypeTest extends TestCase
{
    /**
     * @covers \Netgen\BlockManager\Parameters\ParameterType\IdentifierType::getType
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
     * @return \Netgen\BlockManager\Parameters\Parameter\Identifier
     */
    public function getParameter(array $options = array(), $required = false)
    {
        return new Identifier($options, $required);
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
