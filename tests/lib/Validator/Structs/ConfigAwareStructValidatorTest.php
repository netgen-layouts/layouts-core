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
use Netgen\Layouts\Validator\Constraint\Structs\ConfigAwareStruct as ConfigAwareStructConstraint;
use Netgen\Layouts\Validator\Structs\ConfigAwareStructValidator;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\DataProvider;
use stdClass;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\Validator\ConstraintValidatorInterface;
use Symfony\Component\Validator\Exception\UnexpectedTypeException;
use Symfony\Component\VarExporter\Hydrator;

#[CoversClass(ConfigAwareStructValidator::class)]
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
     */
    #[DataProvider('validateDataProvider')]
    public function testValidate(array $value, bool $isValid): void
    {
        $blockUpdateStruct = new BlockUpdateStruct();
        Hydrator::hydrate($blockUpdateStruct, $value);

        $this->assertValid($isValid, $blockUpdateStruct);
    }

    public function testValidateThrowsUnexpectedTypeExceptionWithInvalidConstraint(): void
    {
        $this->expectException(UnexpectedTypeException::class);
        $this->expectExceptionMessage('Expected argument of type "Netgen\Layouts\Validator\Constraint\Structs\ConfigAwareStruct", "Symfony\Component\Validator\Constraints\NotBlank" given');

        $this->constraint = new NotBlank();
        $this->assertValid(true, new BlockUpdateStruct());
    }

    public function testValidateThrowsUnexpectedTypeExceptionWithInvalidPayload(): void
    {
        $this->expectException(UnexpectedTypeException::class);
        $this->expectExceptionMessage('Expected argument of type "Netgen\Layouts\Config\ConfigDefinitionAwareInterface or array", "stdClass" given');

        $this->constraint->payload = new stdClass();
        $this->assertValid(true, new BlockUpdateStruct());
    }

    public function testValidateThrowsUnexpectedTypeExceptionWithInvalidValue(): void
    {
        $this->expectException(UnexpectedTypeException::class);
        $this->expectExceptionMessage('Expected argument of type "Netgen\Layouts\API\Values\Config\ConfigAwareStruct", "int" given');

        $this->constraint->payload = new BlockDefinition();
        $this->assertValid(true, 42);
    }

    /**
     * @return iterable<mixed>
     */
    public static function validateDataProvider(): iterable
    {
        return [
            [
                [
                    'configStructs' => [
                        'config' => Hydrator::hydrate(
                            new ConfigStruct(),
                            [
                                'parameterValues' => [
                                    'param' => 'value',
                                ],
                            ],
                        ),
                        'other' => Hydrator::hydrate(
                            new ConfigStruct(),
                            [
                                'parameterValues' => [
                                    'param' => null,
                                ],
                            ],
                        ),
                    ],
                ],
                false,
            ],
            [
                [
                    'configStructs' => [
                        'config' => Hydrator::hydrate(
                            new ConfigStruct(),
                            [
                                'parameterValues' => [
                                    'param' => 'value',
                                ],
                            ],
                        ),
                    ],
                ],
                true,
            ],
            [
                [
                    'configStructs' => [
                        'config' => Hydrator::hydrate(
                            new ConfigStruct(),
                            [
                                'parameterValues' => [
                                    'param' => null,
                                ],
                            ],
                        ),
                    ],
                ],
                true,
            ],
            [
                [
                    'configStructs' => [
                        'config' => Hydrator::hydrate(
                            new ConfigStruct(),
                            [
                                'parameterValues' => [
                                    'param' => 42,
                                ],
                            ],
                        ),
                    ],
                ],
                false,
            ],
            [
                [
                    'configStructs' => [
                        'config' => Hydrator::hydrate(
                            new ConfigStruct(),
                            [
                                'parameterValues' => [],
                            ],
                        ),
                    ],
                ],
                true,
            ],
        ];
    }

    protected function getConstraintValidator(): ConstraintValidatorInterface
    {
        return new ConfigAwareStructValidator();
    }
}
