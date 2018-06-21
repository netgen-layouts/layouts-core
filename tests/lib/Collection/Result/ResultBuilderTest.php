<?php

declare(strict_types=1);

namespace Netgen\BlockManager\Tests\Collection\Result;

use Doctrine\Common\Collections\ArrayCollection;
use Netgen\BlockManager\API\Values\Collection\Collection as APICollection;
use Netgen\BlockManager\Collection\Item\ItemDefinition;
use Netgen\BlockManager\Collection\Result\CollectionRunnerFactory;
use Netgen\BlockManager\Collection\Result\ManualItem;
use Netgen\BlockManager\Collection\Result\Result;
use Netgen\BlockManager\Collection\Result\ResultBuilder;
use Netgen\BlockManager\Collection\Result\ResultBuilderInterface;
use Netgen\BlockManager\Collection\Result\ResultSet;
use Netgen\BlockManager\Core\Values\Collection\Collection;
use Netgen\BlockManager\Core\Values\Collection\Item;
use Netgen\BlockManager\Core\Values\Collection\Query;
use Netgen\BlockManager\Core\Values\Config\Config;
use Netgen\BlockManager\Item\CmsItem;
use Netgen\BlockManager\Item\CmsItemBuilder;
use Netgen\BlockManager\Parameters\Parameter;
use Netgen\BlockManager\Tests\Collection\Stubs\QueryType;
use Netgen\BlockManager\Tests\Item\Stubs\Value;
use Netgen\BlockManager\Tests\Item\Stubs\ValueConverter;
use PHPUnit\Framework\TestCase;

final class ResultBuilderTest extends TestCase
{
    /**
     * @var \Netgen\BlockManager\Item\CmsItemBuilderInterface
     */
    private $cmsItemBuilder;

    /**
     * @var \Netgen\BlockManager\Collection\Result\ResultBuilderInterface
     */
    private $resultBuilder;

    public function setUp(): void
    {
        $this->cmsItemBuilder = new CmsItemBuilder(
            [new ValueConverter()]
        );

        $this->resultBuilder = $this->buildResultBuilder(200);
    }

    /**
     * @covers \Netgen\BlockManager\Collection\Result\ResultBuilder::__construct
     * @covers \Netgen\BlockManager\Collection\Result\ResultBuilder::build
     */
    public function testBuildForManualCollection(): void
    {
        $collection = $this->buildCollection(
            [42, 43, 44, 45, 46, 47, 48, 49, 50, 51, 52, 53, 54]
        );

        $resultSet = $this->resultBuilder->build($collection, 0, 5);

        $this->assertInstanceOf(ResultSet::class, $resultSet);
        $this->assertSame($collection, $resultSet->getCollection());
        $this->assertSame(0, $resultSet->getOffset());
        $this->assertSame(5, $resultSet->getLimit());

        foreach ($resultSet as $index => $result) {
            $this->assertInstanceOf(Result::class, $result);
            $this->assertInstanceOf(ManualItem::class, $result->getItem());
            $this->assertSame($index, $result->getPosition());
        }
    }

    /**
     * @covers \Netgen\BlockManager\Collection\Result\ResultBuilder::__construct
     * @covers \Netgen\BlockManager\Collection\Result\ResultBuilder::build
     */
    public function testBuildWithLimitLargerThanMaxLimit(): void
    {
        $resultBuilder = $this->buildResultBuilder(3);

        $collection = $this->buildCollection(
            [42, 43, 44, 45, 46, 47, 48, 49, 50, 51, 52, 53, 54]
        );

        $resultSet = $resultBuilder->build($collection, 0, 5);

        $this->assertInstanceOf(ResultSet::class, $resultSet);
        $this->assertSame($collection, $resultSet->getCollection());
        $this->assertSame(0, $resultSet->getOffset());
        $this->assertSame(3, $resultSet->getLimit());

        foreach ($resultSet as $index => $result) {
            $this->assertInstanceOf(Result::class, $result);
            $this->assertInstanceOf(ManualItem::class, $result->getItem());
            $this->assertSame($index, $result->getPosition());
        }
    }

    /**
     * @covers \Netgen\BlockManager\Collection\Result\ResultBuilder::build
     */
    public function testBuildForDynamicCollection(): void
    {
        $collection = $this->buildCollection(
            [2 => 10, 7 => 14, 8 => 16, 11 => 20],
            [3 => 25, 9 => 26],
            [42, 43, 44, 45, 0, 0, 0, 0, 0, 0, 0, 0, 0],
            13
        );

        $result = $this->resultBuilder->build($collection, 0, 5);

        $this->assertInstanceOf(ResultSet::class, $result);
        $this->assertSame($collection, $result->getCollection());
        $this->assertSame(0, $result->getOffset());
        $this->assertSame(5, $result->getLimit());

        foreach ($result->getResults() as $index => $resultItem) {
            $this->assertInstanceOf(Result::class, $resultItem);
            $this->assertSame($index, $resultItem->getPosition());
            // @todo Test item types
        }
    }

