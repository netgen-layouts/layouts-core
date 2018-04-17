<?php

namespace Netgen\BlockManager\Tests\Core\Service\Validator;

use Netgen\BlockManager\API\Values\Collection\CollectionCreateStruct;
use Netgen\BlockManager\API\Values\Collection\CollectionUpdateStruct;
use Netgen\BlockManager\API\Values\Collection\ItemCreateStruct;
use Netgen\BlockManager\API\Values\Collection\ItemUpdateStruct;
use Netgen\BlockManager\API\Values\Collection\QueryCreateStruct;
use Netgen\BlockManager\API\Values\Collection\QueryUpdateStruct;
use Netgen\BlockManager\API\Values\Config\ConfigStruct;
use Netgen\BlockManager\Collection\Item\ItemDefinition;
use Netgen\BlockManager\Config\ConfigDefinition;
use Netgen\BlockManager\Core\Service\Validator\CollectionValidator;
use Netgen\BlockManager\Core\Service\Validator\ConfigValidator;
use Netgen\BlockManager\Core\Values\Collection\Collection;
use Netgen\BlockManager\Core\Values\Collection\Item;
use Netgen\BlockManager\Core\Values\Collection\Query;
use Netgen\BlockManager\Exception\Validation\ValidationException;
use Netgen\BlockManager\Tests\Collection\Stubs\QueryType;
use Netgen\BlockManager\Tests\TestCase\ValidatorFactory;
use PHPUnit\Framework\TestCase;
use stdClass;
use Symfony\Component\Validator\Validation;

final class CollectionValidatorTest extends TestCase
{
    /**
     * @var \Symfony\Component\Validator\Validator\ValidatorInterface
     */
    private $validator;

    /**
     * @var \Netgen\BlockManager\Core\Service\Validator\CollectionValidator
     */
    private $collectionValidator;

    public function setUp()
    {
        $this->validator = Validation::createValidatorBuilder()
            ->setConstraintValidatorFactory(new ValidatorFactory($this))
            ->getValidator();

        $configValidator = new ConfigValidator();
        $configValidator->setValidator($this->validator);

        $this->collectionValidator = new CollectionValidator($configValidator);
        $this->collectionValidator->setValidator($this->validator);
    }

    /**
     * @param array $params
     * @param array $isValid
     *
     * @covers \Netgen\BlockManager\Core\Service\Validator\CollectionValidator::__construct
     * @covers \Netgen\BlockManager\Core\Service\Validator\CollectionValidator::validateCollectionCreateStruct
     * @dataProvider validateCollectionCreateStructProvider
     */
    public function testValidateCollectionCreateStruct(array $params, $isValid)
    {
        if (!$isValid) {
            $this->expectException(ValidationException::class);
        }

        // Fake assertion to fix coverage on tests which do not perform assertions
        $this->assertTrue(true);

        $this->collectionValidator->validateCollectionCreateStruct(
            new CollectionCreateStruct($params)
        );
    }

    /**
     * @param array $params
     * @param bool $isDynamic
     * @param array $isValid
     *
     * @covers \Netgen\BlockManager\Core\Service\Validator\CollectionValidator::validateCollectionUpdateStruct
     * @dataProvider validateCollectionUpdateStructProvider
     */
    public function testValidateCollectionUpdateStruct(array $params, $isDynamic, $isValid)
    {
        if (!$isValid) {
            $this->expectException(ValidationException::class);
        }

        // Fake assertion to fix coverage on tests which do not perform assertions
        $this->assertTrue(true);

        $this->collectionValidator->validateCollectionUpdateStruct(
            new Collection(['query' => $isDynamic ? new Query() : null]),
            new CollectionUpdateStruct($params)
        );
    }

    /**
     * @param array $params
     * @param bool $isValid
     *
     * @covers \Netgen\BlockManager\Core\Service\Validator\CollectionValidator::validateItemCreateStruct
     * @dataProvider validateItemCreateStructProvider
     */
    public function testValidateItemCreateStruct(array $params, $isValid)
    {
        if (!$isValid) {
            $this->expectException(ValidationException::class);
        }

        // Fake assertion to fix coverage on tests which do not perform assertions
        $this->assertTrue(true);

        $this->collectionValidator->validateItemCreateStruct(new ItemCreateStruct($params));
    }

