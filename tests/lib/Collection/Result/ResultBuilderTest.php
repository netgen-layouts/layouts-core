<?php

declare(strict_types=1);

namespace Netgen\Layouts\Tests\Collection\Result;

use Doctrine\Common\Collections\ArrayCollection;
use Netgen\Layouts\API\Values\Collection\Collection;
use Netgen\Layouts\API\Values\Collection\Item;
use Netgen\Layouts\API\Values\Collection\Query;
use Netgen\Layouts\Collection\Item\ItemDefinition;
use Netgen\Layouts\Collection\Item\VisibilityResolver;
use Netgen\Layouts\Collection\Result\CollectionRunnerFactory;
use Netgen\Layouts\Collection\Result\ManualItem;
use Netgen\Layouts\Collection\Result\Result;
use Netgen\Layouts\Collection\Result\ResultBuilder;
use Netgen\Layouts\Collection\Result\ResultBuilderInterface;
use Netgen\Layouts\Item\CmsItem;
use Netgen\Layouts\Item\CmsItemBuilder;
use Netgen\Layouts\Tests\Collection\Stubs\QueryType;
use Netgen\Layouts\Tests\Item\Stubs\Value;
use Netgen\Layouts\Tests\Item\Stubs\ValueConverter;
use PHPUnit\Framework\TestCase;

use function array_map;

use const PHP_INT_MAX;

final class ResultBuilderTest extends TestCase
{
    private CmsItemBuilder $cmsItemBuilder;

    private ResultBuilderInterface $resultBuilder;

    protected function setUp(): void
    {
        /** @var iterable<\Netgen\Layouts\Item\ValueConverterInterface<object>> $valueConverters */
        $valueConverters = [new ValueConverter()];
        $this->cmsItemBuilder = new CmsItemBuilder($valueConverters);

        $this->resultBuilder = $this->buildResultBuilder(200);
    }

    /**
     * @covers \Netgen\Layouts\Collection\Result\ResultBuilder::__construct
     * @covers \Netgen\Layouts\Collection\Result\ResultBuilder::build
     */
    public function testBuildForManualCollection(): void
    {
        $collection = $this->buildCollection(
            [42, 43, 44, 45, 46, 47, 48, 49, 50, 51, 52, 53, 54],
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
     * @covers \Netgen\Layouts\Collection\Result\ResultBuilder::__construct
     * @covers \Netgen\Layouts\Collection\Result\ResultBuilder::build
     */
    public function testBuildWithLimitLargerThanMaxLimit(): void
    {
        $resultBuilder = $this->buildResultBuilder(3);

        $collection = $this->buildCollection(
            [42, 43, 44, 45, 46, 47, 48, 49, 50, 51, 52, 53, 54],
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
     * @covers \Netgen\Layouts\Collection\Result\ResultBuilder::build
     */
    public function testBuildForDynamicCollection(): void
    {
        $collection = $this->buildCollection(
            [2 => 10, 7 => 14, 8 => 16, 11 => 20],
            [42, 43, 44, 45, 0, 0, 0, 0, 0, 0, 0, 0, 0],
            13,
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
     * @covers \Netgen\Layouts\Collection\Result\ResultBuilder::build
     */
    public function testBuildForDynamicAndContextualCollection(): void
    {
        $collection = $this->buildCollection(
            [2 => 10, 7 => 14, 8 => 16, 11 => 20],
            [42, 43, 44, 45, 0, 0, 0, 0, 0, 0, 0, 0, 0],
            13,
            true,
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
     * @covers \Netgen\Layouts\Collection\Result\ResultBuilder::build
     */
    public function testBuildForDynamicAndContextualCollectionAndLimitLowerThanContextualLimit(): void
    {
        $collection = $this->buildCollection(
            [2 => 10, 7 => 14, 8 => 16, 11 => 20],
            [42, 43, 44, 45, 0, 0, 0, 0, 0, 0, 0, 0, 0],
            13,
            true,
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
            new CollectionRunnerFactory($this->cmsItemBuilder, new VisibilityResolver([])),
            12,
            $maxLimit,
        );
    }

    /**
     * @param mixed[] $itemIds
     * @param mixed[] $queryValues
     *
     * Builds the dynamic collection for provided type and list of values
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
                ],
            );
        }

        return Collection::fromArray(
            [
                'items' => new ArrayCollection($items),
                'slots' => new ArrayCollection(),
                'query' => Query::fromArray(
                    [
                        'queryType' => new QueryType(
                            'my_query_type',
                            $this->buildQueryValues($queryValues),
                            $queryCount,
                            $contextual,
                        ),
                    ],
                ),
            ],
        );
    }

    /**
     * Builds the list of values as returned by queries from provided IDs.
     *
     * @param int[] $ids
     *
     * @return \Netgen\Layouts\Tests\Item\Stubs\Value[]
     */
    private function buildQueryValues(array $ids = []): array
    {
        return array_map(
            static fn ($id): Value => new Value($id, ''),
            $ids,
        );
    }
}
