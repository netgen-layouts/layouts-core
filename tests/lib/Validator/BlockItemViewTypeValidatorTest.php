<?php

declare(strict_types=1);

namespace Netgen\Layouts\Tests\Validator;

use Netgen\Layouts\Block\BlockDefinition;
use Netgen\Layouts\Tests\Core\Stubs\ConfigProvider;
use Netgen\Layouts\Tests\TestCase\ValidatorTestCase;
use Netgen\Layouts\Validator\BlockItemViewTypeValidator;
use Netgen\Layouts\Validator\Constraint\BlockItemViewType;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\Validator\ConstraintValidatorInterface;
use Symfony\Component\Validator\Exception\MissingOptionsException;
use Symfony\Component\Validator\Exception\UnexpectedTypeException;

use function sprintf;

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

        $this->constraint = new BlockItemViewType(['viewType' => '', 'definition' => $this->blockDefinition]);

        parent::setUp();
    }

    /**
     * @covers \Netgen\Layouts\Validator\BlockItemViewTypeValidator::validate
     *
     * @dataProvider validateDataProvider
     */
    public function testValidate(string $viewType, string $value, bool $isValid): void
    {
        $this->constraint->viewType = $viewType;

        $this->assertValid($isValid, $value);
    }

    /**
     * @covers \Netgen\Layouts\Validator\BlockItemViewTypeValidator::validate
     */
    public function testValidateThrowsUnexpectedTypeExceptionWithInvalidConstraint(): void
    {
        $this->expectException(UnexpectedTypeException::class);
        $this->expectExceptionMessage('Expected argument of type "Netgen\Layouts\Validator\Constraint\BlockItemViewType", "Symfony\Component\Validator\Constraints\NotBlank" given');

        $this->constraint = new NotBlank();
        $this->assertValid(true, 'standard');
    }

    /**
     * @covers \Netgen\Layouts\Validator\BlockItemViewTypeValidator::validate
     */
    public function testValidateThrowsMissingOptionsExceptionWithInvalidBlockDefinition(): void
    {
        $this->expectException(MissingOptionsException::class);
        $this->expectExceptionMessage(sprintf('The options "definition" must be set for constraint "%s".', BlockItemViewType::class));

        $this->constraint = new BlockItemViewType(['viewType' => '']);
        $this->assertValid(true, 'standard');
    }

    /**
     * @covers \Netgen\Layouts\Validator\BlockItemViewTypeValidator::validate
     */
    public function testValidateThrowsMissingOptionsExceptionWithInvalidViewType(): void
    {
        $this->expectException(MissingOptionsException::class);
        $this->expectExceptionMessage(sprintf('The options "viewType" must be set for constraint "%s".', BlockItemViewType::class));

        $this->constraint = new BlockItemViewType(['definition' => $this->blockDefinition]);
        $this->assertValid(true, 'standard');
    }

    /**
     * @covers \Netgen\Layouts\Validator\BlockItemViewTypeValidator::validate
     */
    public function testValidateThrowsUnexpectedTypeExceptionWithInvalidValue(): void
    {
        $this->expectException(UnexpectedTypeException::class);
        $this->expectExceptionMessageMatches('/^Expected argument of type "string", "int(eger)?" given$/');

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
