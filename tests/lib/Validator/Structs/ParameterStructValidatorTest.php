<?php

declare(strict_types=1);

namespace Netgen\BlockManager\Tests\Validator\Structs;

use Netgen\BlockManager\API\Values\Block\BlockCreateStruct;
use Netgen\BlockManager\Block\BlockDefinition;
use Netgen\BlockManager\Parameters\CompoundParameterDefinition;
use Netgen\BlockManager\Parameters\ParameterDefinition;
use Netgen\BlockManager\Parameters\ParameterType;
use Netgen\BlockManager\Tests\Parameters\Stubs\ParameterDefinitionCollection;
use Netgen\BlockManager\Tests\TestCase\ValidatorTestCase;
use Netgen\BlockManager\Validator\Constraint\Structs\ParameterStruct;
use Netgen\BlockManager\Validator\Structs\ParameterStructValidator;
use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\Validator\ConstraintValidatorInterface;
use Symfony\Component\Validator\Exception\UnexpectedTypeException;

final class ParameterStructValidatorTest extends ValidatorTestCase
{
    public function setUp(): void
    {
        $compoundParameter = CompoundParameterDefinition::fromArray(
            [
                'name' => 'checkbox',
                'type' => new ParameterType\Compound\BooleanType(),
                'parameterDefinitions' => [
                    'param' => ParameterDefinition::fromArray(
                        [
                            'name' => 'param',
                            'type' => new ParameterType\IdentifierType(),
                            'isRequired' => true,
                        ]
                    ),
                ],
            ]
        );

        $this->constraint = new ParameterStruct(
            [
                'parameterDefinitions' => new ParameterDefinitionCollection(
                    [
                        'css_id' => ParameterDefinition::fromArray(
                            [
                                'name' => 'css_id',
                                'type' => new ParameterType\TextLineType(),
                                'isRequired' => true,
                            ]
                        ),
                        'checkbox' => $compoundParameter,
                    ]
                ),
                'allowMissingFields' => true,
            ]
        );

        parent::setUp();
    }

    /**
     * @covers \Netgen\BlockManager\Validator\Structs\ParameterStructValidator::buildConstraintFields
     * @covers \Netgen\BlockManager\Validator\Structs\ParameterStructValidator::getAllValues
     * @covers \Netgen\BlockManager\Validator\Structs\ParameterStructValidator::getParameterConstraints
     * @covers \Netgen\BlockManager\Validator\Structs\ParameterStructValidator::getRuntimeParameterConstraints
     * @covers \Netgen\BlockManager\Validator\Structs\ParameterStructValidator::validate
     * @dataProvider validateDataProvider
     */
    public function testValidate(array $value, bool $required, bool $isValid): void
    {
        $this->constraint->allowMissingFields = !$required;

        $blockCreateStruct = new BlockCreateStruct(new BlockDefinition());
        $blockCreateStruct->setParameterValues($value);

        $this->assertValid($isValid, $blockCreateStruct);
    }

    /**
     * @covers \Netgen\BlockManager\Validator\Structs\ParameterStructValidator::buildConstraintFields
     * @covers \Netgen\BlockManager\Validator\Structs\ParameterStructValidator::getAllValues
     * @covers \Netgen\BlockManager\Validator\Structs\ParameterStructValidator::getParameterConstraints
     * @covers \Netgen\BlockManager\Validator\Structs\ParameterStructValidator::getRuntimeParameterConstraints
     * @covers \Netgen\BlockManager\Validator\Structs\ParameterStructValidator::validate
     * @dataProvider validateDataProviderWithRuntimeConstraints
     */
    public function testValidateWithRuntimeConstraints(array $value, bool $required, bool $isValid): void
    {
        $compoundParameter = CompoundParameterDefinition::fromArray(
            [
                'name' => 'checkbox',
                'type' => new ParameterType\Compound\BooleanType(),
                'parameterDefinitions' => [
                    'param' => ParameterDefinition::fromArray(
                        [
                            'name' => 'param',
                            'type' => new ParameterType\IdentifierType(),
                            'isRequired' => true,
                        ]
                    ),
                ],
            ]
        );

        $this->constraint = new ParameterStruct(
            [
                'parameterDefinitions' => new ParameterDefinitionCollection(
                    [
                        'css_id' => ParameterDefinition::fromArray(
                            [
                                'name' => 'css_id',
                                'type' => new ParameterType\TextLineType(),
                                'isRequired' => true,
                                'constraints' => [
                                    new Length(['max' => 6]),
                                    static function (): Constraint {
                                        return new Length(['min' => 3]);
                                    },
                                ],
                            ]
                        ),
                        'checkbox' => $compoundParameter,
                    ]
                ),
                'allowMissingFields' => true,
            ]
        );

        $this->constraint->allowMissingFields = !$required;

        $blockCreateStruct = new BlockCreateStruct(new BlockDefinition());
        $blockCreateStruct->setParameterValues($value);

        $this->assertValid($isValid, $blockCreateStruct);
    }

    /**
     * @covers \Netgen\BlockManager\Validator\Structs\ParameterStructValidator::validate
     */
    public function testValidateThrowsUnexpectedTypeExceptionWithInvalidConstraint(): void
    {
        $this->expectException(UnexpectedTypeException::class);
        $this->expectExceptionMessage('Expected argument of type "Netgen\\BlockManager\\Validator\\Constraint\\Structs\\ParameterStruct", "Symfony\\Component\\Validator\\Constraints\\NotBlank" given');

        $this->constraint = new NotBlank();
        $this->assertValid(true, new BlockCreateStruct(new BlockDefinition()));
    }

    /**
     * @covers \Netgen\BlockManager\Validator\Structs\ParameterStructValidator::validate
     */
    public function testValidateThrowsUnexpectedTypeExceptionWithInvalidValue(): void
    {
        $this->expectException(UnexpectedTypeException::class);
        $this->expectExceptionMessage('Expected argument of type "Netgen\\BlockManager\\API\\Values\\ParameterStruct", "integer" given');

        $this->assertValid(true, 42);
    }

    public function validateDataProvider(): array
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

    public function validateDataProviderWithRuntimeConstraints(): array
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
