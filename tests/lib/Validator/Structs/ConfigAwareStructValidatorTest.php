<?php

declare(strict_types=1);

namespace Netgen\BlockManager\Tests\Validator\Structs;

use Netgen\BlockManager\API\Values\Block\BlockUpdateStruct;
use Netgen\BlockManager\API\Values\Config\ConfigStruct;
use Netgen\BlockManager\Block\BlockDefinition;
use Netgen\BlockManager\Config\ConfigDefinition;
use Netgen\BlockManager\Tests\Config\Stubs\ConfigDefinitionAware;
use Netgen\BlockManager\Tests\Config\Stubs\ConfigDefinitionHandler;
use Netgen\BlockManager\Tests\TestCase\ValidatorTestCase;
use Netgen\BlockManager\Utils\Hydrator;
use Netgen\BlockManager\Validator\Constraint\Structs\ConfigAwareStruct as ConfigAwareStructConstraint;
use Netgen\BlockManager\Validator\Constraint\Structs\ParameterStruct;
use Netgen\BlockManager\Validator\Structs\ConfigAwareStructValidator;
use stdClass;
use Symfony\Component\Validator\ConstraintValidatorInterface;

final class ConfigAwareStructValidatorTest extends ValidatorTestCase
{
    public function setUp(): void
    {
        $this->constraint = new ConfigAwareStructConstraint();

        $handler = new ConfigDefinitionHandler();

        $this->constraint->payload = ConfigDefinitionAware::fromArray(
            [
                'configDefinitions' => [
                    'config' => ConfigDefinition::fromArray(
                        [
                            'parameterDefinitions' => $handler->getParameterDefinitions(),
                        ]
                    ),
                ],
            ]
        );

        parent::setUp();
    }

    /**
     * @covers \Netgen\BlockManager\Validator\Structs\ConfigAwareStructValidator::validate
     * @dataProvider validateDataProvider
     */
    public function testValidate(array $value, bool $isValid): void
    {
        $blockUpdateStruct = new BlockUpdateStruct();
        (new Hydrator())->hydrate($value, $blockUpdateStruct);

        $this->assertValid($isValid, $blockUpdateStruct);
    }

    /**
     * @covers \Netgen\BlockManager\Validator\Structs\ConfigAwareStructValidator::validate
     * @expectedException \Symfony\Component\Validator\Exception\UnexpectedTypeException
     * @expectedExceptionMessage Expected argument of type "Netgen\BlockManager\Validator\Constraint\Structs\ConfigAwareStruct", "Netgen\BlockManager\Validator\Constraint\Structs\ParameterStruct" given
     */
    public function testValidateThrowsUnexpectedTypeExceptionWithInvalidConstraint(): void
    {
        $this->constraint = new ParameterStruct();
        $this->assertValid(true, new BlockUpdateStruct());
    }

    /**
     * @covers \Netgen\BlockManager\Validator\Structs\ConfigAwareStructValidator::validate
     * @expectedException \Symfony\Component\Validator\Exception\UnexpectedTypeException
     * @expectedExceptionMessage Expected argument of type "Netgen\BlockManager\Config\ConfigDefinitionAwareInterface or array", "stdClass" given
     */
    public function testValidateThrowsUnexpectedTypeExceptionWithInvalidPayload(): void
    {
        $this->constraint->payload = new stdClass();
        $this->assertValid(true, new BlockUpdateStruct());
    }

    /**
     * @covers \Netgen\BlockManager\Validator\Structs\ConfigAwareStructValidator::validate
     * @expectedException \Symfony\Component\Validator\Exception\UnexpectedTypeException
     * @expectedExceptionMessage Expected argument of type "Netgen\BlockManager\API\Values\Config\ConfigAwareStruct", "integer" given
     */
    public function testValidateThrowsUnexpectedTypeExceptionWithInvalidValue(): void
    {
        $this->constraint->payload = new BlockDefinition();
        $this->assertValid(true, 42);
    }

    public function validateDataProvider(): array
    {
        return [
            [
                [
                    'configStructs' => [
                        'config' => (new Hydrator())->hydrate(
                            [
                                'parameterValues' => [
                                    'param' => 'value',
                                ],
                            ],
                            new ConfigStruct()
                        ),
                        'other' => (new Hydrator())->hydrate(
                            [
                                'parameterValues' => [
                                    'param' => null,
                                ],
                            ],
                            new ConfigStruct()
                        ),
                    ],
                ],
                false,
            ],
            [
                [
                    'configStructs' => [
                        'config' => (new Hydrator())->hydrate(
                            [
                                'parameterValues' => [
                                    'param' => 'value',
                                ],
                            ],
                            new ConfigStruct()
                        ),
                    ],
                ],
                true,
            ],
            [
                [
                    'configStructs' => [
                        'config' => (new Hydrator())->hydrate(
                            [
                                'parameterValues' => [
                                    'param' => null,
                                ],
                            ],
                            new ConfigStruct()
                        ),
                    ],
                ],
                true,
            ],
            [
                [
                    'configStructs' => [
                        'config' => (new Hydrator())->hydrate(
                            [
                                'parameterValues' => [
                                    'param' => 42,
                                ],
                            ],
                            new ConfigStruct()
                        ),
                    ],
                ],
                false,
            ],
            [
                [
                    'configStructs' => [
                        'config' => (new Hydrator())->hydrate(
                            [
                                'parameterValues' => [],
                            ],
                            new ConfigStruct()
                        ),
                    ],
                ],
                true,
            ],
        ];
    }

    protected function getValidator(): ConstraintValidatorInterface
    {
        return new ConfigAwareStructValidator();
    }
}
