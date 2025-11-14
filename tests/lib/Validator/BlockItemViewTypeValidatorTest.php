<?php

declare(strict_types=1);

namespace Netgen\Layouts\Tests\Validator;

use Netgen\Layouts\Block\BlockDefinition;
use Netgen\Layouts\Tests\Core\Stubs\ConfigProvider;
use Netgen\Layouts\Tests\TestCase\ValidatorTestCase;
use Netgen\Layouts\Validator\BlockItemViewTypeValidator;
use Netgen\Layouts\Validator\Constraint\BlockItemViewType;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\DataProvider;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\Validator\ConstraintValidatorInterface;
use Symfony\Component\Validator\Exception\UnexpectedTypeException;

#[CoversClass(BlockItemViewTypeValidator::class)]
final class BlockItemViewTypeValidatorTest extends ValidatorTestCase
{
    private BlockDefinition $blockDefinition;

    protected function setUp(): void
    {
        $this->blockDefinition = BlockDefinition::fromArray(
            [
                'configProvider' => ConfigProvider::fromShortConfig(['large' => ['standard']]),
            ],
        );

        $this->constraint = new BlockItemViewType(viewType: '', definition: $this->blockDefinition);

        parent::setUp();
    }

    #[DataProvider('validateDataProvider')]
    public function testValidate(string $viewType, string $value, bool $isValid): void
    {
        $this->constraint->viewType = $viewType;

        $this->assertValid($isValid, $value);
    }

    public function testValidateThrowsUnexpectedTypeExceptionWithInvalidConstraint(): void
    {
        $this->expectException(UnexpectedTypeException::class);
        $this->expectExceptionMessage('Expected argument of type "Netgen\Layouts\Validator\Constraint\BlockItemViewType", "Symfony\Component\Validator\Constraints\NotBlank" given');

        $this->constraint = new NotBlank();
        $this->assertValid(true, 'standard');
    }

    public function testValidateThrowsUnexpectedTypeExceptionWithInvalidValue(): void
    {
        $this->expectException(UnexpectedTypeException::class);
        $this->expectExceptionMessage('Expected argument of type "string", "int" given');

        $this->constraint->viewType = 'large';
        $this->assertValid(true, 42);
    }

    public static function validateDataProvider(): iterable
    {
        return [
            ['large', 'standard', true],
            ['large', 'unknown', false],
            ['large', '', false],
            ['small', 'standard', false],
            ['small', 'unknown', false],
            ['small', '', false],
            ['', 'standard', false],
            ['', 'unknown', false],
            ['', '', false],
        ];
    }

    protected function getValidator(): ConstraintValidatorInterface
    {
        return new BlockItemViewTypeValidator();
    }
}
