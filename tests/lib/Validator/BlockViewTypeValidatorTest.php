<?php

declare(strict_types=1);

namespace Netgen\Layouts\Tests\Validator;

use Netgen\Layouts\Block\BlockDefinition;
use Netgen\Layouts\Tests\Core\Stubs\ConfigProvider;
use Netgen\Layouts\Tests\TestCase\ValidatorTestCase;
use Netgen\Layouts\Validator\BlockViewTypeValidator;
use Netgen\Layouts\Validator\Constraint\BlockViewType;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\DataProvider;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\Validator\ConstraintValidatorInterface;
use Symfony\Component\Validator\Exception\UnexpectedTypeException;

#[CoversClass(BlockViewTypeValidator::class)]
final class BlockViewTypeValidatorTest extends ValidatorTestCase
{
    protected function setUp(): void
    {
        $blockDefinition = BlockDefinition::fromArray(
            [
                'configProvider' => ConfigProvider::fromShortConfig(['large' => []]),
            ],
        );

        $this->constraint = new BlockViewType(definition: $blockDefinition);

        parent::setUp();
    }

    #[DataProvider('validateDataProvider')]
    public function testValidate(string $value, bool $isValid): void
    {
        $this->assertValid($isValid, $value);
    }

    public function testValidateThrowsUnexpectedTypeExceptionWithInvalidConstraint(): void
    {
        $this->expectException(UnexpectedTypeException::class);
        $this->expectExceptionMessage('Expected argument of type "Netgen\Layouts\Validator\Constraint\BlockViewType", "Symfony\Component\Validator\Constraints\NotBlank" given');

        $this->constraint = new NotBlank();
        $this->assertValid(true, 'large');
    }

    public function testValidateThrowsUnexpectedTypeExceptionWithInvalidValue(): void
    {
        $this->expectException(UnexpectedTypeException::class);
        $this->expectExceptionMessage('Expected argument of type "string", "int" given');

        $this->assertValid(true, 42);
    }

    /**
     * @return iterable<mixed>
     */
    public static function validateDataProvider(): iterable
    {
        return [
            ['large', true],
            ['small', false],
            ['', false],
        ];
    }

    protected function getConstraintValidator(): ConstraintValidatorInterface
    {
        return new BlockViewTypeValidator();
    }
}
