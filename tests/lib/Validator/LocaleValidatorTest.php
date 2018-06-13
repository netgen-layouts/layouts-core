<?php

declare(strict_types=1);

namespace Netgen\BlockManager\Tests\Validator;

use Netgen\BlockManager\Tests\TestCase\ValidatorTestCase;
use Netgen\BlockManager\Validator\Constraint\Locale as LocaleConstraint;
use Netgen\BlockManager\Validator\LocaleValidator;
use Symfony\Component\Validator\Constraints\NotBlank;

final class LocaleValidatorTest extends ValidatorTestCase
{
    public function setUp()
    {
        $this->constraint = new LocaleConstraint();

        parent::setUp();
    }

    /**
     * @return \Symfony\Component\Validator\ConstraintValidator
     */
    public function getValidator()
    {
        return new LocaleValidator();
    }

    /**
     * @param string $value
     * @param bool $isValid
     *
     * @covers \Netgen\BlockManager\Validator\LocaleValidator::validate
     * @dataProvider validateDataProvider
     */
    public function testValidate($value, $isValid)
    {
        $this->assertValid($isValid, $value);
    }

    /**
     * @covers \Netgen\BlockManager\Validator\LocaleValidator::validate
     * @expectedException \Symfony\Component\Validator\Exception\UnexpectedTypeException
     * @expectedExceptionMessage Expected argument of type "Netgen\BlockManager\Validator\Constraint\Locale", "Symfony\Component\Validator\Constraints\NotBlank" given
     */
    public function testValidateThrowsUnexpectedTypeExceptionWithInvalidConstraint()
    {
        $this->constraint = new NotBlank();
        $this->assertValid(true, 'hr_HR');
    }

    /**
     * @covers \Netgen\BlockManager\Validator\LocaleValidator::validate
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
            [null, true],
            ['', true],
            ['en', true],
            ['en_US', true],
            ['pt', true],
            ['pt_PT', true],
            ['zh_Hans', true],
            ['fil_PH', true],
            // We do not allow non-canonicalized locales
            ['en-US', false],
            ['es-AR', false],
            ['fr_FR.utf8', false],
            ['EN', false],
            // Invalid locales
            ['foobar', false],
        ];
    }
}
