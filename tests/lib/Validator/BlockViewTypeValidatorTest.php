<?php

declare(strict_types=1);

namespace Netgen\Layouts\Tests\Validator;

use Netgen\Layouts\Block\BlockDefinition;
use Netgen\Layouts\Tests\Core\Stubs\ConfigProvider;
use Netgen\Layouts\Tests\TestCase\ValidatorTestCase;
use Netgen\Layouts\Validator\BlockViewTypeValidator;
use Netgen\Layouts\Validator\Constraint\BlockViewType;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\Validator\ConstraintValidatorInterface;
use Symfony\Component\Validator\Exception\MissingOptionsException;
use Symfony\Component\Validator\Exception\UnexpectedTypeException;

use function sprintf;

final class BlockViewTypeValidatorTest extends ValidatorTestCase
{
    private BlockDefinition $blockDefinition;

    protected function setUp(): void
    {
        $this->blockDefinition = BlockDefinition::fromArray(
            [
                'configProvider' => ConfigProvider::fromShortConfig(['large' => []]),
            ],
        );

        $this->constraint = new BlockViewType(['definition' => $this->blockDefinition]);

        parent::setUp();
    }

    /**
     * @covers \Netgen\Layouts\Validator\BlockViewTypeValidator::validate
     *
     * @dataProvider validateDataProvider
     */
    public function testValidate(string $value, bool $isValid): void
    {
        $this->assertValid($isValid, $value);
    }

    /**
     * @covers \Netgen\Layouts\Validator\BlockViewTypeValidator::validate
     */
    public function testValidateThrowsUnexpectedTypeExceptionWithInvalidConstraint(): void
    {
        $this->expectException(UnexpectedTypeException::class);
        $this->expectExceptionMessage('Expected argument of type "Netgen\Layouts\Validator\Constraint\BlockViewType", "Symfony\Component\Validator\Constraints\NotBlank" given');

        $this->constraint = new NotBlank();
        $this->assertValid(true, 'large');
    }

    /**
     * @covers \Netgen\Layouts\Validator\BlockViewTypeValidator::validate
     */
    public function testValidateThrowsMissingOptionsExceptionWithInvalidBlockDefinition(): void
    {
        $this->expectException(MissingOptionsException::class);
        $this->expectExceptionMessage(sprintf('The options "definition" must be set for constraint "%s".', BlockViewType::class));

        $this->constraint->definition = new BlockViewType();
        $this->assertValid(true, 'large');
    }

    /**
     * @covers \Netgen\Layouts\Validator\BlockViewTypeValidator::validate
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
            ['large', true],
            ['small', false],
            ['', false],
        ];
    }

    protected function getValidator(): ConstraintValidatorInterface
    {
        return new BlockViewTypeValidator();
    }
}
