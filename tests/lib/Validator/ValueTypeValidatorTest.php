<?php

namespace Netgen\BlockManager\Tests\Validator;

use Netgen\BlockManager\Tests\TestCase\ValidatorTestCase;
use Netgen\BlockManager\Validator\Constraint\ValueType;
use Netgen\BlockManager\Validator\ValueTypeValidator;
use Symfony\Component\Validator\Constraints\NotBlank;

class ValueTypeValidatorTest extends ValidatorTestCase
{
    public function setUp()
    {
        $this->constraint = new ValueType();

        parent::setUp();
    }

    /**
     * @return \Symfony\Component\Validator\ConstraintValidator
     */
    public function getValidator()
    {
        return new ValueTypeValidator(array('value'));
    }

    /**
     * @param string $value
     * @param bool $isValid
     *
     * @covers \Netgen\BlockManager\Validator\ValueTypeValidator::__construct
     * @covers \Netgen\BlockManager\Validator\ValueTypeValidator::validate
     * @dataProvider validateDataProvider
     */
    public function testValidate($value, $isValid)
    {
        $this->assertValid($isValid, $value);
    }

    /**
     * @covers \Netgen\BlockManager\Validator\ValueTypeValidator::validate
     * @expectedException \Symfony\Component\Validator\Exception\UnexpectedTypeException
     * @expectedExceptionMessage Expected argument of type "Netgen\BlockManager\Validator\Constraint\ValueType", "Symfony\Component\Validator\Constraints\NotBlank" given
     */
    public function testValidateThrowsUnexpectedTypeExceptionWithInvalidConstraint()
    {
        $this->constraint = new NotBlank();
        $this->assertValid(true, 'value');
    }

    /**
     * @covers \Netgen\BlockManager\Validator\ValueTypeValidator::validate
     * @expectedException \Symfony\Component\Validator\Exception\UnexpectedTypeException
     * @expectedExceptionMessage Expected argument of type "string", "integer" given
     */
    public function testValidateThrowsUnexpectedTypeExceptionWithInvalidValue()
    {
        $this->assertValid(true, 42);
    }

    public function validateDataProvider()
    {
        return array(
            array('value', true),
            array('other', false),
            array('', false),
        );
    }
}
