<?php

declare(strict_types=1);

namespace Netgen\Layouts\Tests\Core\Validator;

use Netgen\Layouts\API\Values\Collection\Collection;
use Netgen\Layouts\API\Values\Collection\CollectionCreateStruct;
use Netgen\Layouts\API\Values\Collection\CollectionUpdateStruct;
use Netgen\Layouts\API\Values\Collection\Item;
use Netgen\Layouts\API\Values\Collection\ItemCreateStruct;
use Netgen\Layouts\API\Values\Collection\ItemUpdateStruct;
use Netgen\Layouts\API\Values\Collection\Query;
use Netgen\Layouts\API\Values\Collection\QueryCreateStruct;
use Netgen\Layouts\API\Values\Collection\QueryUpdateStruct;
use Netgen\Layouts\API\Values\Config\ConfigStruct;
use Netgen\Layouts\Collection\Item\ItemDefinition;
use Netgen\Layouts\Config\ConfigDefinition;
use Netgen\Layouts\Core\Validator\CollectionValidator;
use Netgen\Layouts\Exception\Validation\ValidationException;
use Netgen\Layouts\Tests\Collection\Stubs\QueryType;
use Netgen\Layouts\Tests\TestCase\ValidatorFactory;
use Netgen\Layouts\Utils\Hydrator;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Validator\Validation;
use Symfony\Component\Validator\Validator\ValidatorInterface;

final class CollectionValidatorTest extends TestCase
{
    private ValidatorInterface $validator;

    private CollectionValidator $collectionValidator;

    protected function setUp(): void
    {
        $this->validator = Validation::createValidatorBuilder()
            ->setConstraintValidatorFactory(new ValidatorFactory($this))
            ->getValidator();

        $this->collectionValidator = new CollectionValidator();
        $this->collectionValidator->setValidator($this->validator);
    }

    /**
     * @param array<string, mixed> $params
     *
     * @covers \Netgen\Layouts\Core\Validator\CollectionValidator::validateCollectionCreateStruct
     *
     * @dataProvider validateCollectionCreateStructDataProvider
     */
    public function testValidateCollectionCreateStruct(array $params, bool $isValid): void
    {
        if (!$isValid) {
            $this->expectException(ValidationException::class);
        }

        $struct = new CollectionCreateStruct();
        (new Hydrator())->hydrate($params, $struct);

        // Tests without assertions are not covered by PHPUnit, so we fake the assertion count
        $this->addToAssertionCount(1);

        $this->collectionValidator->validateCollectionCreateStruct($struct);
    }

    /**
     * @param array<string, mixed> $params
     *
     * @covers \Netgen\Layouts\Core\Validator\CollectionValidator::validateCollectionUpdateStruct
     *
     * @dataProvider validateCollectionUpdateStructDataProvider
     */
    public function testValidateCollectionUpdateStruct(array $params, bool $isDynamic, bool $isValid): void
    {
        if (!$isValid) {
            $this->expectException(ValidationException::class);
        }

        $struct = new CollectionUpdateStruct();
        (new Hydrator())->hydrate($params, $struct);

        // Tests without assertions are not covered by PHPUnit, so we fake the assertion count
        $this->addToAssertionCount(1);

        $this->collectionValidator->validateCollectionUpdateStruct(
            Collection::fromArray(['query' => $isDynamic ? new Query() : null]),
            $struct,
        );
    }

    /**
     * @param array<string, mixed> $params
     *
     * @covers \Netgen\Layouts\Core\Validator\CollectionValidator::validateItemCreateStruct
     *
     * @dataProvider validateItemCreateStructDataProvider
     */
    public function testValidateItemCreateStruct(array $params, bool $isValid): void
    {
        if (!$isValid) {
            $this->expectException(ValidationException::class);
        }

        $struct = new ItemCreateStruct();
        (new Hydrator())->hydrate($params, $struct);

        // Tests without assertions are not covered by PHPUnit, so we fake the assertion count
        $this->addToAssertionCount(1);

        $this->collectionValidator->validateItemCreateStruct($struct);
    }

