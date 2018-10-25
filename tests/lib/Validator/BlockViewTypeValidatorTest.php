<?php

declare(strict_types=1);

namespace Netgen\BlockManager\Tests\Validator;

use Netgen\BlockManager\Block\BlockDefinition;
use Netgen\BlockManager\Block\BlockDefinition\Configuration\ViewType;
use Netgen\BlockManager\Tests\TestCase\ValidatorTestCase;
use Netgen\BlockManager\Validator\BlockViewTypeValidator;
use Netgen\BlockManager\Validator\Constraint\BlockViewType;
use stdClass;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\Validator\ConstraintValidatorInterface;
use Symfony\Component\Validator\Exception\UnexpectedTypeException;

final class BlockViewTypeValidatorTest extends ValidatorTestCase
{
    /**
     * @var \Netgen\BlockManager\Block\BlockDefinitionInterface
     */
    private $blockDefinition;

    public function setUp(): void
    {
        $this->blockDefinition = BlockDefinition::fromArray(
            [
                'viewTypes' => [
                    'large' => new ViewType(),
                ],
            ]
        );

        $this->constraint = new BlockViewType(['definition' => $this->blockDefinition]);

        parent::setUp();
    }

    /**
     * @covers \Netgen\BlockManager\Validator\BlockViewTypeValidator::validate
     * @dataProvider validateDataProvider
     */
    public function testValidate(string $value, bool $isValid): void
    {
        $this->assertValid($isValid, $value);
    }

    /**
     * @covers \Netgen\BlockManager\Validator\BlockViewTypeValidator::validate
     */
    public function testValidateThrowsUnexpectedTypeExceptionWithInvalidConstraint(): void
    {
        $this->expectException(UnexpectedTypeException::class);
        $this->expectExceptionMessage('Expected argument of type "Netgen\\BlockManager\\Validator\\Constraint\\BlockViewType", "Symfony\\Component\\Validator\\Constraints\\NotBlank" given');

        $this->constraint = new NotBlank();
        $this->assertValid(true, 'large');
    }

    /**
     * @covers \Netgen\BlockManager\Validator\BlockViewTypeValidator::validate
     */
    public function testValidateThrowsUnexpectedTypeExceptionWithInvalidBlockDefinition(): void
    {
        $this->expectException(UnexpectedTypeException::class);
        $this->expectExceptionMessage('Expected argument of type "Netgen\\BlockManager\\Block\\BlockDefinitionInterface", "stdClass" given');

        $this->constraint->definition = new stdClass();
        $this->assertValid(true, 'large');
    }

    /**
     * @covers \Netgen\BlockManager\Validator\BlockViewTypeValidator::validate
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
