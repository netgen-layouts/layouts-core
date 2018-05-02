<?php

namespace Netgen\BlockManager\Tests\Collection\Result;

use Netgen\BlockManager\Collection\Result\CollectionRunnerFactory;
use Netgen\BlockManager\Collection\Result\Result;
use Netgen\BlockManager\Item\Item;
use Netgen\BlockManager\Item\ItemBuilderInterface;
use Netgen\BlockManager\Tests\Collection\Stubs\Collection;
use PHPUnit\Framework\TestCase;

final class DynamicCollectionRunnerTest extends TestCase
{
    use IteratorTestTrait;

    /**
     * @var \Netgen\BlockManager\Item\ItemBuilderInterface&\PHPUnit\Framework\MockObject\MockObject
     */
    private $itemBuilderMock;

    public function setUp()
    {
        $this->itemBuilderMock = $this->createMock(ItemBuilderInterface::class);

        $this->itemBuilderMock
            ->expects($this->any())
            ->method('build')
            ->will(
                $this->returnCallback(
                    function ($value) {
                        return new Item(['value' => $value, 'isVisible' => true]);
                    }
                )
            );
    }

    /**
     * @param array $manualItems
     * @param array $overrideItems
     * @param array $queryItems
     * @param int $queryCount
     * @param array $values
     * @param int $totalCount
     * @param int $offset
     * @param int $limit
     *
     * @covers \Netgen\BlockManager\Collection\Result\DynamicCollectionRunner::__construct
     * @covers \Netgen\BlockManager\Collection\Result\DynamicCollectionRunner::buildManualResult
     * @covers \Netgen\BlockManager\Collection\Result\DynamicCollectionRunner::buildOverrideResult
     * @covers \Netgen\BlockManager\Collection\Result\DynamicCollectionRunner::count
     * @covers \Netgen\BlockManager\Collection\Result\DynamicCollectionRunner::getManualItemsCount
     * @covers \Netgen\BlockManager\Collection\Result\DynamicCollectionRunner::getQueryValue
     * @covers \Netgen\BlockManager\Collection\Result\DynamicCollectionRunner::runCollection
     * @covers \Netgen\BlockManager\Collection\Result\DynamicCollectionRunner::runQuery
     *
     * @dataProvider dynamicCollectionProvider
     */
    public function testCollectionResult(
        array $manualItems,
        array $overrideItems,
        array $queryItems,
        $queryCount,
        array $values,
        $totalCount,
        $offset = 0,
        $limit = 200
    ) {
        $collection = new Collection($manualItems, $overrideItems, $queryItems, $queryCount);
        $factory = new CollectionRunnerFactory($this->itemBuilderMock);
        $collectionRunner = $factory->getCollectionRunner($collection);
        $expectedValues = $this->buildExpectedValues($values);

        $this->assertEquals($totalCount, $collectionRunner->count($collection));

        $this->assertIteratorValues(
            $expectedValues,
            $collectionRunner->runCollection($collection, $offset, $limit)
        );
    }

