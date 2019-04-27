<?php

declare(strict_types=1);

namespace Netgen\Layouts\Tests\Validator;

use Netgen\Layouts\Block\BlockDefinition;
use Netgen\Layouts\Block\BlockDefinition\Configuration\ItemViewType;
use Netgen\Layouts\Block\BlockDefinition\Configuration\ViewType;
use Netgen\Layouts\Tests\TestCase\ValidatorTestCase;
use Netgen\Layouts\Validator\BlockItemViewTypeValidator;
use Netgen\Layouts\Validator\Constraint\BlockItemViewType;
use stdClass;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\Validator\ConstraintValidatorInterface;
use Symfony\Component\Validator\Exception\UnexpectedTypeException;

final class BlockItemViewTypeValidatorTest extends ValidatorTestCase
{
    /**
     * @var \Netgen\Layouts\Block\BlockDefinitionInterface
     */
    private $blockDefinition;

    protected function setUp(): void
    {
        $this->blockDefinition = BlockDefinition::fromArray(
            [
                'viewTypes' => [
                    'large' => ViewType::fromArray(
                        [
                            'itemViewTypes' => [
                                'standard' => new ItemViewType(),
                            ],
                        ]
                    ),
                ],
            ]
        );

        $this->constraint = new BlockItemViewType(['definition' => $this->blockDefinition]);

        parent::setUp();
    }

    /**
     * @covers \Netgen\Layouts\Validator\BlockItemViewTypeValidator::validate
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
        $this->expectExceptionMessage('Expected argument of type "Netgen\\Layouts\\Validator\\Constraint\\BlockItemViewType", "Symfony\\Component\\Validator\\Constraints\\NotBlank" given');

        $this->constraint = new NotBlank();
        $this->assertValid(true, 'standard');
    }

    /**
     * @covers \Netgen\Layouts\Validator\BlockItemViewTypeValidator::validate
     */
    public function testValidateThrowsUnexpectedTypeExceptionWithInvalidBlockDefinition(): void
    {
        $this->expectException(UnexpectedTypeException::class);
        $this->expectExceptionMessage('Expected argument of type "Netgen\\Layouts\\Block\\BlockDefinitionInterface", "stdClass" given');

        $this->constraint->definition = new stdClass();
        $this->assertValid(true, 'standard');
    }

    /**
     * @covers \Netgen\Layouts\Validator\BlockItemViewTypeValidator::validate
     */
    public function testValidateThrowsUnexpectedTypeExceptionWithInvalidViewType(): void
    {
        $this->expectException(UnexpectedTypeException::class);
        $this->expectExceptionMessage('Expected argument of type "string", "integer" given');

        $this->constraint->viewType = 42;
        $this->assertValid(true, 'standard');
    }

    /**
     * @covers \Netgen\Layouts\Validator\BlockItemViewTypeValidator::validate
     */
    public function testValidateThrowsUnexpectedTypeExceptionWithInvalidValue(): void
    {
        $this->expectException(UnexpectedTypeException::class);
        $this->expectExceptionMessage('Expected argument of type "string", "integer" given');

        $this->constraint->viewType = 'large';
        $this->assertValid(true, 42);
    }

    public function validateDataProvider(): array
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
