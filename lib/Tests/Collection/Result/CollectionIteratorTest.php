<?php

namespace Netgen\BlockManager\Tests\Collection\Result;

use Netgen\BlockManager\Collection\Result\CollectionIterator;
use Netgen\BlockManager\Tests\Collection\Stubs\Collection;
use PHPUnit\Framework\TestCase;

class CollectionIteratorTest extends TestCase
{
    use IteratorTestTrait;

    /**
     * @param array $collectionItems
     * @param array $values
     * @param int $totalCount
     * @param int $offset
     * @param int $limit
     *
     * @dataProvider manualCollectionProvider
     */
    public function testWithManualCollection(array $collectionItems, array $values, $totalCount, $offset = 0, $limit = null)
    {
        $collection = new Collection($collectionItems);
        $collectionIterator = new CollectionIterator($collection, $offset, $limit);

        $this->assertEquals($totalCount, $collectionIterator->count());
        $this->assertIteratorValues($values, $collectionIterator);
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
     * @dataProvider dynamicCollectionProvider
     */
    public function testWithDynamicCollection(
        array $manualItems,
        array $overrideItems,
        array $queryItems,
        $queryCount,
        array $values,
        $totalCount,
        $offset = 0,
        $limit = null
    ) {
        $collection = new Collection($manualItems, $overrideItems, array($queryItems), array($queryCount));
        $collectionIterator = new CollectionIterator($collection, $offset, $limit);

        $this->assertEquals($totalCount, $collectionIterator->count());
        $this->assertIteratorValues($values, $collectionIterator);
    }

    /**
     * Builds data providers for building result from manual collection.
     *
     * IDs are identifiers of 3rd party values (for example eZ content)
     *
     * @return array
     */
    public function manualCollectionProvider()
    {
        return array(
            array(
                array(42, 43, 44, 45, 46, 47, 48, 49, 50, 51, 52, 53, 54),
                array(42, 43, 44, 45, 46),
                13,
                0,
                5,
            ),
            array(
                array(42, 43, 44, 45, 46, 47, 48, 49, 50, 51, 52, 53, 54),
                array(42, 43, 44, 45, 46, 47, 48, 49, 50, 51, 52, 53, 54),
                13,
            ),
            array(
                array(42, 43, 44, 45, 46, 47, 48, 49, 50, 51, 52, 53, 54),
                array(48, 49, 50, 51, 52),
                13,
                6,
                5,
            ),
            array(
                array(42, 43, 44, 45, 46, 47, 48, 49, 50, 51, 52, 53, 54),
                array(48, 49, 50, 51, 52, 53, 54),
                13,
                6,
            ),
            array(
                array(),
                array(),
                0,
            ),
            array(
                array(),
                array(),
                0,
                5,
            ),
        );
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
                6, null,
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
                6, null,
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
                6, null,
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
}
