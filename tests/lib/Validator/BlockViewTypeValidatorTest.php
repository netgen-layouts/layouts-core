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
     * @expectedException \Symfony\Component\Validator\Exception\UnexpectedTypeException
     * @expectedExceptionMessage Expected argument of type "Netgen\BlockManager\Validator\Constraint\BlockViewType", "Symfony\Component\Validator\Constraints\NotBlank" given
     */
    public function testValidateThrowsUnexpectedTypeExceptionWithInvalidConstraint(): void
    {
        $this->constraint = new NotBlank();
        $this->assertValid(true, 'large');
    }

    /**
     * @covers \Netgen\BlockManager\Validator\BlockViewTypeValidator::validate
     * @expectedException \Symfony\Component\Validator\Exception\UnexpectedTypeException
     * @expectedExceptionMessage Expected argument of type "Netgen\BlockManager\Block\BlockDefinitionInterface", "stdClass" given
     */
    public function testValidateThrowsUnexpectedTypeExceptionWithInvalidBlockDefinition(): void
    {
        $this->constraint->definition = new stdClass();
        $this->assertValid(true, 'large');
    }

    /**
     * @covers \Netgen\BlockManager\Validator\BlockViewTypeValidator::validate
     * @expectedException \Symfony\Component\Validator\Exception\UnexpectedTypeException
     * @expectedExceptionMessage Expected argument of type "string", "integer" given
     */
    public function testValidateThrowsUnexpectedTypeExceptionWithInvalidValue(): void
    {
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
