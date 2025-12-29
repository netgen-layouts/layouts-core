<?php

declare(strict_types=1);

namespace Netgen\Layouts\Tests\Validator;

use Netgen\Layouts\Block\BlockDefinition;
use Netgen\Layouts\Block\Registry\BlockDefinitionRegistry;
use Netgen\Layouts\Tests\TestCase\ValidatorTestCase;
use Netgen\Layouts\Validator\BlockDefinitionValidator;
use Netgen\Layouts\Validator\Constraint\BlockDefinition as BlockDefinitionConstraint;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\DataProvider;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\Validator\ConstraintValidatorInterface;
use Symfony\Component\Validator\Exception\UnexpectedTypeException;

#[CoversClass(BlockDefinitionValidator::class)]
final class BlockDefinitionValidatorTest extends ValidatorTestCase
{
    protected function setUp(): void
    {
        $this->constraint = new BlockDefinitionConstraint();

        parent::setUp();
    }

    #[DataProvider('validateDataProvider')]
    public function testValidate(mixed $value, bool $isValid): void
    {
        $this->assertValid($isValid, $value);
    }

    public function testValidateWithNonExistentValue(): void
    {
        $this->constraint = new BlockDefinitionConstraint(allowInvalid: true);

        $this->assertValid(true, 'non_existing');
    }

    public function testValidateThrowsUnexpectedTypeExceptionWithInvalidConstraint(): void
    {
        $this->expectException(UnexpectedTypeException::class);
        $this->expectExceptionMessage('Expected argument of type "Netgen\Layouts\Validator\Constraint\BlockDefinition", "Symfony\Component\Validator\Constraints\NotBlank" given');

        $this->constraint = new NotBlank();
        $this->assertValid(true, 'value');
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
            ['title', true],
            ['other', false],
            ['', false],
            [null, true],
        ];
    }

    protected function getConstraintValidator(): ConstraintValidatorInterface
    {
        $blockDefinitionRegistry = new BlockDefinitionRegistry(['title' => BlockDefinition::fromArray(['identifier' => 'title'])]);

        return new BlockDefinitionValidator($blockDefinitionRegistry);
    }
}
