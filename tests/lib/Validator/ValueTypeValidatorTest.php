<?php

declare(strict_types=1);

namespace Netgen\BlockManager\Tests\Validator;

use Netgen\BlockManager\Item\Registry\ValueTypeRegistry;
use Netgen\BlockManager\Item\ValueType\ValueType;
use Netgen\BlockManager\Tests\TestCase\ValidatorTestCase;
use Netgen\BlockManager\Validator\Constraint\ValueType as ValueTypeConstraint;
use Netgen\BlockManager\Validator\ValueTypeValidator;
use Symfony\Component\Validator\Constraints\NotBlank;

final class ValueTypeValidatorTest extends ValidatorTestCase
{
    public function setUp()
    {
        $this->constraint = new ValueTypeConstraint();

        parent::setUp();
    }

    /**
     * @return \Symfony\Component\Validator\ConstraintValidator
     */
    public function getValidator()
    {
        $valueTypeRegistry = new ValueTypeRegistry();
        $valueTypeRegistry->addValueType('value', new ValueType(['isEnabled' => true]));

        return new ValueTypeValidator($valueTypeRegistry);
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
        return [
            ['value', true],
            ['other', false],
            ['', false],
        ];
    }
}
