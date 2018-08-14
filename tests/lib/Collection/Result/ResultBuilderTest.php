<?php

declare(strict_types=1);

namespace Netgen\BlockManager\Tests\Collection\Result;

use Doctrine\Common\Collections\ArrayCollection;
use Netgen\BlockManager\API\Values\Collection\Collection;
use Netgen\BlockManager\API\Values\Collection\Item;
use Netgen\BlockManager\API\Values\Collection\Query;
use Netgen\BlockManager\Collection\Item\ItemDefinition;
use Netgen\BlockManager\Collection\Item\VisibilityResolver;
use Netgen\BlockManager\Collection\Result\CollectionRunnerFactory;
use Netgen\BlockManager\Collection\Result\ManualItem;
use Netgen\BlockManager\Collection\Result\Result;
use Netgen\BlockManager\Collection\Result\ResultBuilder;
use Netgen\BlockManager\Collection\Result\ResultBuilderInterface;
use Netgen\BlockManager\Item\CmsItem;
use Netgen\BlockManager\Item\CmsItemBuilder;
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

        self::assertSame($collection, $resultSet->getCollection());
        self::assertSame(0, $resultSet->getOffset());
        self::assertSame(5, $resultSet->getLimit());
        self::assertContainsOnlyInstancesOf(Result::class, $resultSet->getResults());

        foreach ($resultSet as $index => $result) {
            self::assertInstanceOf(ManualItem::class, $result->getItem());
            self::assertSame($index, $result->getPosition());
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

        self::assertSame($collection, $resultSet->getCollection());
        self::assertSame(0, $resultSet->getOffset());
        self::assertSame(3, $resultSet->getLimit());
        self::assertContainsOnlyInstancesOf(Result::class, $resultSet->getResults());

        foreach ($resultSet as $index => $result) {
            self::assertInstanceOf(ManualItem::class, $result->getItem());
            self::assertSame($index, $result->getPosition());
        }
    }

    /**
     * @covers \Netgen\BlockManager\Collection\Result\ResultBuilder::build
     */
    public function testBuildForDynamicCollection(): void
    {
        $collection = $this->buildCollection(
            [2 => 10, 7 => 14, 8 => 16, 11 => 20],
            [42, 43, 44, 45, 0, 0, 0, 0, 0, 0, 0, 0, 0],
            13
        );

        $result = $this->resultBuilder->build($collection, 0, 5);

        self::assertSame($collection, $result->getCollection());
        self::assertSame(0, $result->getOffset());
        self::assertSame(5, $result->getLimit());
        self::assertContainsOnlyInstancesOf(Result::class, $result->getResults());

        foreach ($result->getResults() as $index => $resultItem) {
            self::assertSame($index, $resultItem->getPosition());
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
            [42, 43, 44, 45, 0, 0, 0, 0, 0, 0, 0, 0, 0],
            13,
            true
        );

        $result = $this->resultBuilder->build($collection, 0, 20, PHP_INT_MAX);

        self::assertSame($collection, $result->getCollection());
        self::assertSame(0, $result->getOffset());
        self::assertSame(12, $result->getLimit());
        self::assertContainsOnlyInstancesOf(Result::class, $result->getResults());

        foreach ($result->getResults() as $index => $resultItem) {
            self::assertSame($index, $resultItem->getPosition());
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
            [42, 43, 44, 45, 0, 0, 0, 0, 0, 0, 0, 0, 0],
            13,
            true
        );

        $result = $this->resultBuilder->build($collection, 0, 5, PHP_INT_MAX);

        self::assertSame($collection, $result->getCollection());
        self::assertSame(0, $result->getOffset());
        self::assertSame(5, $result->getLimit());
        self::assertContainsOnlyInstancesOf(Result::class, $result->getResults());

        foreach ($result->getResults() as $index => $resultItem) {
            self::assertSame($index, $resultItem->getPosition());
            // @todo Test item types
        }
    }

    private function buildResultBuilder(int $maxLimit): ResultBuilderInterface
    {
        return new ResultBuilder(
            new CollectionRunnerFactory($this->cmsItemBuilder, new VisibilityResolver()),
            12,
            $maxLimit
        );
    }

    /**
     * Builds the dynamic collection for provided type and list of values.
     */
    private function buildCollection(
        array $itemIds,
        array $queryValues = [],
        int $queryCount = 0,
        bool $contextual = false
    ): Collection {
        $items = [];

        foreach ($itemIds as $position => $id) {
            $items[] = Item::fromArray(
                [
                    'position' => $position,
                    'value' => $id,
                    'definition' => ItemDefinition::fromArray(['valueType' => 'value']),
                    'cmsItem' => CmsItem::fromArray(['value' => $id, 'valueType' => 'value', 'isVisible' => true]),
                ]
            );
        }

        $collection = Collection::fromArray(
            [
                'items' => new ArrayCollection($items),
                'query' => Query::fromArray(
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
