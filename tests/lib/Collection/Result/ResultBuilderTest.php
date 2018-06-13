<?php

declare(strict_types=1);

namespace Netgen\BlockManager\Tests\Collection\Result;

use Doctrine\Common\Collections\ArrayCollection;
use Netgen\BlockManager\Collection\Item\ItemDefinition;
use Netgen\BlockManager\Collection\Result\CollectionRunnerFactory;
use Netgen\BlockManager\Collection\Result\ManualItem;
use Netgen\BlockManager\Collection\Result\Result;
use Netgen\BlockManager\Collection\Result\ResultBuilder;
use Netgen\BlockManager\Collection\Result\ResultSet;
use Netgen\BlockManager\Core\Values\Collection\Collection;
use Netgen\BlockManager\Core\Values\Collection\Item;
use Netgen\BlockManager\Core\Values\Collection\Query;
use Netgen\BlockManager\Core\Values\Config\Config;
use Netgen\BlockManager\Item\Item as CmsItem;
use Netgen\BlockManager\Item\ItemBuilder;
use Netgen\BlockManager\Parameters\Parameter;
use Netgen\BlockManager\Tests\Collection\Stubs\QueryType;
use Netgen\BlockManager\Tests\Item\Stubs\Value;
use Netgen\BlockManager\Tests\Item\Stubs\ValueConverter;
use PHPUnit\Framework\TestCase;

final class ResultBuilderTest extends TestCase
{
    /**
     * @var \Netgen\BlockManager\Item\ItemBuilderInterface
     */
    private $itemBuilder;

    /**
     * @var \Netgen\BlockManager\Collection\Result\ResultBuilderInterface
     */
    private $resultBuilder;

    public function setUp()
    {
        $this->itemBuilder = new ItemBuilder(
            [new ValueConverter()]
        );

        $this->resultBuilder = $this->buildResultBuilder(200);
    }

    /**
     * @covers \Netgen\BlockManager\Collection\Result\ResultBuilder::__construct
     * @covers \Netgen\BlockManager\Collection\Result\ResultBuilder::build
     */
    public function testBuildForManualCollection()
    {
        $collection = $this->buildCollection(
            [42, 43, 44, 45, 46, 47, 48, 49, 50, 51, 52, 53, 54]
        );

        $resultSet = $this->resultBuilder->build($collection, 0, 5);

        $this->assertInstanceOf(ResultSet::class, $resultSet);
        $this->assertEquals($collection, $resultSet->getCollection());
        $this->assertEquals(0, $resultSet->getOffset());
        $this->assertEquals(5, $resultSet->getLimit());

        foreach ($resultSet as $index => $result) {
            $this->assertInstanceOf(Result::class, $result);
            $this->assertInstanceOf(ManualItem::class, $result->getItem());
            $this->assertEquals($index, $result->getPosition());
        }
    }

    /**
     * @covers \Netgen\BlockManager\Collection\Result\ResultBuilder::__construct
     * @covers \Netgen\BlockManager\Collection\Result\ResultBuilder::build
     */
    public function testBuildWithLimitLargerThanMaxLimit()
    {
        $resultBuilder = $this->buildResultBuilder(3);

        $collection = $this->buildCollection(
            [42, 43, 44, 45, 46, 47, 48, 49, 50, 51, 52, 53, 54]
        );

        $resultSet = $resultBuilder->build($collection, 0, 5);

        $this->assertInstanceOf(ResultSet::class, $resultSet);
        $this->assertEquals($collection, $resultSet->getCollection());
        $this->assertEquals(0, $resultSet->getOffset());
        $this->assertEquals(3, $resultSet->getLimit());

        foreach ($resultSet as $index => $result) {
            $this->assertInstanceOf(Result::class, $result);
            $this->assertInstanceOf(ManualItem::class, $result->getItem());
            $this->assertEquals($index, $result->getPosition());
        }
    }

    /**
     * @covers \Netgen\BlockManager\Collection\Result\ResultBuilder::build
     */
    public function testBuildForDynamicCollection()
    {
        $collection = $this->buildCollection(
            [2 => 10, 7 => 14, 8 => 16, 11 => 20],
            [3 => 25, 9 => 26],
            [42, 43, 44, 45, 0, 0, 0, 0, 0, 0, 0, 0, 0],
            13
        );

        $result = $this->resultBuilder->build($collection, 0, 5);

        $this->assertInstanceOf(ResultSet::class, $result);
        $this->assertEquals($collection, $result->getCollection());
        $this->assertEquals(0, $result->getOffset());
        $this->assertEquals(5, $result->getLimit());

        foreach ($result->getResults() as $index => $resultItem) {
            $this->assertInstanceOf(Result::class, $resultItem);
            $this->assertEquals($index, $resultItem->getPosition());
            // @todo Test item types
        }
    }

    /**
     * @covers \Netgen\BlockManager\Collection\Result\ResultBuilder::build
     */
    public function testBuildForDynamicAndContextualCollection()
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
        $this->assertEquals($collection, $result->getCollection());
        $this->assertEquals(0, $result->getOffset());
        $this->assertEquals(12, $result->getLimit());

        foreach ($result->getResults() as $index => $resultItem) {
            $this->assertInstanceOf(Result::class, $resultItem);
            $this->assertEquals($index, $resultItem->getPosition());
            // @todo Test item types
        }
    }

    /**
     * @covers \Netgen\BlockManager\Collection\Result\ResultBuilder::build
     */
    public function testBuildForDynamicAndContextualCollectionAndLimitLowerThanContextualLimit()
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
        $this->assertEquals($collection, $result->getCollection());
        $this->assertEquals(0, $result->getOffset());
        $this->assertEquals(5, $result->getLimit());

        foreach ($result->getResults() as $index => $resultItem) {
            $this->assertInstanceOf(Result::class, $resultItem);
            $this->assertEquals($index, $resultItem->getPosition());
            // @todo Test item types
        }
    }

    private function buildResultBuilder($maxLimit)
    {
        return new ResultBuilder(
            new CollectionRunnerFactory($this->itemBuilder),
            12,
            $maxLimit
        );
    }

    /**
     * Builds the dynamic collection for provided type and list of values.
     *
     * @param array $manualIds
     * @param array $overrideIds
     * @param array $queryValues
     * @param int $queryCount
     * @param bool $contextual
     *
     * @return \Netgen\BlockManager\Core\Values\Collection\Collection
     */
    private function buildCollection(
        array $manualIds = [],
        array $overrideIds = [],
        array $queryValues = [],
        $queryCount = 0,
        $contextual = false
    ) {
        $items = [];

        foreach ($manualIds as $position => $id) {
            $items[] = new Item(
                [
                    'position' => $position,
                    'type' => Item::TYPE_MANUAL,
                    'value' => $id,
                    'definition' => new ItemDefinition(['valueType' => 'value']),
                    'cmsItem' => new CmsItem(['value' => $id, 'valueType' => 'value']),
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
                    'cmsItem' => new CmsItem(['value' => $id, 'valueType' => 'value']),
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
     * @param array $ids
     *
     * @return \Netgen\BlockManager\Tests\Item\Stubs\Value[]
     */
    private function buildQueryValues(array $ids = [])
    {
        return array_map(
            function ($id) {
                return new Value($id, '');
            },
            $ids
        );
    }
}
