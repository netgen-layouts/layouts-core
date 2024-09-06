<?php

declare(strict_types=1);

namespace Netgen\Layouts\Tests\Validator;

use Netgen\Layouts\Item\Registry\ValueTypeRegistry;
use Netgen\Layouts\Item\ValueType\ValueType;
use Netgen\Layouts\Tests\TestCase\ValidatorTestCase;
use Netgen\Layouts\Validator\Constraint\ValueType as ValueTypeConstraint;
use Netgen\Layouts\Validator\ValueTypeValidator;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\Validator\ConstraintValidatorInterface;
use Symfony\Component\Validator\Exception\UnexpectedTypeException;

final class ValueTypeValidatorTest extends ValidatorTestCase
{
    protected function setUp(): void
    {
        $this->constraint = new ValueTypeConstraint();

        parent::setUp();
    }

    /**
     * @covers \Netgen\Layouts\Validator\ValueTypeValidator::__construct
     * @covers \Netgen\Layouts\Validator\ValueTypeValidator::validate
     *
     * @dataProvider validateDataProvider
     */
    public function testValidate(string $value, bool $isValid): void
    {
        $this->assertValid($isValid, $value);
    }

    /**
     * @covers \Netgen\Layouts\Validator\ValueTypeValidator::validate
     */
    public function testValidateThrowsUnexpectedTypeExceptionWithInvalidConstraint(): void
    {
        $this->expectException(UnexpectedTypeException::class);
        $this->expectExceptionMessage('Expected argument of type "Netgen\Layouts\Validator\Constraint\ValueType", "Symfony\Component\Validator\Constraints\NotBlank" given');

        $this->constraint = new NotBlank();
        $this->assertValid(true, 'value');
    }

    /**
     * @covers \Netgen\Layouts\Validator\ValueTypeValidator::validate
     */
    public function testValidateThrowsUnexpectedTypeExceptionWithInvalidValue(): void
    {
        $this->expectException(UnexpectedTypeException::class);
        $this->expectExceptionMessageMatches('/^Expected argument of type "string", "int(eger)?" given$/');

        $this->assertValid(true, 42);
    }

    public static function validateDataProvider(): iterable
    {
        return [
            ['value', true],
            ['other', false],
            ['', false],
        ];
    }

    protected function getValidator(): ConstraintValidatorInterface
    {
        $valueTypeRegistry = new ValueTypeRegistry(['value' => ValueType::fromArray(['isEnabled' => true])]);

        return new ValueTypeValidator($valueTypeRegistry);
    }
}
