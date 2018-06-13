<?php

declare(strict_types=1);

namespace Netgen\BlockManager\Tests\Validator\Structs;

use Netgen\BlockManager\API\Values\Block\BlockCreateStruct;
use Netgen\BlockManager\Block\BlockDefinition;
use Netgen\BlockManager\Block\BlockDefinition\Configuration\ItemViewType;
use Netgen\BlockManager\Block\BlockDefinition\Configuration\ViewType;
use Netgen\BlockManager\Block\ContainerDefinition;
use Netgen\BlockManager\Tests\Block\Stubs\BlockDefinitionHandlerWithRequiredParameter;
use Netgen\BlockManager\Tests\Block\Stubs\ContainerDefinitionHandler;
use Netgen\BlockManager\Tests\TestCase\ValidatorTestCase;
use Netgen\BlockManager\Validator\Constraint\Structs\BlockCreateStruct as BlockCreateStructConstraint;
use Netgen\BlockManager\Validator\Structs\BlockCreateStructValidator;
use Symfony\Component\Validator\Constraints\NotBlank;

final class BlockCreateStructValidatorTest extends ValidatorTestCase
{
    public function setUp()
    {
        $this->constraint = new BlockCreateStructConstraint();

        parent::setUp();
    }

    /**
     * @return \Symfony\Component\Validator\ConstraintValidator
     */
    public function getValidator()
    {
        return new BlockCreateStructValidator();
    }

    /**
     * @param array $value
     * @param bool $isValid
     *
     * @covers \Netgen\BlockManager\Validator\Structs\BlockCreateStructValidator::validate
     * @dataProvider validateDataProvider
     */
    public function testValidate($value, $isValid)
    {
        $this->assertValid($isValid, new BlockCreateStruct($value));
    }

    /**
     * @covers \Netgen\BlockManager\Validator\Structs\BlockCreateStructValidator::validate
     * @expectedException \Symfony\Component\Validator\Exception\UnexpectedTypeException
     * @expectedExceptionMessage Expected argument of type "Netgen\BlockManager\Validator\Constraint\Structs\BlockCreateStruct", "Symfony\Component\Validator\Constraints\NotBlank" given
     */
    public function testValidateThrowsUnexpectedTypeExceptionWithInvalidConstraint()
    {
        $this->constraint = new NotBlank();
        $this->assertValid(true, new BlockCreateStruct());
    }

    /**
     * @covers \Netgen\BlockManager\Validator\Structs\BlockCreateStructValidator::validate
     * @expectedException \Symfony\Component\Validator\Exception\UnexpectedTypeException
     * @expectedExceptionMessage Expected argument of type "Netgen\BlockManager\API\Values\Block\BlockCreateStruct", "integer" given
     */
    public function testValidateThrowsUnexpectedTypeExceptionWithInvalidValue()
    {
        $this->assertValid(true, 42);
    }

    /**
     * @covers \Netgen\BlockManager\Validator\Structs\BlockCreateStructValidator::validate
     * @expectedException \Symfony\Component\Validator\Exception\UnexpectedTypeException
     * @expectedExceptionMessage Expected argument of type "Netgen\BlockManager\Block\BlockDefinitionInterface", "integer" given
     */
    public function testValidateThrowsUnexpectedTypeExceptionWithInvalidBlockDefinition()
    {
        $this->assertValid(true, new BlockCreateStruct(['definition' => 42]));
    }

