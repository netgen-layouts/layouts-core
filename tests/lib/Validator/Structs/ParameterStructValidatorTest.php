<?php

declare(strict_types=1);

namespace Netgen\Layouts\Tests\Validator\Structs;

use Netgen\Layouts\API\Values\Block\BlockCreateStruct;
use Netgen\Layouts\Block\BlockDefinition;
use Netgen\Layouts\Parameters\ParameterDefinition;
use Netgen\Layouts\Parameters\ParameterType;
use Netgen\Layouts\Tests\Parameters\Stubs\ParameterDefinitionCollection;
use Netgen\Layouts\Tests\TestCase\ValidatorTestCase;
use Netgen\Layouts\Validator\Constraint\Structs\ParameterStruct;
use Netgen\Layouts\Validator\Structs\ParameterStructValidator;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\DataProvider;
use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\Validator\ConstraintValidatorInterface;
use Symfony\Component\Validator\Exception\UnexpectedTypeException;

#[CoversClass(ParameterStructValidator::class)]
final class ParameterStructValidatorTest extends ValidatorTestCase
{
    protected function setUp(): void
    {
        $compoundDefinition = ParameterDefinition::fromArray(
            [
                'name' => 'checkbox',
                'type' => new ParameterType\Compound\BooleanType(),
                'isRequired' => false,
                'defaultValue' => null,
                'parameterDefinitions' => [
                    'param' => ParameterDefinition::fromArray(
                        [
                            'name' => 'param',
                            'type' => new ParameterType\IdentifierType(),
                            'isRequired' => true,
                            'defaultValue' => null,
                        ],
                    ),
                ],
            ],
        );

        $this->constraint = new ParameterStruct(
            parameterDefinitions: new ParameterDefinitionCollection(
                [
                    'css_id' => ParameterDefinition::fromArray(
                        [
                            'name' => 'css_id',
                            'type' => new ParameterType\TextLineType(),
                            'isRequired' => true,
                            'defaultValue' => null,
                        ],
                    ),
                    'checkbox' => $compoundDefinition,
                ],
            ),
            allowMissingFields: true,
        );

        parent::setUp();
    }

    /**
     * @param mixed[] $parameters
     */
    #[DataProvider('validateDataProvider')]
    public function testValidate(array $parameters, bool $required, bool $isValid): void
    {
        $this->constraint->allowMissingFields = !$required;

        $blockCreateStruct = new BlockCreateStruct(new BlockDefinition());

        foreach ($parameters as $parameterName => $value) {
            $blockCreateStruct->setParameterValue($parameterName, $value);
        }

        $this->assertValid($isValid, $blockCreateStruct);
    }

    /**
     * @param mixed[] $parameters
     */
    #[DataProvider('validateWithRuntimeConstraintsDataProvider')]
    public function testValidateWithRuntimeConstraints(array $parameters, bool $required, bool $isValid): void
    {
        $compoundDefinition = ParameterDefinition::fromArray(
            [
                'name' => 'checkbox',
                'type' => new ParameterType\Compound\BooleanType(),
                'isRequired' => false,
                'defaultValue' => null,
                'parameterDefinitions' => [
                    'param' => ParameterDefinition::fromArray(
                        [
                            'name' => 'param',
                            'type' => new ParameterType\IdentifierType(),
                            'isRequired' => true,
                            'defaultValue' => null,
                        ],
                    ),
                ],
            ],
        );

        $this->constraint = new ParameterStruct(
            parameterDefinitions: new ParameterDefinitionCollection(
                [
                    'css_id' => ParameterDefinition::fromArray(
                        [
                            'name' => 'css_id',
                            'type' => new ParameterType\TextLineType(),
                            'isRequired' => true,
                            'defaultValue' => null,
                            'constraints' => [
                                new Length(max: 6),
                                static fn (): Constraint => new Length(min: 3),
                            ],
                        ],
                    ),
                    'checkbox' => $compoundDefinition,
                ],
            ),
            allowMissingFields: true,
        );

        $this->constraint->allowMissingFields = !$required;

        $blockCreateStruct = new BlockCreateStruct(new BlockDefinition());

        foreach ($parameters as $parameterName => $value) {
            $blockCreateStruct->setParameterValue($parameterName, $value);
        }

        $this->assertValid($isValid, $blockCreateStruct);
    }

    public function testValidateThrowsUnexpectedTypeExceptionWithInvalidConstraint(): void
    {
        $this->expectException(UnexpectedTypeException::class);
        $this->expectExceptionMessage('Expected argument of type "Netgen\Layouts\Validator\Constraint\Structs\ParameterStruct", "Symfony\Component\Validator\Constraints\NotBlank" given');

        $this->constraint = new NotBlank();
        $this->assertValid(true, new BlockCreateStruct(new BlockDefinition()));
    }

