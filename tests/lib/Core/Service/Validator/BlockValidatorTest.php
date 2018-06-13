<?php

declare(strict_types=1);

namespace Netgen\BlockManager\Tests\Core\Service\Validator;

use Netgen\BlockManager\API\Values\Block\BlockCreateStruct;
use Netgen\BlockManager\API\Values\Block\BlockUpdateStruct;
use Netgen\BlockManager\API\Values\Collection\CollectionCreateStruct;
use Netgen\BlockManager\Block\BlockDefinition;
use Netgen\BlockManager\Block\BlockDefinition\Configuration\ItemViewType;
use Netgen\BlockManager\Block\BlockDefinition\Configuration\ViewType;
use Netgen\BlockManager\Block\ContainerDefinition;
use Netgen\BlockManager\Core\Service\Validator\BlockValidator;
use Netgen\BlockManager\Core\Service\Validator\CollectionValidator;
use Netgen\BlockManager\Core\Values\Block\Block;
use Netgen\BlockManager\Exception\Validation\ValidationException;
use Netgen\BlockManager\Tests\Block\Stubs\BlockDefinitionHandler;
use Netgen\BlockManager\Tests\Block\Stubs\BlockDefinitionHandlerWithRequiredParameter;
use Netgen\BlockManager\Tests\Block\Stubs\ContainerDefinitionHandler;
use Netgen\BlockManager\Tests\TestCase\ValidatorFactory;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Validator\Validation;

final class BlockValidatorTest extends TestCase
{
    /**
     * @var \Symfony\Component\Validator\Validator\ValidatorInterface
     */
    private $validator;

    /**
     * @var \Netgen\BlockManager\Core\Service\Validator\BlockValidator
     */
    private $blockValidator;

    public function setUp()
    {
        $this->validator = Validation::createValidatorBuilder()
            ->setConstraintValidatorFactory(new ValidatorFactory($this))
            ->getValidator();

        $collectionValidator = new CollectionValidator();
        $collectionValidator->setValidator($this->validator);

        $this->blockValidator = new BlockValidator($collectionValidator);
        $this->blockValidator->setValidator($this->validator);
    }

    /**
     * @param array $params
     * @param bool $isValid
     *
     * @covers \Netgen\BlockManager\Core\Service\Validator\BlockValidator::__construct
     * @covers \Netgen\BlockManager\Core\Service\Validator\BlockValidator::validateBlockCreateStruct
     * @dataProvider validateBlockCreateStructDataProvider
     */
    public function testValidateBlockCreateStruct(array $params, $isValid)
    {
        if (!$isValid) {
            $this->expectException(ValidationException::class);
        }

        // Fake assertion to fix coverage on tests which do not perform assertions
        $this->assertTrue(true);

        $this->blockValidator->validateBlockCreateStruct(new BlockCreateStruct($params));
    }

    /**
     * @param array $params
     * @param bool $isValid
     *
     * @covers \Netgen\BlockManager\Core\Service\Validator\BlockValidator::validateBlockUpdateStruct
     * @dataProvider validateBlockUpdateStructDataProvider
     */
    public function testValidateBlockUpdateStruct(array $params, $isValid)
    {
        if (!$isValid) {
            $this->expectException(ValidationException::class);
        }

        // Fake assertion to fix coverage on tests which do not perform assertions
        $this->assertTrue(true);

        $this->blockValidator->validateBlockUpdateStruct(
            new Block(
                [
                    'viewType' => 'large',
                    'mainLocale' => 'en',
                    'definition' => $this->getBlockDefinition(false),
                ]
            ),
            new BlockUpdateStruct($params)
        );
    }