    public function validateDataProvider()
    {
        return [
            [
                [
                    'definition' => $this->getBlockDefinition(),
                    'viewType' => 'large',
                    'itemViewType' => 'standard',
                    'name' => 'My block',
                    'isTranslatable' => false,
                    'alwaysAvailable' => true,
                    'parameterValues' => [
                        'css_class' => 'class',
                        'css_id' => 'id',
                    ],
                ],
                true,
            ],
            [
                [
                    'definition' => $this->getBlockDefinition(),
                    'viewType' => 'nonexistent',
                    'itemViewType' => 'standard',
                    'name' => 'My block',
                    'isTranslatable' => false,
                    'alwaysAvailable' => true,
                    'parameterValues' => [
                        'css_class' => 'class',
                        'css_id' => 'id',
                    ],
                ],
                false,
            ],
            [
                [
                    'definition' => $this->getBlockDefinition(),
                    'viewType' => '',
                    'itemViewType' => 'standard',
                    'name' => 'My block',
                    'isTranslatable' => false,
                    'alwaysAvailable' => true,
                    'parameterValues' => [
                        'css_class' => 'class',
                        'css_id' => 'id',
                    ],
                ],
                false,
            ],
            [
                [
                    'definition' => $this->getBlockDefinition(),
                    'viewType' => 'large',
                    'itemViewType' => 'nonexistent',
                    'name' => 'My block',
                    'isTranslatable' => false,
                    'alwaysAvailable' => true,
                    'parameterValues' => [
                        'css_class' => 'class',
                        'css_id' => 'id',
                    ],
                ],
                false,
            ],
            [
                [
                    'definition' => $this->getBlockDefinition(),
                    'viewType' => 'large',
                    'itemViewType' => '',
                    'name' => 'My block',
                    'isTranslatable' => false,
                    'alwaysAvailable' => true,
                    'parameterValues' => [
                        'css_class' => 'class',
                        'css_id' => 'id',
                    ],
                ],
                false,
            ],
            [
                [
                    'definition' => $this->getBlockDefinition(),
                    'viewType' => 'large',
                    'itemViewType' => 'standard',
                    'name' => null,
                    'isTranslatable' => false,
                    'alwaysAvailable' => true,
                    'parameterValues' => [
                        'css_class' => 'class',
                        'css_id' => 'id',
                    ],
                ],
                true,
            ],
            [
                [
                    'definition' => $this->getBlockDefinition(),
                    'viewType' => 'large',
                    'itemViewType' => 'standard',
                    'name' => '',
                    'isTranslatable' => false,
                    'alwaysAvailable' => true,
                    'parameterValues' => [
                        'css_class' => 'class',
                        'css_id' => 'id',
                    ],
                ],
                true,
            ],
            [
                [
                    'definition' => $this->getBlockDefinition(),
                    'viewType' => 'large',
                    'itemViewType' => 'standard',
                    'name' => 42,
                    'isTranslatable' => false,
                    'alwaysAvailable' => true,
                    'parameterValues' => [
                        'css_class' => 'class',
                        'css_id' => 'id',
                    ],
                ],
                false,
            ],
            [
                [
                    'definition' => $this->getBlockDefinition(),
                    'viewType' => 'large',
                    'itemViewType' => 'standard',
                    'name' => 'My block',
                    'isTranslatable' => true,
                    'alwaysAvailable' => true,
                    'parameterValues' => [
                        'css_class' => 'class',
                        'css_id' => 'id',
                    ],
                ],
                true,
            ],
            [
                [
                    'definition' => $this->getBlockDefinition(),
                    'viewType' => 'large',
                    'itemViewType' => 'standard',
                    'name' => 'My block',
                    'isTranslatable' => null,
                    'alwaysAvailable' => true,
                    'parameterValues' => [
                        'css_class' => 'class',
                        'css_id' => 'id',
                    ],
                ],
                false,
            ],
            [
                [
                    'definition' => $this->getBlockDefinition(),
                    'viewType' => 'large',
                    'itemViewType' => 'standard',
                    'name' => 'My block',
                    'isTranslatable' => 42,
                    'alwaysAvailable' => true,
                    'parameterValues' => [
                        'css_class' => 'class',
                        'css_id' => 'id',
                    ],
                ],
                false,
            ],
            [
                [
                    'definition' => $this->getBlockDefinition(),
                    'viewType' => 'large',
                    'itemViewType' => 'standard',
                    'name' => 'My block',
                    'isTranslatable' => false,
                    'alwaysAvailable' => null,
                    'parameterValues' => [
                        'css_class' => 'class',
                        'css_id' => 'id',
                    ],
                ],
                false,
            ],
            [
                [
                    'definition' => $this->getBlockDefinition(),
                    'viewType' => 'large',
                    'itemViewType' => 'standard',
                    'name' => 'My block',
                    'isTranslatable' => false,
                    'alwaysAvailable' => 42,
                    'parameterValues' => [
                        'css_class' => 'class',
                        'css_id' => 'id',
                    ],
                ],
                false,
            ],
            [
                [
                    'definition' => $this->getBlockDefinition(),
                    'viewType' => 'large',
                    'itemViewType' => 'standard',
                    'name' => 'My block',
                    'isTranslatable' => false,
                    'alwaysAvailable' => true,
                    'parameterValues' => [
                        'css_class' => '',
                        'css_id' => 'id',
                    ],
                ],
                false,
            ],
            [
                [
                    'definition' => $this->getBlockDefinition(),
                    'viewType' => 'large',
                    'itemViewType' => 'standard',
                    'name' => 'My block',
                    'isTranslatable' => false,
                    'alwaysAvailable' => true,
                    'parameterValues' => [
                        'css_class' => null,
                        'css_id' => 'id',
                    ],
                ],
                false,
            ],
            [
                [
                    'definition' => $this->getBlockDefinition(),
                    'viewType' => 'large',
                    'itemViewType' => 'standard',
                    'name' => 'My block',
                    'isTranslatable' => false,
                    'alwaysAvailable' => true,
                    'parameterValues' => [
                        'css_id' => 'id',
                    ],
                ],
                false,
            ],
            [
                [
                    'definition' => $this->getBlockDefinition(),
                    'viewType' => 'large',
                    'itemViewType' => 'standard',
                    'name' => 'My block',
                    'isTranslatable' => false,
                    'alwaysAvailable' => true,
                    'parameterValues' => [
                        'css_class' => 'class',
                        'css_id' => '',
                    ],
                ],
                true,
            ],
            [
                [
                    'definition' => $this->getBlockDefinition(),
                    'viewType' => 'large',
                    'itemViewType' => 'standard',
                    'name' => 'My block',
                    'isTranslatable' => false,
                    'alwaysAvailable' => true,
                    'parameterValues' => [
                        'css_class' => 'class',
                        'css_id' => null,
                    ],
                ],
                true,
            ],
            [
                [
                    'definition' => $this->getBlockDefinition(),
                    'viewType' => 'large',
                    'itemViewType' => 'standard',
                    'name' => 'My block',
                    'isTranslatable' => false,
                    'alwaysAvailable' => true,
                    'parameterValues' => [
                        'css_class' => 'class',
                    ],
                ],
                true,
            ],
            [
                [
                    'definition' => $this->getBlockDefinition(),
                    'viewType' => 'large',
                    'itemViewType' => 'standard',
                    'name' => 'My block',
                    'isTranslatable' => false,
                    'alwaysAvailable' => true,
                ],
                false,
            ],
            [
                [
                    'definition' => $this->getBlockDefinition(),
                    'viewType' => 'large',
                    'itemViewType' => 'standard',
                    'name' => 'My block',
                    'isTranslatable' => false,
                    'alwaysAvailable' => true,
                    'parameterValues' => [],
                ],
                false,
            ],

            // Container block definitions

            [
                [
                    'definition' => $this->getContainerDefinition(),
                    'viewType' => 'large',
                    'itemViewType' => 'standard',
                    'name' => 'My block',
                    'isTranslatable' => false,
                    'alwaysAvailable' => true,
                    'parameterValues' => [
                        'css_class' => 'class',
                        'css_id' => 'id',
                    ],
                ],
                true,
            ],

            [
                [
                    'definition' => $this->getBlockDefinition(),
                    'viewType' => 'large',
                    'itemViewType' => 'standard',
                    'name' => 'My block',
                    'isTranslatable' => false,
                    'alwaysAvailable' => true,
                    'parameterValues' => [
                        'css_class' => 'class',
                        'css_id' => 'id',
                    ],
                ],
                true,
            ],
            [
                [
                    'definition' => $this->getBlockDefinition(),
                    'viewType' => 'large',
                    'itemViewType' => 'standard',
                    'name' => 'My block',
                    'isTranslatable' => false,
                    'alwaysAvailable' => true,
                    'parameterValues' => [
                        'css_class' => 'class',
                        'css_id' => 'id',
                    ],
                ],
                true,
            ],
        ];
    }

    private function getBlockDefinition()
    {
        $handler = new BlockDefinitionHandlerWithRequiredParameter();

        return new BlockDefinition(
            [
                'parameterDefinitions' => $handler->getParameterDefinitions(),
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
    }

    /**
     * @return \Netgen\BlockManager\Block\BlockDefinitionInterface
     */
    private function getContainerDefinition()
    {
        return new ContainerDefinition(
            [
                'handler' => new ContainerDefinitionHandler([], ['main']),
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
    }
}