    public function testValidateThrowsUnexpectedTypeExceptionWithInvalidValue(): void
    {
        $this->expectException(UnexpectedTypeException::class);
        $this->expectExceptionMessage('Expected argument of type "Netgen\Layouts\API\Values\ParameterStruct", "int" given');

        $this->assertValid(true, 42);
    }

    public static function validateDataProvider(): iterable
    {
        return [
            [['css_id' => 'ID', 'checkbox' => true, 'param' => 'value'], true, true],
            [['css_id' => '', 'checkbox' => true, 'param' => 'value'], true, false],
            [['css_id' => null, 'checkbox' => true, 'param' => 'value'], true, false],
            [['checkbox' => true, 'param' => 'value'], true, false],
            [['css_id' => 'ID', 'checkbox' => true, 'param' => 'value'], false, true],
            [['css_id' => '', 'checkbox' => true, 'param' => 'value'], false, false],
            [['css_id' => null, 'checkbox' => true, 'param' => 'value'], false, false],
            [['checkbox' => true, 'param' => 'value'], false, true],
            [['css_id' => 'ID', 'checkbox' => true, 'param' => 'value'], true, true],
            [['css_id' => 'ID', 'checkbox' => true, 'param' => '?'], true, false],
            [['css_id' => 'ID', 'checkbox' => true, 'param' => ''], true, false],
            [['css_id' => 'ID', 'checkbox' => true, 'param' => null], true, true],
            [['css_id' => 'ID', 'checkbox' => true], true, true],
            [['css_id' => 'ID', 'checkbox' => false, 'param' => 'value'], true, true],
            [['css_id' => 'ID', 'checkbox' => false, 'param' => '?'], true, false],
            [['css_id' => 'ID', 'checkbox' => false, 'param' => ''], true, false],
            [['css_id' => 'ID', 'checkbox' => false, 'param' => null], true, true],
            [['css_id' => 'ID', 'checkbox' => false], true, true],
            [['css_id' => 'ID', 'checkbox' => null, 'param' => 'value'], true, true],
            [['css_id' => 'ID', 'checkbox' => null, 'param' => '?'], true, false],
            [['css_id' => 'ID', 'checkbox' => null, 'param' => ''], true, false],
            [['css_id' => 'ID', 'checkbox' => null, 'param' => null], true, true],
            [['css_id' => 'ID', 'checkbox' => null], true, true],
            [['css_id' => 'ID', 'param' => 'value'], true, true],
            [['css_id' => 'ID', 'param' => '?'], true, false],
            [['css_id' => 'ID', 'param' => ''], true, false],
            [['css_id' => 'ID', 'param' => null], true, true],
            [['css_id' => 'ID'], true, true],
            [['css_id' => 'ID', 'checkbox' => true, 'param' => 'value'], false, true],
            [['css_id' => 'ID', 'checkbox' => true, 'param' => '?'], false, false],
            [['css_id' => 'ID', 'checkbox' => true, 'param' => ''], false, false],
            [['css_id' => 'ID', 'checkbox' => true, 'param' => null], false, true],
            [['css_id' => 'ID', 'checkbox' => true], false, true],
            [['css_id' => 'ID', 'checkbox' => false, 'param' => 'value'], false, true],
            [['css_id' => 'ID', 'checkbox' => false, 'param' => '?'], false, false],
            [['css_id' => 'ID', 'checkbox' => false, 'param' => ''], false, false],
            [['css_id' => 'ID', 'checkbox' => false, 'param' => null], false, true],
            [['css_id' => 'ID', 'checkbox' => false], false, true],
            [['css_id' => 'ID', 'checkbox' => null, 'param' => 'value'], false, true],
            [['css_id' => 'ID', 'checkbox' => null, 'param' => '?'], false, false],
            [['css_id' => 'ID', 'checkbox' => null, 'param' => ''], false, false],
            [['css_id' => 'ID', 'checkbox' => null, 'param' => null], false, true],
            [['css_id' => 'ID', 'checkbox' => null], false, true],
            [['css_id' => 'ID', 'param' => 'value'], false, true],
            [['css_id' => 'ID', 'param' => '?'], false, false],
            [['css_id' => 'ID', 'param' => ''], false, false],
            [['css_id' => 'ID', 'param' => null], false, true],
            [['css_id' => 'ID'], false, true],
        ];
    }

    public static function validateWithRuntimeConstraintsDataProvider(): iterable
    {
        return [
            [['css_id' => 'fo'], true, false],
            [['css_id' => 'fooo'], true, true],
            [['css_id' => 'fooooooo'], true, false],
            [['css_id' => ''], true, false],
            [['css_id' => 'fo'], false, false],
            [['css_id' => 'fooo'], false, true],
            [['css_id' => 'fooooooo'], false, false],
            [['css_id' => ''], false, false],
        ];
    }

    protected function getValidator(): ConstraintValidatorInterface
    {
        return new ParameterStructValidator();
    }
}
