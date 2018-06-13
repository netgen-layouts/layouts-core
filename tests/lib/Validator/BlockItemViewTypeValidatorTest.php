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

final class BlockItemViewTypeValidatorTest extends ValidatorTestCase
{
    /**
     * @var \Netgen\BlockManager\Block\BlockDefinitionInterface
     */
    private $blockDefinition;

    public function setUp()
    {
        $this->blockDefinition = new BlockDefinition(
            [
                'viewTypes' => [
                    'large' => new ViewType(
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
     * @return \Symfony\Component\Validator\ConstraintValidator
     */
    public function getValidator()
    {
        return new BlockItemViewTypeValidator();
    }

    /**
     * @param string $viewType
     * @param string $value
     * @param bool $isValid
     *
     * @covers \Netgen\BlockManager\Validator\BlockItemViewTypeValidator::validate
     * @dataProvider validateDataProvider
     */
    public function testValidate($viewType, $value, $isValid)
    {
        $this->constraint->viewType = $viewType;

        $this->assertValid($isValid, $value);
    }

    /**
     * @covers \Netgen\BlockManager\Validator\BlockItemViewTypeValidator::validate
     * @expectedException \Symfony\Component\Validator\Exception\UnexpectedTypeException
     * @expectedExceptionMessage Expected argument of type "Netgen\BlockManager\Validator\Constraint\BlockItemViewType", "Symfony\Component\Validator\Constraints\NotBlank" given
     */
    public function testValidateThrowsUnexpectedTypeExceptionWithInvalidConstraint()
    {
        $this->constraint = new NotBlank();
        $this->assertValid(true, 'standard');
    }

    /**
     * @covers \Netgen\BlockManager\Validator\BlockItemViewTypeValidator::validate
     * @expectedException \Symfony\Component\Validator\Exception\UnexpectedTypeException
     * @expectedExceptionMessage Expected argument of type "Netgen\BlockManager\Block\BlockDefinitionInterface", "stdClass" given
     */
    public function testValidateThrowsUnexpectedTypeExceptionWithInvalidBlockDefinition()
    {
        $this->constraint->definition = new stdClass();
        $this->assertValid(true, 'standard');
    }

    /**
     * @covers \Netgen\BlockManager\Validator\BlockItemViewTypeValidator::validate
     * @expectedException \Symfony\Component\Validator\Exception\UnexpectedTypeException
     * @expectedExceptionMessage Expected argument of type "string", "integer" given
     */
    public function testValidateThrowsUnexpectedTypeExceptionWithInvalidViewType()
    {
        $this->constraint->viewType = 42;
        $this->assertValid(true, 'standard');
    }

    /**
     * @covers \Netgen\BlockManager\Validator\BlockItemViewTypeValidator::validate
     * @expectedException \Symfony\Component\Validator\Exception\UnexpectedTypeException
     * @expectedExceptionMessage Expected argument of type "string", "integer" given
     */
    public function testValidateThrowsUnexpectedTypeExceptionWithInvalidValue()
    {
        $this->constraint->viewType = 'large';
        $this->assertValid(true, 42);
    }

    public function validateDataProvider()
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
}
