<?php

namespace Netgen\BlockManager\Tests\Validator;

use Netgen\BlockManager\Tests\TestCase\ValidatorTestCase;
use Netgen\BlockManager\Validator\ValueTypeValidator;
use Netgen\BlockManager\Validator\Constraint\ValueType;

class ValueTypeValidatorTest extends ValidatorTestCase
{
    public function setUp()
    {
        parent::setUp();

        $this->constraint = new ValueType();
    }

    /**
     * @return \Symfony\Component\Validator\Validator\ValidatorInterface
     */
    public function getValidator()
    {
        return new ValueTypeValidator(array('value'));
    }

    /**
     * @param string $valueType
     * @param bool $isValid
     *
     * @covers \Netgen\BlockManager\Validator\ValueTypeValidator::__construct
     * @covers \Netgen\BlockManager\Validator\ValueTypeValidator::validate
     * @dataProvider validateDataProvider
     */
    public function testValidate($valueType, $isValid)
    {
        $this->assertValid($isValid, $valueType);
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
