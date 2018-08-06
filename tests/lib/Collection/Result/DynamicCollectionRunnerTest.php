<?php

declare(strict_types=1);

namespace Netgen\BlockManager\Tests\Collection\Result;

use Netgen\BlockManager\Collection\Item\VisibilityResolver;
use Netgen\BlockManager\Collection\Result\CollectionRunnerFactory;
use Netgen\BlockManager\Collection\Result\Result;
use Netgen\BlockManager\Core\Values\Collection\Query;
use Netgen\BlockManager\Item\CmsItem;
use Netgen\BlockManager\Item\CmsItemBuilderInterface;
use Netgen\BlockManager\Item\CmsItemInterface;
use Netgen\BlockManager\Tests\Collection\Stubs\Collection;
use Netgen\BlockManager\Tests\Collection\Stubs\QueryType;
use PHPUnit\Framework\TestCase;

final class DynamicCollectionRunnerTest extends TestCase
{
    /**
     * @var \Netgen\BlockManager\Item\CmsItemBuilderInterface&\PHPUnit\Framework\MockObject\MockObject
     */
    private $cmsItemBuilderMock;

    public function setUp(): void
    {
        $this->cmsItemBuilderMock = $this->createMock(CmsItemBuilderInterface::class);

        $this->cmsItemBuilderMock
            ->expects($this->any())
            ->method('build')
            ->will(
                $this->returnCallback(
                    function ($value): CmsItemInterface {
                        return CmsItem::fromArray(['value' => $value, 'isVisible' => true]);
                    }
                )
            );
    }

    /**
     * @covers \Netgen\BlockManager\Collection\Result\DynamicCollectionRunner::__construct
     * @covers \Netgen\BlockManager\Collection\Result\DynamicCollectionRunner::buildManualResult
     * @covers \Netgen\BlockManager\Collection\Result\DynamicCollectionRunner::count
     * @covers \Netgen\BlockManager\Collection\Result\DynamicCollectionRunner::getManualItemsCount
     * @covers \Netgen\BlockManager\Collection\Result\DynamicCollectionRunner::getQueryValue
     * @covers \Netgen\BlockManager\Collection\Result\DynamicCollectionRunner::runCollection
     * @covers \Netgen\BlockManager\Collection\Result\DynamicCollectionRunner::runQuery
     *
     * @dataProvider dynamicCollectionProvider
     */
    public function testCollectionResult(
        array $items,
        array $queryItems,
        int $queryCount,
        array $expected,
        int $totalCount,
        int $offset = 0,
        int $limit = 200
    ): void {
        $query = Query::fromArray(['queryType' => new QueryType('my_query_type', $queryItems, $queryCount)]);
        $collection = new Collection($items, $query);

        $factory = new CollectionRunnerFactory($this->cmsItemBuilderMock, new VisibilityResolver());
        $collectionRunner = $factory->getCollectionRunner($collection);

        $this->assertSame($totalCount, $collectionRunner->count($collection));

        $result = array_map(
            function (Result $result) {
                return $result->getItem()->getValue();
            },
            iterator_to_array($collectionRunner->runCollection($collection, $offset, $limit))
        );

        $this->assertSame($expected, $result);
    }

    /**
     * Builds data providers for building result from dynamic collection.
     */
    public function dynamicCollectionProvider(): array
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