    /**
     * Builds data providers for building result from dynamic collection.
     *
     * @return array
     */
    public function dynamicCollectionProvider()
    {
        return [
            [
                [11 => 10, 12 => 14, 16 => 16, 17 => 20],
                [3 => 25, 9 => 26],
                [42, 43, 44, 45, 46, 47, 48, 49, 50, 51, 52, 53, 54], 13,
                [42, 43, 44, 25, 46, 47, 48, 49, 50, 26, 52, 10, 14, 53, 54], 15,
            ],
            [
                [11 => 10, 12 => null, 16 => 16, 17 => 20],
                [3 => 25, 9 => 26],
                [42, 43, 44, 45, 46, 47, 48, 49, 50, 51, 52, 53, 54], 13,
                [42, 43, 44, 25, 46, 47, 48, 49, 50, 26, 52, 10, 53, 54], 14,
            ],
            [
                [11 => 10, 12 => 14, 16 => 16, 17 => 20],
                [3 => 25, 9 => null],
                [42, 43, 44, 45, 46, 47, 48, 49, 50, 51, 52, 53, 54], 13,
                [42, 43, 44, 25, 46, 47, 48, 49, 50, 51, 52, 10, 14, 53, 54], 15,
            ],
            [
                [11 => 10, 12 => 14, 15 => 16, 16 => 20],
                [3 => 25, 9 => 26],
                [42, 43, 44, 45, 46, 47, 48, 49, 50, 51, 52, 53, 54], 13,
                [42, 43, 44, 25, 46, 47, 48, 49, 50, 26, 52, 10, 14, 53, 54, 16, 20], 17,
            ],
            [
                [11 => 10, 12 => 14, 14 => 16, 15 => 20],
                [3 => 25, 9 => 26],
                [42, 43, 44, 45, 46, 47, 48, 49, 50, 51, 52, 53, 54], 13,
                [42, 43, 44, 25, 46, 47, 48, 49, 50, 26, 52, 10, 14, 53, 16, 20, 54], 17,
            ],
            [
                [11 => 10, 12 => 14, 13 => 16, 14 => 20],
                [3 => 25, 9 => 26],
                [42, 43, 44, 45, 46, 47, 48, 49, 50, 51, 52, 53, 54], 13,
                [42, 43, 44, 25, 46, 47, 48, 49, 50, 26, 52, 10, 14, 16, 20, 53, 54], 17,
            ],
            [
                [2 => 10, 7 => 14, 8 => 16, 11 => 20],
                [3 => 25, 9 => 26],
                [42, 43, 44, 45, 0, 0, 0, 0, 0, 0, 0, 0, 0], 13,
                [42, 43, 10, 25, 45], 17,
                0, 5,
            ],
            [
                [2 => null, 7 => 14, 8 => 16, 11 => 20],
                [3 => 25, 9 => 26],
                [42, 43, 44, 45, 46, 0, 0, 0, 0, 0, 0, 0, 0], 13,
                [42, 43, 44, 25, 46], 16,
                0, 5,
            ],
            [
                [2 => 10, 7 => 14, 8 => 16, 11 => 20],
                [3 => null, 9 => 26],
                [42, 43, 44, 45, 0, 0, 0, 0, 0, 0, 0, 0, 0], 13,
                [42, 43, 10, 44, 45], 17,
                0, 5,
            ],
            [
                [2 => 10, 7 => null, 8 => 16, 11 => 20],
                [3 => 25, 9 => 26],
                [42, 43, 44, 45, 0, 0, 0, 0, 0, 0, 0, 0, 0], 13,
                [42, 43, 10, 25, 45], 16,
                0, 5,
            ],
            [
                [2 => 10, 7 => 14, 8 => 16, 11 => 20],
                [3 => 25, 9 => null],
                [42, 43, 44, 45, 0, 0, 0, 0, 0, 0, 0, 0, 0], 13,
                [42, 43, 10, 25, 45], 17,
                0, 5,
            ],
            [
                [2 => 10, 7 => 14, 8 => 16, 11 => 20],
                [3 => 25, 9 => 26],
                [42, 43, 44, 45, 46, 47, 48, 49, 50, 51, 52, 53, 54], 13,
                [42, 43, 10, 25, 45, 46, 47, 14, 16, 26, 49, 20, 50, 51, 52, 53, 54], 17,
            ],
            [
                [2 => 10, 7 => 14, 8 => 16, 11 => 20],
                [3 => 25, 9 => 26],
                [0, 0, 0, 0, 0, 42, 43, 44, 0, 0, 0, 0, 0], 13,
                [42, 14, 16, 26, 44], 17,
                6, 5,
            ],
            [
                [2 => 10, 7 => 14, 8 => null, 11 => 20],
                [3 => 25, 9 => 26],
                [0, 0, 0, 0, 0, 42, 43, 44, 45, 0, 0, 0, 0], 13,
                [42, 14, 43, 26, 45], 16,
                6, 5,
            ],
            [
                [2 => 10, 7 => 14, 8 => 16, 11 => 20],
                [3 => 25, 9 => null],
                [0, 0, 0, 0, 0, 42, 43, 44, 0, 0, 0, 0, 0], 13,
                [42, 14, 16, 43, 44], 17,
                6, 5,
            ],
            [
                [2 => null, 7 => 14, 8 => 16, 11 => 20],
                [3 => 25, 9 => 26],
                [0, 0, 0, 0, 0, 0, 42, 43, 44, 0, 0, 0, 0], 13,
                [42, 14, 16, 26, 44], 16,
                6, 5,
            ],
            [
                [2 => 10, 7 => 14, 8 => 16, 11 => 20],
                [3 => null, 9 => 26],
                [0, 0, 0, 0, 0, 42, 43, 44, 0, 0, 0, 0, 0], 13,
                [42, 14, 16, 26, 44], 17,
                6, 5,
            ],
            [
                [2 => 10, 7 => 14, 8 => 16, 11 => null],
                [3 => 25, 9 => 26],
                [0, 0, 0, 0, 0, 42, 43, 44, 0, 0, 0, 0, 0], 13,
                [42, 14, 16, 26, 44], 16,
                6, 5,
            ],
            [
                [2 => 10, 7 => 14, 8 => 16, 11 => 20],
                [3 => 25, 9 => 26, 12 => null],
                [0, 0, 0, 0, 0, 42, 43, 44, 0, 0, 0, 0, 0], 13,
                [42, 14, 16, 26, 44], 17,
                6, 5,
            ],
            [
                [2 => 10, 7 => 14, 8 => 16, 11 => 20],
                [3 => 25, 9 => 26],
                [0, 0, 0, 0, 0, 42, 43, 44, 45, 46, 47, 48, 49, 50, 51, 52, 53, 54], 18,
                [42, 14, 16, 26, 44, 20, 45, 46, 47, 48, 49, 50, 51, 52, 53, 54], 22,
                6,
            ],
            [
                [2 => 10, 7 => null, 8 => 16, 11 => 20],
                [3 => 25, 9 => 26],
                [0, 0, 0, 0, 0, 42, 43, 44, 45, 46, 47, 48, 49, 50, 51, 52, 53, 54], 18,
                [42, 43, 16, 26, 45, 20, 46, 47, 48, 49, 50, 51, 52, 53, 54], 21,
                6,
            ],
            [
                [2 => 10, 7 => 14, 8 => 16, 11 => 20],
                [3 => 25, 9 => null],
                [0, 0, 0, 0, 0, 42, 43, 44, 45, 46, 47, 48, 49, 50, 51, 52, 53, 54], 18,
                [42, 14, 16, 43, 44, 20, 45, 46, 47, 48, 49, 50, 51, 52, 53, 54], 22,
                6,
            ],
            [
                [2 => null, 7 => 14, 8 => 16, 11 => 20],
                [3 => 25, 9 => 26],
                [0, 0, 0, 0, 0, 0, 42, 43, 44, 45, 46, 47, 48, 49, 50, 51, 52, 53], 18,
                [42, 14, 16, 26, 44, 20, 45, 46, 47, 48, 49, 50, 51, 52, 53], 21,
                6,
            ],
            [
                [2 => 10, 7 => 14, 8 => 16, 11 => 20],
                [3 => null, 9 => 26],
                [0, 0, 0, 0, 0, 42, 43, 44, 45, 46, 47, 48, 49, 50, 51, 52, 53, 54], 18,
                [42, 14, 16, 26, 44, 20, 45, 46, 47, 48, 49, 50, 51, 52, 53, 54], 22,
                6,
            ],
            [
                [],
                [3 => 25, 9 => 26],
                [42, 43, 44, 45, 46, 47, 48, 49, 50, 51, 52, 53, 54], 13,
                [42, 43, 44, 25, 46, 47, 48, 49, 50, 26, 52, 53, 54], 13,
            ],
            [
                [],
                [3 => null, 9 => 26],
                [42, 43, 44, 45, 46, 47, 48, 49, 50, 51, 52, 53, 54], 13,
                [42, 43, 44, 45, 46, 47, 48, 49, 50, 26, 52, 53, 54], 13,
            ],
            [
                [],
                [3 => 25, 9 => 26],
                [0, 0, 0, 0, 0, 0, 42, 43, 44, 45, 46, 47, 48, 49, 50, 51, 52, 53, 54], 19,
                [42, 43, 44, 26, 46, 47, 48, 49, 50, 51, 52, 53, 54], 19,
                6,
            ],
            [
                [],
                [3 => 25, 9 => null],
                [0, 0, 0, 0, 0, 0, 42, 43, 44, 45, 46, 47, 48, 49, 50, 51, 52, 53, 54], 19,
                [42, 43, 44, 45, 46, 47, 48, 49, 50, 51, 52, 53, 54], 19,
                6,
            ],
            [
                [],
                [3 => null, 9 => 26],
                [0, 0, 0, 0, 0, 0, 42, 43, 44, 45, 46, 47, 48, 49, 50, 51, 52, 53, 54], 19,
                [42, 43, 44, 26, 46, 47, 48, 49, 50, 51, 52, 53, 54], 19,
                6,
            ],
            [
                [2 => 10, 7 => 14, 8 => 16, 11 => 20],
                [],
                [42, 43, 44, 45, 46, 47, 48, 49, 50, 51, 52, 53, 54], 13,
                [42, 43, 10, 44, 45, 46, 47, 14, 16, 48, 49, 20, 50, 51, 52, 53, 54], 17,
            ],
            [
                [2 => null, 7 => 14, 8 => 16, 11 => 20],
                [],
                [42, 43, 44, 45, 46, 47, 48, 49, 50, 51, 52, 53, 54], 13,
                [42, 43, 44, 45, 46, 47, 48, 14, 16, 49, 50, 20, 51, 52, 53, 54], 16,
            ],
            [
                [2 => 10, 7 => 14, 8 => 16, 11 => 20],
                [],
                [0, 0, 0, 0, 0, 42, 43, 44, 45, 46, 47, 48, 49, 50, 51, 52, 53, 54], 18,
                [42, 14, 16, 43, 44, 20, 45, 46, 47, 48, 49, 50, 51, 52, 53, 54], 22,
                6,
            ],
            [
                [2 => 10, 7 => 14, 8 => null, 11 => 20],
                [],
                [0, 0, 0, 0, 0, 42, 43, 44, 45, 46, 47, 48, 49, 50, 51, 52, 53, 54], 18,
                [42, 14, 43, 44, 45, 20, 46, 47, 48, 49, 50, 51, 52, 53, 54], 21,
                6,
            ],
            [
                [2 => null, 7 => 14, 8 => 16, 11 => 20],
                [],
                [0, 0, 0, 0, 0, 0, 42, 43, 44, 45, 46, 47, 48, 49, 50, 51, 52, 53], 18,
                [42, 14, 16, 43, 44, 20, 45, 46, 47, 48, 49, 50, 51, 52, 53], 21,
                6,
            ],
            [
                [2 => 10, 7 => 14, 8 => 16, 11 => 20],
                [3 => 25, 9 => 26],
                [], 0,
                [], 0,
            ],
            [
                [2 => null, 7 => 14, 8 => 16, 11 => 20],
                [3 => 25, 9 => 26],
                [], 0,
                [], 0,
            ],
            [
                [2 => 10, 7 => 14, 8 => 16, 11 => 20],
                [3 => null, 9 => 26],
                [], 0,
                [], 0,
            ],
            [
                [0 => 10, 7 => 14, 8 => 16, 11 => 20],
                [3 => 25, 9 => 26],
                [], 0,
                [10], 1,
            ],
            [
                [0 => null, 7 => 14, 8 => 16, 11 => 20],
                [3 => 25, 9 => 26],
                [], 0,
                [], 0,
            ],
            [
                [0 => 10, 7 => 14, 8 => 16, 11 => 20],
                [3 => null, 9 => 26],
                [], 0,
                [10], 1,
            ],
            [
                [2 => 10, 7 => 14, 8 => 16, 11 => 20],
                [0 => 25, 9 => 26],
                [], 0,
                [25], 1,
            ],
            [
                [2 => null, 7 => 14, 8 => 16, 11 => 20],
                [0 => 25, 9 => 26],
                [], 0,
                [25], 1,
            ],
            [
                [2 => 10, 7 => 14, 8 => 16, 11 => 20],
                [0 => null, 9 => 26],
                [], 0,
                [], 0,
            ],
            [
                [],
                [],
                [], 0,
                [], 0,
            ],
        ];
    }

    private function buildExpectedValues(array $values)
    {
        $results = [];
        foreach ($values as $key => $value) {
            $results[] = new Result($key, new Item(['value' => $value]));
        }

        return $results;
    }
}
