<?php

declare(strict_types=1);

namespace Netgen\BlockManager\Tests\Validator;

use Netgen\BlockManager\Block\BlockDefinition;
use Netgen\BlockManager\Block\BlockDefinition\Configuration\ItemViewType;
use Netgen\BlockManager\Block\BlockDefinition\Configuration\ViewType;
use Netgen\BlockManager\Tests\TestCase\ValidatorTestCase;
use Netgen\BlockManager\Validator\BlockItemViewTypeValidator;
use Netgen\BlockManager\Validator\Constraint\BlockItemViewType;
use stdClass;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\Validator\ConstraintValidatorInterface;

final class BlockItemViewTypeValidatorTest extends ValidatorTestCase
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
     * @covers \Netgen\BlockManager\Validator\BlockItemViewTypeValidator::validate
     * @dataProvider validateDataProvider
     */
    public function testValidate(string $viewType, string $value, bool $isValid): void
    {
        $this->constraint->viewType = $viewType;

        $this->assertValid($isValid, $value);
    }

    /**
     * @covers \Netgen\BlockManager\Validator\BlockItemViewTypeValidator::validate
     * @expectedException \Symfony\Component\Validator\Exception\UnexpectedTypeException
     * @expectedExceptionMessage Expected argument of type "Netgen\BlockManager\Validator\Constraint\BlockItemViewType", "Symfony\Component\Validator\Constraints\NotBlank" given
     */
    public function testValidateThrowsUnexpectedTypeExceptionWithInvalidConstraint(): void
    {
        $this->constraint = new NotBlank();
        $this->assertValid(true, 'standard');
    }

    /**
     * @covers \Netgen\BlockManager\Validator\BlockItemViewTypeValidator::validate
     * @expectedException \Symfony\Component\Validator\Exception\UnexpectedTypeException
     * @expectedExceptionMessage Expected argument of type "Netgen\BlockManager\Block\BlockDefinitionInterface", "stdClass" given
     */
    public function testValidateThrowsUnexpectedTypeExceptionWithInvalidBlockDefinition(): void
    {
        $this->constraint->definition = new stdClass();
        $this->assertValid(true, 'standard');
    }

    /**
     * @covers \Netgen\BlockManager\Validator\BlockItemViewTypeValidator::validate
     * @expectedException \Symfony\Component\Validator\Exception\UnexpectedTypeException
     * @expectedExceptionMessage Expected argument of type "string", "integer" given
     */
    public function testValidateThrowsUnexpectedTypeExceptionWithInvalidViewType(): void
    {
        $this->constraint->viewType = 42;
        $this->assertValid(true, 'standard');
    }

    /**
     * @covers \Netgen\BlockManager\Validator\BlockItemViewTypeValidator::validate
     * @expectedException \Symfony\Component\Validator\Exception\UnexpectedTypeException
     * @expectedExceptionMessage Expected argument of type "string", "integer" given
     */
    public function testValidateThrowsUnexpectedTypeExceptionWithInvalidValue(): void
    {
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