    /**
     * @param array<string, mixed> $params
     *
     * @covers \Netgen\Layouts\Core\Validator\CollectionValidator::validateItemUpdateStruct
     *
     * @dataProvider validateItemUpdateStructDataProvider
     */
    public function testValidateItemUpdateStruct(array $params, bool $isValid): void
    {
        if (!$isValid) {
            $this->expectException(ValidationException::class);
        }

        $struct = new ItemUpdateStruct();
        (new Hydrator())->hydrate($params, $struct);

        // Tests without assertions are not covered by PHPUnit, so we fake the assertion count
        $this->addToAssertionCount(1);

        $this->collectionValidator->validateItemUpdateStruct(
            Item::fromArray(
                [
                    'definition' => ItemDefinition::fromArray(
                        [
                            'configDefinitions' => [
                                'key' => new ConfigDefinition(),
                            ],
                        ],
                    ),
                ],
            ),
            $struct,
        );
    }

    /**
     * @param array<string, mixed> $params
     *
     * @covers \Netgen\Layouts\Core\Validator\CollectionValidator::validateQueryCreateStruct
     *
     * @dataProvider validateQueryCreateStructDataProvider
     */
    public function testValidateQueryCreateStruct(array $params, bool $isValid): void
    {
        if (!$isValid) {
            $this->expectException(ValidationException::class);
        }

        $queryCreateStruct = new QueryCreateStruct($params['queryType']);
        (new Hydrator())->hydrate($params, $queryCreateStruct);

        // Tests without assertions are not covered by PHPUnit, so we fake the assertion count
        $this->addToAssertionCount(1);

        $this->collectionValidator->validateQueryCreateStruct($queryCreateStruct);
    }

    /**
     * @param array<string, mixed> $params
     *
     * @covers \Netgen\Layouts\Core\Validator\CollectionValidator::validateQueryUpdateStruct
     *
     * @dataProvider validateQueryUpdateStructDataProvider
     */
    public function testValidateQueryUpdateStruct(array $params, bool $isValid): void
    {
        if (!$isValid) {
            $this->expectException(ValidationException::class);
        }

        $queryUpdateStruct = new QueryUpdateStruct();
        (new Hydrator())->hydrate($params, $queryUpdateStruct);

        // Tests without assertions are not covered by PHPUnit, so we fake the assertion count
        $this->addToAssertionCount(1);

        $this->collectionValidator->validateQueryUpdateStruct(
            Query::fromArray(['queryType' => new QueryType('query_type')]),
            $queryUpdateStruct,
        );
    }

    public static function validateCollectionCreateStructDataProvider(): iterable
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
                    'offset' => -3,
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
                    'limit' => null,
                    'queryCreateStruct' => $queryCreateStruct,
                ],
                true,
            ],
        ];
    }

    public static function validateCollectionUpdateStructDataProvider(): iterable
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
        ];
    }

    public static function validateItemCreateStructDataProvider(): iterable
    {
        return [
            [
                ['value' => 42, 'viewType' => 'overlay'],
                false,
            ],
            [
                ['definition' => new ItemDefinition(), 'value' => 42, 'viewType' => 'overlay'],
                true,
            ],
            [
                ['definition' => new ItemDefinition(), 'value' => 42, 'viewType' => ''],
                true,
            ],
            [
                ['definition' => new ItemDefinition(), 'value' => 42, 'viewType' => null],
                true,
            ],
            [
                ['definition' => new ItemDefinition(), 'value' => '42', 'viewType' => 'overlay'],
                true,
            ],
            [
                ['definition' => new ItemDefinition(), 'value' => null, 'viewType' => 'overlay'],
                true,
            ],
            [
                ['definition' => new ItemDefinition(), 'value' => '', 'viewType' => 'overlay'],
                true,
            ],
        ];
    }

    public static function validateItemUpdateStructDataProvider(): iterable
    {
        return [
            [
                [],
                true,
            ],
            [
                [
                    'viewType' => 'overlay',
                ],
                true,
            ],
            [
                [
                    'viewType' => '',
                ],
                true,
            ],
            [
                [
                    'viewType' => null,
                ],
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

    public static function validateQueryCreateStructDataProvider(): iterable
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

    public static function validateQueryUpdateStructDataProvider(): iterable
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
                    'locale' => '',
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
