<?php

declare(strict_types=1);

namespace Netgen\Layouts\Tests\Validator\Structs;

use Netgen\Layouts\API\Values\Block\BlockUpdateStruct;
use Netgen\Layouts\API\Values\Config\ConfigStruct;
use Netgen\Layouts\Block\BlockDefinition;
use Netgen\Layouts\Config\ConfigDefinition;
use Netgen\Layouts\Tests\Config\Stubs\ConfigDefinitionAware;
use Netgen\Layouts\Tests\Config\Stubs\ConfigDefinitionHandler;
use Netgen\Layouts\Tests\TestCase\ValidatorTestCase;
use Netgen\Layouts\Utils\Hydrator;
use Netgen\Layouts\Validator\Constraint\Structs\ConfigAwareStruct as ConfigAwareStructConstraint;
use Netgen\Layouts\Validator\Structs\ConfigAwareStructValidator;
use stdClass;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\Validator\ConstraintValidatorInterface;
use Symfony\Component\Validator\Exception\UnexpectedTypeException;

final class ConfigAwareStructValidatorTest extends ValidatorTestCase
{
    protected function setUp(): void
    {
        $this->constraint = new ConfigAwareStructConstraint();

        $handler = new ConfigDefinitionHandler();

        $this->constraint->payload = ConfigDefinitionAware::fromArray(
            [
                'configDefinitions' => [
                    'config' => ConfigDefinition::fromArray(
                        [
                            'parameterDefinitions' => $handler->getParameterDefinitions(),
                        ],
                    ),
                ],
            ],
        );

        parent::setUp();
    }

    /**
     * @param mixed[] $value
     *
     * @covers \Netgen\Layouts\Validator\Structs\ConfigAwareStructValidator::validate
     *
     * @dataProvider validateDataProvider
     */
    public function testValidate(array $value, bool $isValid): void
    {
        $blockUpdateStruct = new BlockUpdateStruct();
        (new Hydrator())->hydrate($value, $blockUpdateStruct);

        $this->assertValid($isValid, $blockUpdateStruct);
    }

    /**
     * @covers \Netgen\Layouts\Validator\Structs\ConfigAwareStructValidator::validate
     */
    public function testValidateThrowsUnexpectedTypeExceptionWithInvalidConstraint(): void
    {
        $this->expectException(UnexpectedTypeException::class);
        $this->expectExceptionMessage('Expected argument of type "Netgen\Layouts\Validator\Constraint\Structs\ConfigAwareStruct", "Symfony\Component\Validator\Constraints\NotBlank" given');

        $this->constraint = new NotBlank();
        $this->assertValid(true, new BlockUpdateStruct());
    }

    /**
     * @covers \Netgen\Layouts\Validator\Structs\ConfigAwareStructValidator::validate
     */
    public function testValidateThrowsUnexpectedTypeExceptionWithInvalidPayload(): void
    {
        $this->expectException(UnexpectedTypeException::class);
        $this->expectExceptionMessage('Expected argument of type "Netgen\Layouts\Config\ConfigDefinitionAwareInterface or array", "stdClass" given');

        $this->constraint->payload = new stdClass();
        $this->assertValid(true, new BlockUpdateStruct());
    }

    /**
     * @covers \Netgen\Layouts\Validator\Structs\ConfigAwareStructValidator::validate
     */
    public function testValidateThrowsUnexpectedTypeExceptionWithInvalidValue(): void
    {
        $this->expectException(UnexpectedTypeException::class);
        $this->expectExceptionMessageMatches('/^Expected argument of type "Netgen\\\Layouts\\\API\\\Values\\\Config\\\ConfigAwareStruct", "int(eger)?" given$/');

        $this->constraint->payload = new BlockDefinition();
        $this->assertValid(true, 42);
    }

    public static function validateDataProvider(): iterable
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
                            new ConfigStruct(),
                        ),
                        'other' => (new Hydrator())->hydrate(
                            [
                                'parameterValues' => [
                                    'param' => null,
                                ],
                            ],
                            new ConfigStruct(),
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
                            new ConfigStruct(),
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
                            new ConfigStruct(),
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
                            new ConfigStruct(),
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
                            new ConfigStruct(),
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
