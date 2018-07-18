<?php

declare(strict_types=1);

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
use Netgen\BlockManager\Core\Values\Collection\Collection;
use Netgen\BlockManager\Core\Values\Collection\Item;
use Netgen\BlockManager\Core\Values\Collection\Query;
use Netgen\BlockManager\Exception\Validation\ValidationException;
use Netgen\BlockManager\Tests\Collection\Stubs\QueryType;
use Netgen\BlockManager\Tests\TestCase\ValidatorFactory;
use Netgen\BlockManager\Utils\Hydrator;
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

    public function setUp(): void
    {
        $this->validator = Validation::createValidatorBuilder()
            ->setConstraintValidatorFactory(new ValidatorFactory($this))
            ->getValidator();

        $this->collectionValidator = new CollectionValidator();
        $this->collectionValidator->setValidator($this->validator);
    }

    /**
     * @covers \Netgen\BlockManager\Core\Service\Validator\CollectionValidator::validateCollectionCreateStruct
     * @dataProvider validateCollectionCreateStructProvider
     */
    public function testValidateCollectionCreateStruct(array $params, bool $isValid): void
    {
        if (!$isValid) {
            $this->expectException(ValidationException::class);
        }

        $struct = new CollectionCreateStruct();
        (new Hydrator())->hydrate($params, $struct);

        // Fake assertion to fix coverage on tests which do not perform assertions
        $this->assertTrue(true);

        $this->collectionValidator->validateCollectionCreateStruct($struct);
    }

    /**
     * @covers \Netgen\BlockManager\Core\Service\Validator\CollectionValidator::validateCollectionUpdateStruct
     * @dataProvider validateCollectionUpdateStructProvider
     */
    public function testValidateCollectionUpdateStruct(array $params, bool $isDynamic, bool $isValid): void
    {
        if (!$isValid) {
            $this->expectException(ValidationException::class);
        }

        $struct = new CollectionUpdateStruct();
        (new Hydrator())->hydrate($params, $struct);

        // Fake assertion to fix coverage on tests which do not perform assertions
        $this->assertTrue(true);

        $this->collectionValidator->validateCollectionUpdateStruct(
            Collection::fromArray(['query' => $isDynamic ? new Query() : null]),
            $struct
        );
    }

    /**
     * @covers \Netgen\BlockManager\Core\Service\Validator\CollectionValidator::validateItemCreateStruct
     * @dataProvider validateItemCreateStructProvider
     */
    public function testValidateItemCreateStruct(array $params, bool $isValid): void
    {
        if (!$isValid) {
            $this->expectException(ValidationException::class);
        }

        $struct = new ItemCreateStruct();
        (new Hydrator())->hydrate($params, $struct);

        // Fake assertion to fix coverage on tests which do not perform assertions
        $this->assertTrue(true);

        $this->collectionValidator->validateItemCreateStruct($struct);
    }

    /**
     * @covers \Netgen\BlockManager\Core\Service\Validator\CollectionValidator::validateItemUpdateStruct
     * @dataProvider validateItemUpdateStructDataProvider
     */
    public function testValidateItemUpdateStruct(array $params, bool $isValid): void
    {
        if (!$isValid) {
            $this->expectException(ValidationException::class);
        }

        $struct = new ItemUpdateStruct();
        (new Hydrator())->hydrate($params, $struct);

        // Fake assertion to fix coverage on tests which do not perform assertions
        $this->assertTrue(true);

        $this->collectionValidator->validateItemUpdateStruct(
            Item::fromArray(
                [
                    'definition' => ItemDefinition::fromArray(
                        [
                            'configDefinitions' => [
                                'key' => new ConfigDefinition(),
                            ],
                        ]
                    ),
                ]
            ),
            $struct
        );
    }

    public function validateItemUpdateStructDataProvider(): array
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
                        'key' => new ConfigStruct(),
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
     * @covers \Netgen\BlockManager\Core\Service\Validator\CollectionValidator::validateQueryCreateStruct
     * @dataProvider validateQueryCreateStructProvider
     */
    public function testValidateQueryCreateStruct(array $params, bool $isValid): void
    {
        if (!$isValid) {
            $this->expectException(ValidationException::class);
        }

        $queryCreateStruct = new QueryCreateStruct($params['queryType']);
        (new Hydrator())->hydrate($params, $queryCreateStruct);

        // Fake assertion to fix coverage on tests which do not perform assertions
        $this->assertTrue(true);

        $this->collectionValidator->validateQueryCreateStruct($queryCreateStruct);
    }

    /**
     * @covers \Netgen\BlockManager\Core\Service\Validator\CollectionValidator::validateQueryUpdateStruct
     * @dataProvider validateQueryUpdateStructProvider
     */
    public function testValidateQueryUpdateStruct(array $params, bool $isValid): void
    {
        if (!$isValid) {
            $this->expectException(ValidationException::class);
        }

        $queryUpdateStruct = new QueryUpdateStruct();
        (new Hydrator())->hydrate($params, $queryUpdateStruct);

        // Fake assertion to fix coverage on tests which do not perform assertions
        $this->assertTrue(true);

        $this->collectionValidator->validateQueryUpdateStruct(
            Query::fromArray(['queryType' => new QueryType('query_type')]),
            $queryUpdateStruct
        );
    }

    public function validateCollectionCreateStructProvider(): array
    {
        $queryCreateStruct = new QueryCreateStruct(new QueryType('test'));
        $queryCreateStruct->setParameterValues(['param' => 'value']);

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
                    'queryCreateStruct' => $queryCreateStruct,
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

    public function validateCollectionUpdateStructProvider(): array
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

    public function validateItemCreateStructProvider(): array
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
            [['definition' => new ItemDefinition(), 'value' => 42, 'type' => '0'], false],
        ];
    }

    public function validateQueryCreateStructProvider(): array
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

    public function validateQueryUpdateStructProvider(): array
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
