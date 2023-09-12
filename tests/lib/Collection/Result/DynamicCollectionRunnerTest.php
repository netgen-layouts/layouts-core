<?php

declare(strict_types=1);

namespace Netgen\Layouts\Tests\Collection\Result;

use Doctrine\Common\Collections\ArrayCollection;
use Netgen\Layouts\API\Values\Collection\Collection;
use Netgen\Layouts\API\Values\Collection\Item;
use Netgen\Layouts\API\Values\Collection\Query;
use Netgen\Layouts\Collection\Item\VisibilityResolver;
use Netgen\Layouts\Collection\Result\CollectionRunnerFactory;
use Netgen\Layouts\Collection\Result\Result;
use Netgen\Layouts\Item\CmsItem;
use Netgen\Layouts\Item\CmsItemBuilderInterface;
use Netgen\Layouts\Item\CmsItemInterface;
use Netgen\Layouts\Item\NullCmsItem;
use Netgen\Layouts\Tests\Collection\Result\Stubs\Value;
use Netgen\Layouts\Tests\Collection\Stubs\QueryType;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;

use function array_map;
use function count;

final class DynamicCollectionRunnerTest extends TestCase
{
    /**
     * @var \PHPUnit\Framework\MockObject\MockObject&\Netgen\Layouts\Item\CmsItemBuilderInterface
     */
    private MockObject $cmsItemBuilderMock;

    protected function setUp(): void
    {
        $this->cmsItemBuilderMock = $this->createMock(CmsItemBuilderInterface::class);

        $this->cmsItemBuilderMock
            ->method('build')
            ->willReturnCallback(
                static fn ($value): CmsItemInterface => CmsItem::fromArray(['value' => $value, 'isVisible' => true]),
            );
    }

    /**
     * @param mixed[] $itemValues
     * @param mixed[] $queryItems
     * @param mixed[] $expected
     *
     * @covers \Netgen\Layouts\Collection\Result\DynamicCollectionRunner::__construct
     * @covers \Netgen\Layouts\Collection\Result\DynamicCollectionRunner::buildManualResult
     * @covers \Netgen\Layouts\Collection\Result\DynamicCollectionRunner::count
     * @covers \Netgen\Layouts\Collection\Result\DynamicCollectionRunner::getManualItemsCount
     * @covers \Netgen\Layouts\Collection\Result\DynamicCollectionRunner::getQueryValue
     * @covers \Netgen\Layouts\Collection\Result\DynamicCollectionRunner::runCollection
     * @covers \Netgen\Layouts\Collection\Result\DynamicCollectionRunner::runQuery
     *
     * @dataProvider dynamicCollectionDataProvider
     */
    public function testCollectionResult(
        array $itemValues,
        array $queryItems,
        int $queryCount,
        array $expected,
        int $totalCount,
        int $offset = 0,
        int $limit = 200
    ): void {
        $items = [];
        foreach ($itemValues as $position => $itemValue) {
            $itemValueObject = new Value($itemValue);
            $items[$position] = Item::fromArray(
                [
                    'value' => $itemValueObject,
                    'cmsItem' => $itemValue !== null ?
                        CmsItem::fromArray(['value' => $itemValueObject, 'isVisible' => true]) :
                        new NullCmsItem('value'),
                    'position' => $position,
                ],
            );
        }

        $queryItems = array_map(static fn (?int $value): Value => new Value($value), $queryItems);
        $query = Query::fromArray(['queryType' => new QueryType('my_query_type', $queryItems, $queryCount)]);
        $collection = Collection::fromArray(['items' => new ArrayCollection($items), 'slots' => new ArrayCollection(), 'query' => $query]);

        $factory = new CollectionRunnerFactory($this->cmsItemBuilderMock, new VisibilityResolver([]));
        $collectionRunner = $factory->getCollectionRunner($collection);

        self::assertSame($totalCount, $collectionRunner->count($collection));

        $result = [...$collectionRunner->runCollection($collection, $offset, $limit)];

        $result = array_map(
            static fn (Result $resultItem) => $resultItem->getItem()->getValue(),
            $result,
        );

        self::assertCount(count($expected), $result);

        foreach ($result as $index => $resultItem) {
            self::assertInstanceOf(Value::class, $resultItem);
            self::assertSame($expected[$index], $resultItem->getValue());
        }
    }

