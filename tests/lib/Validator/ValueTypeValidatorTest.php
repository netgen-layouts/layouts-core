<?php

declare(strict_types=1);

namespace Netgen\BlockManager\Tests\Validator;

use Netgen\BlockManager\Item\Registry\ValueTypeRegistry;
use Netgen\BlockManager\Item\ValueType\ValueType;
use Netgen\BlockManager\Tests\TestCase\ValidatorTestCase;
use Netgen\BlockManager\Validator\Constraint\ValueType as ValueTypeConstraint;
use Netgen\BlockManager\Validator\ValueTypeValidator;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\Validator\ConstraintValidatorInterface;

final class ValueTypeValidatorTest extends ValidatorTestCase
{
    public function setUp(): void
    {
        $this->constraint = new ValueTypeConstraint();

        parent::setUp();
    }

    public function getValidator(): ConstraintValidatorInterface
    {
        $valueTypeRegistry = new ValueTypeRegistry(['value' => ValueType::fromArray(['isEnabled' => true])]);

        return new ValueTypeValidator($valueTypeRegistry);
    }

    /**
     * @covers \Netgen\BlockManager\Validator\ValueTypeValidator::__construct
     * @covers \Netgen\BlockManager\Validator\ValueTypeValidator::validate
     * @dataProvider validateDataProvider
     */
    public function testValidate(string $value, bool $isValid): void
    {
        self::assertValid($isValid, $value);
    }

    /**
     * @covers \Netgen\BlockManager\Validator\ValueTypeValidator::validate
     * @expectedException \Symfony\Component\Validator\Exception\UnexpectedTypeException
     * @expectedExceptionMessage Expected argument of type "Netgen\BlockManager\Validator\Constraint\ValueType", "Symfony\Component\Validator\Constraints\NotBlank" given
     */
    public function testValidateThrowsUnexpectedTypeExceptionWithInvalidConstraint(): void
    {
        $this->constraint = new NotBlank();
        self::assertValid(true, 'value');
    }

    /**
     * @covers \Netgen\BlockManager\Validator\ValueTypeValidator::validate
     * @expectedException \Symfony\Component\Validator\Exception\UnexpectedTypeException
     * @expectedExceptionMessage Expected argument of type "string", "integer" given
     */
    public function testValidateThrowsUnexpectedTypeExceptionWithInvalidValue(): void
    {
        self::assertValid(true, 42);
    }

    public function validateDataProvider(): array
    {
        return [
            ['value', true],
            ['other', false],
            ['', false],
        ];
    }
}