    /**
     * @covers \Netgen\BlockManager\Collection\Result\ResultBuilder::build
     */
    public function testBuildForDynamicAndContextualCollection(): void
    {
        $collection = $this->buildCollection(
            [2 => 10, 7 => 14, 8 => 16, 11 => 20],
            [3 => 25, 9 => 26],
            [42, 43, 44, 45, 0, 0, 0, 0, 0, 0, 0, 0, 0],
            13,
            true
        );

        $result = $this->resultBuilder->build($collection, 0, 20, PHP_INT_MAX);

        $this->assertInstanceOf(ResultSet::class, $result);
        $this->assertSame($collection, $result->getCollection());
        $this->assertSame(0, $result->getOffset());
        $this->assertSame(12, $result->getLimit());

        foreach ($result->getResults() as $index => $resultItem) {
            $this->assertInstanceOf(Result::class, $resultItem);
            $this->assertSame($index, $resultItem->getPosition());
            // @todo Test item types
        }
    }

    /**
     * @covers \Netgen\BlockManager\Collection\Result\ResultBuilder::build
     */
    public function testBuildForDynamicAndContextualCollectionAndLimitLowerThanContextualLimit(): void
    {
        $collection = $this->buildCollection(
            [2 => 10, 7 => 14, 8 => 16, 11 => 20],
            [3 => 25, 9 => 26],
            [42, 43, 44, 45, 0, 0, 0, 0, 0, 0, 0, 0, 0],
            13,
            true
        );

        $result = $this->resultBuilder->build($collection, 0, 5, PHP_INT_MAX);

        $this->assertInstanceOf(ResultSet::class, $result);
        $this->assertSame($collection, $result->getCollection());
        $this->assertSame(0, $result->getOffset());
        $this->assertSame(5, $result->getLimit());

        foreach ($result->getResults() as $index => $resultItem) {
            $this->assertInstanceOf(Result::class, $resultItem);
            $this->assertSame($index, $resultItem->getPosition());
            // @todo Test item types
        }
    }

    private function buildResultBuilder(int $maxLimit): ResultBuilderInterface
    {
        return new ResultBuilder(
            new CollectionRunnerFactory($this->cmsItemBuilder),
            12,
            $maxLimit
        );
    }

    /**
     * Builds the dynamic collection for provided type and list of values.
     */
    private function buildCollection(
        array $manualIds = [],
        array $overrideIds = [],
        array $queryValues = [],
        int $queryCount = 0,
        bool $contextual = false
    ): APICollection {
        $items = [];

        foreach ($manualIds as $position => $id) {
            $items[] = new Item(
                [
                    'position' => $position,
                    'type' => Item::TYPE_MANUAL,
                    'value' => $id,
                    'definition' => new ItemDefinition(['valueType' => 'value']),
                    'cmsItem' => new CmsItem(['value' => $id, 'valueType' => 'value', 'isVisible' => true]),
                    'configs' => [
                        'visibility' => new Config(
                            [
                                'parameters' => [
                                    'visibility_status' => new Parameter(
                                        [
                                            'value' => Item::VISIBILITY_VISIBLE,
                                        ]
                                    ),
                                ],
                            ]
                        ),
                    ],
                ]
            );
        }

        foreach ($overrideIds as $position => $id) {
            $items[] = new Item(
                [
                    'position' => $position,
                    'type' => Item::TYPE_OVERRIDE,
                    'value' => $id,
                    'definition' => new ItemDefinition(['valueType' => 'value']),
                    'cmsItem' => new CmsItem(['value' => $id, 'valueType' => 'value', 'isVisible' => true]),
                    'configs' => [
                        'visibility' => new Config(
                            [
                                'parameters' => [
                                    'visibility_status' => new Parameter(
                                        [
                                            'value' => Item::VISIBILITY_VISIBLE,
                                        ]
                                    ),
                                ],
                            ]
                        ),
                    ],
                ]
            );
        }

        $collection = new Collection(
            [
                'items' => new ArrayCollection($items),
                'query' => new Query(
                    [
                        'queryType' => new QueryType(
                            'my_query_type',
                            $this->buildQueryValues($queryValues),
                            $queryCount,
                            $contextual
                        ),
                    ]
                ),
            ]
        );

        return $collection;
    }

    /**
     * Builds the list of values as returned by queries from provided IDs.
     *
     * @return \Netgen\BlockManager\Tests\Item\Stubs\Value[]
     */
    private function buildQueryValues(array $ids = []): array
    {
        return array_map(
            function ($id): Value {
                return new Value($id, '');
            },
            $ids
        );
    }
}