    /**
     * Builds data providers for building result from dynamic collection.
     */
    public static function dynamicCollectionDataProvider(): iterable
    {
        return [
            [
                [11 => 10, 12 => 14, 16 => 16, 17 => 20],
                [42, 43, 44, 45, 46, 47, 48, 49, 50, 51, 52, 53, 54], 13,
                [42, 43, 44, 45, 46, 47, 48, 49, 50, 51, 52, 10, 14, 53, 54], 15,
            ],
            [
                [11 => 10, 12 => null, 16 => 16, 17 => 20],
                [42, 43, 44, 45, 46, 47, 48, 49, 50, 51, 52, 53, 54], 13,
                [42, 43, 44, 45, 46, 47, 48, 49, 50, 51, 52, 10, 53, 54], 14,
            ],
            [
                [11 => 10, 12 => 14, 16 => 16, 17 => 20],
                [42, 43, 44, 45, 46, 47, 48, 49, 50, 51, 52, 53, 54], 13,
                [42, 43, 44, 45, 46, 47, 48, 49, 50, 51, 52, 10, 14, 53, 54], 15,
            ],
            [
                [11 => 10, 12 => 14, 15 => 16, 16 => 20],
                [42, 43, 44, 45, 46, 47, 48, 49, 50, 51, 52, 53, 54], 13,
                [42, 43, 44, 45, 46, 47, 48, 49, 50, 51, 52, 10, 14, 53, 54, 16, 20], 17,
            ],
            [
                [11 => 10, 12 => 14, 14 => 16, 15 => 20],
                [42, 43, 44, 45, 46, 47, 48, 49, 50, 51, 52, 53, 54], 13,
                [42, 43, 44, 45, 46, 47, 48, 49, 50, 51, 52, 10, 14, 53, 16, 20, 54], 17,
            ],
            [
                [11 => 10, 12 => 14, 13 => 16, 14 => 20],
                [42, 43, 44, 45, 46, 47, 48, 49, 50, 51, 52, 53, 54], 13,
                [42, 43, 44, 45, 46, 47, 48, 49, 50, 51, 52, 10, 14, 16, 20, 53, 54], 17,
            ],
            [
                [2 => 10, 7 => 14, 8 => 16, 11 => 20],
                [42, 43, 44, 45, 0, 0, 0, 0, 0, 0, 0, 0, 0], 13,
                [42, 43, 10, 44, 45], 17,
                0, 5,
            ],
            [
                [2 => null, 7 => 14, 8 => 16, 11 => 20],
                [42, 43, 44, 45, 46, 0, 0, 0, 0, 0, 0, 0, 0], 13,
                [42, 43, 44, 45, 46], 16,
                0, 5,
            ],
            [
                [2 => 10, 7 => 14, 8 => 16, 11 => 20],
                [42, 43, 44, 45, 0, 0, 0, 0, 0, 0, 0, 0, 0], 13,
                [42, 43, 10, 44, 45], 17,
                0, 5,
            ],
            [
                [2 => 10, 7 => null, 8 => 16, 11 => 20],
                [42, 43, 44, 45, 0, 0, 0, 0, 0, 0, 0, 0, 0], 13,
                [42, 43, 10, 44, 45], 16,
                0, 5,
            ],
            [
                [2 => 10, 7 => 14, 8 => 16, 11 => 20],
                [42, 43, 44, 45, 0, 0, 0, 0, 0, 0, 0, 0, 0], 13,
                [42, 43, 10, 44, 45], 17,
                0, 5,
            ],
            [
                [2 => 10, 7 => 14, 8 => 16, 11 => 20],
                [42, 43, 44, 45, 46, 47, 48, 49, 50, 51, 52, 53, 54], 13,
                [42, 43, 10, 44, 45, 46, 47, 14, 16, 48, 49, 20, 50, 51, 52, 53, 54], 17,
            ],
            [
                [2 => 10, 7 => 14, 8 => 16, 11 => 20],
                [0, 0, 0, 0, 0, 42, 43, 44, 0, 0, 0, 0, 0], 13,
                [42, 14, 16, 43, 44], 17,
                6, 5,
            ],
            [
                [2 => 10, 7 => 14, 8 => null, 11 => 20],
                [0, 0, 0, 0, 0, 42, 43, 44, 45, 0, 0, 0, 0], 13,
                [42, 14, 43, 44, 45], 16,
                6, 5,
            ],
            [
                [2 => 10, 7 => 14, 8 => 16, 11 => 20],
                [0, 0, 0, 0, 0, 42, 43, 44, 0, 0, 0, 0, 0], 13,
                [42, 14, 16, 43, 44], 17,
                6, 5,
            ],
            [
                [2 => null, 7 => 14, 8 => 16, 11 => 20],
                [0, 0, 0, 0, 0, 0, 42, 43, 44, 0, 0, 0, 0], 13,
                [42, 14, 16, 43, 44], 16,
                6, 5,
            ],
            [
                [2 => 10, 7 => 14, 8 => 16, 11 => 20],
                [0, 0, 0, 0, 0, 42, 43, 44, 0, 0, 0, 0, 0], 13,
                [42, 14, 16, 43, 44], 17,
                6, 5,
            ],
            [
                [2 => 10, 7 => 14, 8 => 16, 11 => null],
                [0, 0, 0, 0, 0, 42, 43, 44, 0, 0, 0, 0, 0], 13,
                [42, 14, 16, 43, 44], 16,
                6, 5,
            ],
            [
                [2 => 10, 7 => 14, 8 => 16, 11 => 20],
                [0, 0, 0, 0, 0, 42, 43, 44, 0, 0, 0, 0, 0], 13,
                [42, 14, 16, 43, 44], 17,
                6, 5,
            ],
            [
                [2 => 10, 7 => 14, 8 => 16, 11 => 20],
                [0, 0, 0, 0, 0, 42, 43, 44, 45, 46, 47, 48, 49, 50, 51, 52, 53, 54], 18,
                [42, 14, 16, 43, 44, 20, 45, 46, 47, 48, 49, 50, 51, 52, 53, 54], 22,
                6,
            ],
            [
                [2 => 10, 7 => null, 8 => 16, 11 => 20],
                [0, 0, 0, 0, 0, 42, 43, 44, 45, 46, 47, 48, 49, 50, 51, 52, 53, 54], 18,
                [42, 43, 16, 44, 45, 20, 46, 47, 48, 49, 50, 51, 52, 53, 54], 21,
                6,
            ],
            [
                [2 => 10, 7 => 14, 8 => 16, 11 => 20],
                [0, 0, 0, 0, 0, 42, 43, 44, 45, 46, 47, 48, 49, 50, 51, 52, 53, 54], 18,
                [42, 14, 16, 43, 44, 20, 45, 46, 47, 48, 49, 50, 51, 52, 53, 54], 22,
                6,
            ],
            [
                [2 => null, 7 => 14, 8 => 16, 11 => 20],
                [0, 0, 0, 0, 0, 0, 42, 43, 44, 45, 46, 47, 48, 49, 50, 51, 52, 53], 18,
                [42, 14, 16, 43, 44, 20, 45, 46, 47, 48, 49, 50, 51, 52, 53], 21,
                6,
            ],
            [
                [2 => 10, 7 => 14, 8 => 16, 11 => 20],
                [0, 0, 0, 0, 0, 42, 43, 44, 45, 46, 47, 48, 49, 50, 51, 52, 53, 54], 18,
                [42, 14, 16, 43, 44, 20, 45, 46, 47, 48, 49, 50, 51, 52, 53, 54], 22,
                6,
            ],
            [
                [],
                [42, 43, 44, 45, 46, 47, 48, 49, 50, 51, 52, 53, 54], 13,
                [42, 43, 44, 45, 46, 47, 48, 49, 50, 51, 52, 53, 54], 13,
            ],
            [
                [],
                [42, 43, 44, 45, 46, 47, 48, 49, 50, 51, 52, 53, 54], 13,
                [42, 43, 44, 45, 46, 47, 48, 49, 50, 51, 52, 53, 54], 13,
            ],
            [
                [],
                [0, 0, 0, 0, 0, 0, 42, 43, 44, 45, 46, 47, 48, 49, 50, 51, 52, 53, 54], 19,
                [42, 43, 44, 45, 46, 47, 48, 49, 50, 51, 52, 53, 54], 19,
                6,
            ],
            [
                [],
                [0, 0, 0, 0, 0, 0, 42, 43, 44, 45, 46, 47, 48, 49, 50, 51, 52, 53, 54], 19,
                [42, 43, 44, 45, 46, 47, 48, 49, 50, 51, 52, 53, 54], 19,
                6,
            ],
            [
                [],
                [0, 0, 0, 0, 0, 0, 42, 43, 44, 45, 46, 47, 48, 49, 50, 51, 52, 53, 54], 19,
                [42, 43, 44, 45, 46, 47, 48, 49, 50, 51, 52, 53, 54], 19,
                6,
            ],
            [
                [2 => 10, 7 => 14, 8 => 16, 11 => 20],
                [42, 43, 44, 45, 46, 47, 48, 49, 50, 51, 52, 53, 54], 13,
                [42, 43, 10, 44, 45, 46, 47, 14, 16, 48, 49, 20, 50, 51, 52, 53, 54], 17,
            ],
            [
                [2 => null, 7 => 14, 8 => 16, 11 => 20],
                [42, 43, 44, 45, 46, 47, 48, 49, 50, 51, 52, 53, 54], 13,
                [42, 43, 44, 45, 46, 47, 48, 14, 16, 49, 50, 20, 51, 52, 53, 54], 16,
            ],
            [
                [2 => 10, 7 => 14, 8 => 16, 11 => 20],
                [0, 0, 0, 0, 0, 42, 43, 44, 45, 46, 47, 48, 49, 50, 51, 52, 53, 54], 18,
                [42, 14, 16, 43, 44, 20, 45, 46, 47, 48, 49, 50, 51, 52, 53, 54], 22,
                6,
            ],
            [
                [2 => 10, 7 => 14, 8 => null, 11 => 20],
                [0, 0, 0, 0, 0, 42, 43, 44, 45, 46, 47, 48, 49, 50, 51, 52, 53, 54], 18,
                [42, 14, 43, 44, 45, 20, 46, 47, 48, 49, 50, 51, 52, 53, 54], 21,
                6,
            ],
            [
                [2 => null, 7 => 14, 8 => 16, 11 => 20],
                [0, 0, 0, 0, 0, 0, 42, 43, 44, 45, 46, 47, 48, 49, 50, 51, 52, 53], 18,
                [42, 14, 16, 43, 44, 20, 45, 46, 47, 48, 49, 50, 51, 52, 53], 21,
                6,
            ],
            [
                [2 => 10, 7 => 14, 8 => 16, 11 => 20],
                [], 0,
                [], 0,
            ],
            [
                [2 => null, 7 => 14, 8 => 16, 11 => 20],
                [], 0,
                [], 0,
            ],
            [
                [2 => 10, 7 => 14, 8 => 16, 11 => 20],
                [], 0,
                [], 0,
            ],
            [
                [0 => 10, 7 => 14, 8 => 16, 11 => 20],
                [], 0,
                [10], 1,
            ],
            [
                [0 => null, 7 => 14, 8 => 16, 11 => 20],
                [], 0,
                [], 0,
            ],
            [
                [0 => 10, 7 => 14, 8 => 16, 11 => 20],
                [], 0,
                [10], 1,
            ],
            [
                [2 => 10, 7 => 14, 8 => 16, 11 => 20],
                [], 0,
                [], 0,
            ],
            [
                [2 => null, 7 => 14, 8 => 16, 11 => 20],
                [], 0,
                [], 0,
            ],
            [
                [2 => 10, 7 => 14, 8 => 16, 11 => 20],
                [], 0,
                [], 0,
            ],
            [
                [],
                [], 0,
                [], 0,
            ],
        ];
    }
}
