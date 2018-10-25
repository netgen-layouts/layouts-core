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
use Symfony\Component\Validator\Exception\UnexpectedTypeException;

final class ValueTypeValidatorTest extends ValidatorTestCase
{
    public function setUp(): void
    {
        $this->constraint = new ValueTypeConstraint();

        parent::setUp();
    }

    /**
     * @covers \Netgen\BlockManager\Validator\ValueTypeValidator::__construct
     * @covers \Netgen\BlockManager\Validator\ValueTypeValidator::validate
     * @dataProvider validateDataProvider
     */
    public function testValidate(string $value, bool $isValid): void
    {
        $this->assertValid($isValid, $value);
    }

    /**
     * @covers \Netgen\BlockManager\Validator\ValueTypeValidator::validate
     */
    public function testValidateThrowsUnexpectedTypeExceptionWithInvalidConstraint(): void
    {
        $this->expectException(UnexpectedTypeException::class);
        $this->expectExceptionMessage('Expected argument of type "Netgen\\BlockManager\\Validator\\Constraint\\ValueType", "Symfony\\Component\\Validator\\Constraints\\NotBlank" given');

        $this->constraint = new NotBlank();
        $this->assertValid(true, 'value');
    }

    /**
     * @covers \Netgen\BlockManager\Validator\ValueTypeValidator::validate
     */
    public function testValidateThrowsUnexpectedTypeExceptionWithInvalidValue(): void
    {
        $this->expectException(UnexpectedTypeException::class);
        $this->expectExceptionMessage('Expected argument of type "string", "integer" given');

        $this->assertValid(true, 42);
    }

    public function validateDataProvider(): array
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