    /**
     * @param array $params
     * @param bool $isValid
     *
     * @covers \Netgen\BlockManager\Core\Service\Validator\CollectionValidator::validateItemUpdateStruct
     * @dataProvider validateItemUpdateStructDataProvider
     */
    public function testValidateItemUpdateStruct(array $params, $isValid)
    {
        if (!$isValid) {
            $this->expectException(ValidationException::class);
        }

        // Fake assertion to fix coverage on tests which do not perform assertions
        $this->assertTrue(true);

        $this->collectionValidator->validateItemUpdateStruct(
            new Item(
                [
                    'definition' => new ItemDefinition(
                        [
                            'configDefinitions' => [
                                'visibility' => new ConfigDefinition(),
                            ],
                        ]
                    ),
                ]
            ),
            new ItemUpdateStruct($params)
        );
    }

    public function validateItemUpdateStructDataProvider()
    {
        return [
            [
                [],
                true,
            ],
            [
                [
                    'configStructs' => [],
                ],
                true,
            ],
            [
                [
                    'configStructs' => [
                        'visibility' => new ConfigStruct(),
                    ],
                ],
                true,
            ],
            [
                [
                    'configStructs' => [
                        'unknown' => new ConfigStruct(),
                    ],
                ],
                false,
            ],
        ];
    }

    /**
     * @param array $params
     * @param array $isValid
     *
     * @covers \Netgen\BlockManager\Core\Service\Validator\CollectionValidator::validateQueryCreateStruct
     * @dataProvider validateQueryCreateStructProvider
     */
    public function testValidateQueryCreateStruct(array $params, $isValid)
    {
        if (!$isValid) {
            $this->expectException(ValidationException::class);
        }

        // Fake assertion to fix coverage on tests which do not perform assertions
        $this->assertTrue(true);

        $this->collectionValidator->validateQueryCreateStruct(new QueryCreateStruct($params));
    }

    /**
     * @param array $params
     * @param array $isValid
     *
     * @covers \Netgen\BlockManager\Core\Service\Validator\CollectionValidator::validateQueryUpdateStruct
     * @dataProvider validateQueryUpdateStructProvider
     */
    public function testValidateQueryUpdateStruct(array $params, $isValid)
    {
        if (!$isValid) {
            $this->expectException(ValidationException::class);
        }

        // Fake assertion to fix coverage on tests which do not perform assertions
        $this->assertTrue(true);

        $this->collectionValidator->validateQueryUpdateStruct(
            new Query(['queryType' => new QueryType('query_type')]),
            new QueryUpdateStruct($params)
        );
    }

    public function validateCollectionCreateStructProvider()
    {
        return [
            [
                [
                    'offset' => 0,
                    'limit' => null,
                ],
                true,
            ],
            [
                [
                    'offset' => 3,
                    'limit' => null,
                ],
                false,
            ],
            [
                [
                    'limit' => null,
                ],
                true,
            ],
            [
                [
                    'offset' => null,
                    'limit' => null,
                ],
                false,
            ],
            [
                [
                    'offset' => -3,
                    'limit' => null,
                ],
                false,
            ],
            [
                [
                    'offset' => '3',
                    'limit' => null,
                ],
                false,
            ],
            [
                [
                    'offset' => 0,
                ],
                true,
            ],
            [
                [
                    'offset' => 0,
                    'limit' => 3,
                ],
                true,
            ],
            [
                [
                    'offset' => 0,
                    'limit' => 0,
                ],
                false,
            ],
            [
                [
                    'offset' => 0,
                    'limit' => -3,
                ],
                false,
            ],
            [
                [
                    'offset' => 0,
                    'limit' => '3',
                ],
                false,
            ],
            [
                [
                    'offset' => 0,
                    'limit' => null,
                    'queryCreateStruct' => new QueryCreateStruct(
                        [
                            'queryType' => new QueryType('test'),
                            'parameterValues' => [
                                'param' => 'value',
                            ],
                        ]
                    ),
                ],
                true,
            ],
            [
                [
                    'offset' => 0,
                    'limit' => null,
                    'queryCreateStruct' => new stdClass(),
                ],
                false,
            ],
        ];
    }

