<?php

namespace Netgen\BlockManager\Tests\Validator\Structs;

use Netgen\BlockManager\API\Values\Block\BlockUpdateStruct;
use Netgen\BlockManager\API\Values\Config\ConfigStruct;
use Netgen\BlockManager\Core\Values\Block\Block;
use Netgen\BlockManager\Core\Values\Config\Config;
use Netgen\BlockManager\Tests\Config\Stubs\ConfigDefinition;
use Netgen\BlockManager\Tests\Core\Stubs\ConfigAwareValue;
use Netgen\BlockManager\Tests\TestCase\ValidatorTestCase;
use Netgen\BlockManager\Validator\Constraint\Structs\ConfigAwareStruct as ConfigAwareStructConstraint;
use Netgen\BlockManager\Validator\Structs\ConfigAwareStructValidator;
use stdClass;
use Symfony\Component\Validator\Constraints\NotBlank;

final class ConfigAwareStructValidatorTest extends ValidatorTestCase
{
    public function setUp()
    {
        $this->constraint = new ConfigAwareStructConstraint();

        $this->constraint->payload = new ConfigAwareValue(
            [
                'configs' => [
                    'config' => new Config(
                        [
                            'configKey' => 'config',
                            'definition' => new ConfigDefinition('config'),
                        ]
                    ),
                ],
            ]
        );

        parent::setUp();
    }

    /**
     * @return \Symfony\Component\Validator\ConstraintValidator
     */
    public function getValidator()
    {
        return new ConfigAwareStructValidator();
    }

    /**
     * @param array $value
     * @param bool $isValid
     *
     * @covers \Netgen\BlockManager\Validator\Structs\ConfigAwareStructValidator::validate
     * @dataProvider validateDataProvider
     */
    public function testValidate($value, $isValid)
    {
        $this->assertValid($isValid, new BlockUpdateStruct($value));
    }

    /**
     * @covers \Netgen\BlockManager\Validator\Structs\ConfigAwareStructValidator::validate
     * @expectedException \Symfony\Component\Validator\Exception\UnexpectedTypeException
     * @expectedExceptionMessage Expected argument of type "Netgen\BlockManager\Validator\Constraint\Structs\ConfigAwareStruct", "Symfony\Component\Validator\Constraints\NotBlank" given
     */
    public function testValidateThrowsUnexpectedTypeExceptionWithInvalidConstraint()
    {
        $this->constraint = new NotBlank();
        $this->assertValid(true, new BlockUpdateStruct());
    }

    /**
     * @covers \Netgen\BlockManager\Validator\Structs\ConfigAwareStructValidator::validate
     * @expectedException \Symfony\Component\Validator\Exception\UnexpectedTypeException
     * @expectedExceptionMessage Expected argument of type "Netgen\BlockManager\API\Values\Config\ConfigAwareValue", "stdClass" given
     */
    public function testValidateThrowsUnexpectedTypeExceptionWithInvalidPayload()
    {
        $this->constraint->payload = new stdClass();
        $this->assertValid(true, new BlockUpdateStruct());
    }

    /**
     * @covers \Netgen\BlockManager\Validator\Structs\ConfigAwareStructValidator::validate
     * @expectedException \Symfony\Component\Validator\Exception\UnexpectedTypeException
     * @expectedExceptionMessage Expected argument of type "Netgen\BlockManager\API\Values\Config\ConfigAwareStruct", "integer" given
     */
    public function testValidateThrowsUnexpectedTypeExceptionWithInvalidValue()
    {
        $this->constraint->payload = new Block();
        $this->assertValid(true, 42);
    }

    public function validateDataProvider()
    {
        return [
            [
                [
                    'configStructs' => [
                        'config' => new ConfigStruct(
                            [
                                'parameterValues' => [
                                    'param' => 'value',
                                ],
                            ]
                        ),
                        'other' => new ConfigStruct(
                            [
                                'parameterValues' => [
                                    'param' => null,
                                ],
                            ]
                        ),
                    ],
                ],
                true,
            ],
            [
                [
                    'configStructs' => [
                        'config' => new ConfigStruct(
                            [
                                'parameterValues' => [
                                    'param' => 'value',
                                ],
                            ]
                        ),
                    ],
                ],
                true,
            ],
            [
                [
                    'configStructs' => [
                        'config' => new ConfigStruct(
                            [
                                'parameterValues' => [
                                    'param' => null,
                                ],
                            ]
                        ),
                    ],
                ],
                true,
            ],
            [
                [
                    'configStructs' => [
                        'config' => new ConfigStruct(
                            [
                                'parameterValues' => [
                                    'param' => 42,
                                ],
                            ]
                        ),
                    ],
                ],
                false,
            ],
            [
                [
                    'configStructs' => [
                        'config' => new ConfigStruct(
                            [
                                'parameterValues' => [],
                            ]
                        ),
                    ],
                ],
                true,
            ],
        ];
    }
}