    public function validateBlockCreateStructDataProvider()
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
                    'definition' => null,
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
                false,
            ],
            [
                [
                    'definition' => 42,
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
                false,
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
            [
                [
                    'definition' => $this->getBlockDefinition(),
                    'viewType' => 'large',
                    'itemViewType' => 'standard',
                    'name' => 'My block',
                    'isTranslatable' => false,
                    'alwaysAvailable' => true,
                    'parameterValues' => 42,
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
                    'collectionCreateStructs' => [],
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
                    'collectionCreateStructs' => [
                        'default' => new CollectionCreateStruct(),
                    ],
                ],
                true,
            ],
        ];
    }

    public function validateBlockUpdateStructDataProvider()
    {
        return [
            [
                [
                    'locale' => 'en',
                    'alwaysAvailable' => true,
                    'viewType' => 'large',
                    'itemViewType' => 'standard',
                    'name' => 'My block',
                    'parameterValues' => [
                        'css_class' => 'class',
                        'css_id' => 'id',
                    ],
                ],
                true,
            ],
            [
                [
                    'alwaysAvailable' => true,
                    'viewType' => 'large',
                    'itemViewType' => 'standard',
                    'name' => 'My block',
                    'parameterValues' => [
                        'css_class' => 'class',
                        'css_id' => 'id',
                    ],
                ],
                false,
            ],
            [
                [
                    'locale' => null,
                    'alwaysAvailable' => true,
                    'viewType' => 'large',
                    'itemViewType' => 'standard',
                    'name' => 'My block',
                    'parameterValues' => [
                        'css_class' => 'class',
                        'css_id' => 'id',
                    ],
                ],
                false,
            ],
            [
                [
                    'locale' => '',
                    'alwaysAvailable' => true,
                    'viewType' => 'large',
                    'itemViewType' => 'standard',
                    'name' => 'My block',
                    'parameterValues' => [
                        'css_class' => 'class',
                        'css_id' => 'id',
                    ],
                ],
                false,
            ],
            [
                [
                    'locale' => 42,
                    'alwaysAvailable' => true,
                    'viewType' => 'large',
                    'itemViewType' => 'standard',
                    'name' => 'My block',
                    'parameterValues' => [
                        'css_class' => 'class',
                        'css_id' => 'id',
                    ],
                ],
                false,
            ],
            [
                [
                    'locale' => 'nonexistent',
                    'alwaysAvailable' => true,
                    'viewType' => 'large',
                    'itemViewType' => 'standard',
                    'name' => 'My block',
                    'parameterValues' => [
                        'css_class' => 'class',
                        'css_id' => 'id',
                    ],
                ],
                false,
            ],
            [
                [
                    'locale' => 'en',
                    'viewType' => 'large',
                    'itemViewType' => 'standard',
                    'name' => 'My block',
                    'parameterValues' => [
                        'css_class' => 'class',
                        'css_id' => 'id',
                    ],
                ],
                true,
            ],
            [
                [
                    'locale' => 'en',
                    'alwaysAvailable' => 42,
                    'viewType' => 'large',
                    'itemViewType' => 'standard',
                    'name' => 'My block',
                    'parameterValues' => [
                        'css_class' => 'class',
                        'css_id' => 'id',
                    ],
                ],
                false,
            ],
            [
                [
                    'locale' => 'en',
                    'alwaysAvailable' => true,
                    'viewType' => null,
                    'itemViewType' => 'standard',
                    'name' => 'My block',
                    'parameterValues' => [
                        'css_class' => 'class',
                        'css_id' => 'id',
                    ],
                ],
                true,
            ],
            [
                [
                    'locale' => 'en',
                    'alwaysAvailable' => true,
                    'viewType' => null,
                    'itemViewType' => null,
                    'name' => 'My block',
                    'parameterValues' => [
                        'css_class' => 'class',
                        'css_id' => 'id',
                    ],
                ],
                true,
            ],
            [
                [
                    'locale' => 'en',
                    'alwaysAvailable' => true,
                    'viewType' => '',
                    'itemViewType' => 'standard',
                    'name' => 'My block',
                    'parameterValues' => [
                        'css_class' => 'class',
                        'css_id' => 'id',
                    ],
                ],
                false,
            ],
            [
                [
                    'locale' => 'en',
                    'alwaysAvailable' => true,
                    'viewType' => 'large',
                    'itemViewType' => '',
                    'name' => 'My block',
                    'parameterValues' => [
                        'css_class' => 'class',
                        'css_id' => 'id',
                    ],
                ],
                false,
            ],
            [
                [
                    'locale' => 'en',
                    'alwaysAvailable' => true,
                    'viewType' => 'large',
                    'itemViewType' => 'nonexistent',
                    'name' => 'My block',
                    'parameterValues' => [
                        'css_class' => 'class',
                        'css_id' => 'id',
                    ],
                ],
                false,
            ],
            [
                [
                    'locale' => 'en',
                    'alwaysAvailable' => true,
                    'viewType' => 'large',
                    'itemViewType' => 'standard',
                    'name' => null,
                    'parameterValues' => [
                        'css_class' => 'class',
                        'css_id' => 'id',
                    ],
                ],
                true,
            ],
            [
                [
                    'locale' => 'en',
                    'alwaysAvailable' => true,
                    'viewType' => 'large',
                    'itemViewType' => 'standard',
                    'name' => '',
                    'parameterValues' => [
                        'css_class' => 'class',
                        'css_id' => 'id',
                    ],
                ],
                true,
            ],
            [
                [
                    'locale' => 'en',
                    'alwaysAvailable' => true,
                    'viewType' => 'large',
                    'itemViewType' => 'standard',
                    'name' => 42,
                    'parameterValues' => [
                        'css_class' => 'class',
                        'css_id' => 'id',
                    ],
                ],
                false,
            ],
            [
                [
                    'locale' => 'en',
                    'alwaysAvailable' => true,
                    'viewType' => 'large',
                    'itemViewType' => 'standard',
                    'name' => 'My block',
                    'parameterValues' => [
                        'css_class' => '',
                        'css_id' => 'id',
                    ],
                ],
                true,
            ],
            [
                [
                    'locale' => 'en',
                    'alwaysAvailable' => true,
                    'viewType' => 'large',
                    'itemViewType' => 'standard',
                    'name' => 'My block',
                    'parameterValues' => [
                        'css_class' => null,
                        'css_id' => 'id',
                    ],
                ],
                true,
            ],
            [
                [
                    'locale' => 'en',
                    'alwaysAvailable' => true,
                    'viewType' => 'large',
                    'itemViewType' => 'standard',
                    'name' => 'My block',
                    'parameterValues' => [
                        'css_id' => 'id',
                    ],
                ],
                true,
            ],
            [
                [
                    'locale' => 'en',
                    'alwaysAvailable' => true,
                    'viewType' => 'large',
                    'itemViewType' => 'standard',
                    'name' => 'My block',
                    'parameterValues' => [
                        'css_class' => 'class',
                        'css_id' => '',
                    ],
                ],
                true,
            ],
            [
                [
                    'locale' => 'en',
                    'alwaysAvailable' => true,
                    'viewType' => 'large',
                    'itemViewType' => 'standard',
                    'name' => 'My block',
                    'parameterValues' => [
                        'css_class' => 'class',
                        'css_id' => null,
                    ],
                ],
                true,
            ],
            [
                [
                    'locale' => 'en',
                    'alwaysAvailable' => true,
                    'viewType' => 'large',
                    'itemViewType' => 'standard',
                    'name' => 'My block',
                    'parameterValues' => [
                        'css_class' => 'class',
                    ],
                ],
                true,
            ],
            [
                [
                    'locale' => 'en',
                    'alwaysAvailable' => true,
                    'viewType' => 'large',
                    'itemViewType' => 'standard',
                    'name' => 'My block',
                    'parameterValues' => [],
                ],
                true,
            ],
            [
                [
                    'locale' => 'en',
                    'alwaysAvailable' => true,
                    'viewType' => 'large',
                    'itemViewType' => 'standard',
                    'name' => 'My block',
                    'parameterValues' => 42,
                ],
                false,
            ],
            [
                [
                    'locale' => 'en',
                    'alwaysAvailable' => true,
                    'viewType' => 'large',
                    'itemViewType' => 'standard',
                    'name' => 'My block',
                ],
                true,
            ],
        ];
    }

    /**
     * @param bool $hasRequiredParam
     *
     * @return \Netgen\BlockManager\Block\BlockDefinition
     */
    private function getBlockDefinition($hasRequiredParam = true)
    {
        $handler = $hasRequiredParam ?
            new BlockDefinitionHandlerWithRequiredParameter() :
            new BlockDefinitionHandler();

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