    public function validateCollectionUpdateStructProvider()
    {
        return [
            [
                [
                    'offset' => 6,
                ],
                true,
                true,
            ],
            [
                [
                    'offset' => 0,
                ],
                true,
                true,
            ],
            [
                [
                    'offset' => null,
                ],
                true,
                true,
            ],
            [
                [
                    'offset' => -6,
                ],
                true,
                false,
            ],
            [
                [
                    'offset' => '6',
                ],
                true,
                false,
            ],
            [
                [
                    'limit' => 6,
                ],
                true,
                true,
            ],
            [
                [
                    'limit' => 0,
                ],
                true,
                true,
            ],
            [
                [
                    'limit' => null,
                ],
                true,
                true,
            ],
            [
                [
                    'limit' => -6,
                ],
                true,
                false,
            ],
            [
                [
                    'limit' => '6',
                ],
                true,
                false,
            ],
            [
                [
                    'offset' => 6,
                ],
                false,
                false,
            ],
            [
                [
                    'offset' => 0,
                ],
                false,
                true,
            ],
            [
                [
                    'offset' => null,
                ],
                false,
                true,
            ],
            [
                [
                    'offset' => -6,
                ],
                false,
                false,
            ],
            [
                [
                    'offset' => '6',
                ],
                false,
                false,
            ],
            [
                [
                    'limit' => 6,
                ],
                false,
                true,
            ],
            [
                [
                    'limit' => 0,
                ],
                false,
                true,
            ],
            [
                [
                    'limit' => null,
                ],
                false,
                true,
            ],
            [
                [
                    'limit' => -6,
                ],
                false,
                false,
            ],
            [
                [
                    'limit' => '6',
                ],
                false,
                false,
            ],
        ];
    }

    public function validateItemCreateStructProvider()
    {
        return [
            [
                ['definition' => new ItemDefinition(), 'value' => 42, 'type' => Item::TYPE_MANUAL],
                true,
            ],
            [
                ['definition' => new ItemDefinition(), 'value' => '42', 'type' => Item::TYPE_MANUAL],
                true,
            ],
            [
                ['definition' => new ItemDefinition(), 'value' => null, 'type' => Item::TYPE_MANUAL],
                true,
            ],
            [
                ['definition' => new ItemDefinition(), 'value' => '', 'type' => Item::TYPE_MANUAL],
                true,
            ],
            [['definition' => 42, 'value' => 42, 'type' => Item::TYPE_MANUAL], false],
            [['definition' => null, 'value' => 42, 'type' => Item::TYPE_MANUAL], false],
            [['definition' => new ItemDefinition(), 'value' => 42, 'type' => 23], false],
            [['definition' => new ItemDefinition(), 'value' => 42, 'type' => 'type'], false],
            [['definition' => new ItemDefinition(), 'value' => 42, 'type' => null], false],
        ];
    }

    public function validateQueryCreateStructProvider()
    {
        return [
            [
                [
                    'queryType' => new QueryType('test'),
                    'parameterValues' => [
                        'param' => 'value',
                    ],
                ],
                true,
            ],
            [
                [
                    'queryType' => null,
                    'parameterValues' => [
                        'param' => 'value',
                    ],
                ],
                false,
            ],
            [
                [
                    'queryType' => 42,
                    'parameterValues' => [
                        'param' => 'value',
                    ],
                ],
                false,
            ],
            [
                [
                    'queryType' => new QueryType('test'),
                    'parameterValues' => [
                        'param' => '',
                    ],
                ],
                false,
            ],
            [
                [
                    'queryType' => new QueryType('test'),
                    'parameterValues' => [
                        'param' => null,
                    ],
                ],
                false,
            ],
            [
                [
                    'queryType' => new QueryType('test'),
                    'parameterValues' => [],
                ],
                false,
            ],
        ];
    }

    public function validateQueryUpdateStructProvider()
    {
        return [
            [
                [
                    'parameterValues' => [
                        'param' => 'value',
                    ],
                ],
                false,
            ],
            [
                [
                    'locale' => null,
                    'parameterValues' => [
                        'param' => 'value',
                    ],
                ],
                false,
            ],
            [
                [
                    'locale' => '',
                    'parameterValues' => [
                        'param' => 'value',
                    ],
                ],
                false,
            ],
            [
                [
                    'locale' => 42,
                    'parameterValues' => [
                        'param' => 'value',
                    ],
                ],
                false,
            ],
            [
                [
                    'locale' => 'nonexistent',
                    'parameterValues' => [
                        'param' => 'value',
                    ],
                ],
                false,
            ],
            [
                [
                    'locale' => 'en',
                    'parameterValues' => [
                        'param' => 'value',
                    ],
                ],
                true,
            ],
            [
                [
                    'locale' => 'en',
                    'parameterValues' => [
                        'param' => '',
                    ],
                ],
                false,
            ],
            [
                [
                    'locale' => 'en',
                    'parameterValues' => [
                        'param' => null,
                    ],
                ],
                false,
            ],
            [
                [
                    'locale' => 'en',
                    'parameterValues' => [],
                ],
                true,
            ],
        ];
    }
}
