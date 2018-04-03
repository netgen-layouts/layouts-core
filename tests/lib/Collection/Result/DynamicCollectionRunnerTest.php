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
     * @var \PHPUnit\Framework\MockObject\MockObject
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
                        return new Item(array('value' => $value, 'isVisible' => true));
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
     * @covers \Netgen\BlockManager\Collection\Result\DynamicCollectionRunner::count
     * @covers \Netgen\BlockManager\Collection\Result\DynamicCollectionRunner::__invoke
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
        $this->assertIteratorValues($expectedValues, $collectionRunner($collection, $offset, $limit));
    }

    /**
     * Builds data providers for building result from dynamic collection.
     *
     * @return array
     */
    public function dynamicCollectionProvider()
    {
        return array(
            array(
                array(11 => 10, 12 => 14, 16 => 16, 17 => 20),
                array(3 => 25, 9 => 26),
                array(42, 43, 44, 45, 46, 47, 48, 49, 50, 51, 52, 53, 54), 13,
                array(42, 43, 44, 25, 46, 47, 48, 49, 50, 26, 52, 10, 14, 53, 54), 15,
            ),
            array(
                array(11 => 10, 12 => 14, 15 => 16, 16 => 20),
                array(3 => 25, 9 => 26),
                array(42, 43, 44, 45, 46, 47, 48, 49, 50, 51, 52, 53, 54), 13,
                array(42, 43, 44, 25, 46, 47, 48, 49, 50, 26, 52, 10, 14, 53, 54, 16, 20), 17,
            ),
            array(
                array(11 => 10, 12 => 14, 14 => 16, 15 => 20),
                array(3 => 25, 9 => 26),
                array(42, 43, 44, 45, 46, 47, 48, 49, 50, 51, 52, 53, 54), 13,
                array(42, 43, 44, 25, 46, 47, 48, 49, 50, 26, 52, 10, 14, 53, 16, 20, 54), 17,
            ),
            array(
                array(11 => 10, 12 => 14, 13 => 16, 14 => 20),
                array(3 => 25, 9 => 26),
                array(42, 43, 44, 45, 46, 47, 48, 49, 50, 51, 52, 53, 54), 13,
                array(42, 43, 44, 25, 46, 47, 48, 49, 50, 26, 52, 10, 14, 16, 20, 53, 54), 17,
            ),
            array(
                array(2 => 10, 7 => 14, 8 => 16, 11 => 20),
                array(3 => 25, 9 => 26),
                array(42, 43, 44, 45, 0, 0, 0, 0, 0, 0, 0, 0, 0), 13,
                array(42, 43, 10, 25, 45), 17,
                0, 5,
            ),
            array(
                array(2 => 10, 7 => 14, 8 => 16, 11 => 20),
                array(3 => 25, 9 => 26),
                array(42, 43, 44, 45, 46, 47, 48, 49, 50, 51, 52, 53, 54), 13,
                array(42, 43, 10, 25, 45, 46, 47, 14, 16, 26, 49, 20, 50, 51, 52, 53, 54), 17,
            ),
            array(
                array(2 => 10, 7 => 14, 8 => 16, 11 => 20),
                array(3 => 25, 9 => 26),
                array(0, 0, 0, 0, 0, 42, 43, 44, 0, 0, 0, 0, 0), 13,
                array(42, 14, 16, 26, 44), 17,
                6, 5,
            ),
            array(
                array(2 => 10, 7 => 14, 8 => 16, 11 => 20),
                array(3 => 25, 9 => 26),
                array(0, 0, 0, 0, 0, 42, 43, 44, 45, 46, 47, 48, 49, 50, 51, 52, 53, 54), 18,
                array(42, 14, 16, 26, 44, 20, 45, 46, 47, 48, 49, 50, 51, 52, 53, 54), 22,
                6,
            ),
            array(
                array(),
                array(3 => 25, 9 => 26),
                array(42, 43, 44, 45, 46, 47, 48, 49, 50, 51, 52, 53, 54), 13,
                array(42, 43, 44, 25, 46, 47, 48, 49, 50, 26, 52, 53, 54), 13,
            ),
            array(
                array(),
                array(3 => 25, 9 => 26),
                array(0, 0, 0, 0, 0, 0, 42, 43, 44, 45, 46, 47, 48, 49, 50, 51, 52, 53, 54), 19,
                array(42, 43, 44, 26, 46, 47, 48, 49, 50, 51, 52, 53, 54), 19,
                6,
            ),
            array(
                array(2 => 10, 7 => 14, 8 => 16, 11 => 20),
                array(),
                array(42, 43, 44, 45, 46, 47, 48, 49, 50, 51, 52, 53, 54), 13,
                array(42, 43, 10, 44, 45, 46, 47, 14, 16, 48, 49, 20, 50, 51, 52, 53, 54), 17,
            ),
            array(
                array(2 => 10, 7 => 14, 8 => 16, 11 => 20),
                array(),
                array(0, 0, 0, 0, 0, 42, 43, 44, 45, 46, 47, 48, 49, 50, 51, 52, 53, 54), 18,
                array(42, 14, 16, 43, 44, 20, 45, 46, 47, 48, 49, 50, 51, 52, 53, 54), 22,
                6,
            ),
            array(
                array(2 => 10, 7 => 14, 8 => 16, 11 => 20),
                array(3 => 25, 9 => 26),
                array(), 0,
                array(), 0,
            ),
            array(
                array(0 => 10, 7 => 14, 8 => 16, 11 => 20),
                array(3 => 25, 9 => 26),
                array(), 0,
                array(10), 1,
            ),
            array(
                array(2 => 10, 7 => 14, 8 => 16, 11 => 20),
                array(0 => 25, 9 => 26),
                array(), 0,
                array(25), 1,
            ),
            array(
                array(),
                array(),
                array(), 0,
                array(), 0,
            ),
        );
    }

    private function buildExpectedValues(array $values)
    {
        $results = array();
        foreach ($values as $key => $value) {
            $results[] = new Result(
                $key,
                new Item(array('value' => $value, 'isVisible' => true))
            );
        }

        return $results;
    }
}
