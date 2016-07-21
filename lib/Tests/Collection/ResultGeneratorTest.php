<?php

namespace Netgen\BlockManager\Tests\Collection;

use Netgen\BlockManager\Collection\ResultGenerator\QueryRunnerInterface;
use Netgen\BlockManager\Collection\ResultGeneratorInterface;
use Netgen\BlockManager\Item\Registry\ValueLoaderRegistryInterface;
use Netgen\BlockManager\Collection\Result;
use Netgen\BlockManager\Collection\ResultGenerator;
use Netgen\BlockManager\Item\ItemBuilder;
use Netgen\BlockManager\Collection\ResultGenerator\ResultItemBuilder;
use Netgen\BlockManager\Collection\ResultItem;
use Netgen\BlockManager\Core\Values\Collection\Collection;
use Netgen\BlockManager\Core\Values\Collection\Item;
use Netgen\BlockManager\Core\Values\Collection\Query;
use Netgen\BlockManager\Tests\Item\Stubs\Value;
use Netgen\BlockManager\Tests\Item\Stubs\ValueConverter;
use Netgen\BlockManager\Tests\Item\Stubs\ValueLoader;
use PHPUnit\Framework\TestCase;
use Exception;

class ResultGeneratorTest extends TestCase
{
    /**
     * @var \PHPUnit_Framework_MockObject_MockObject
     */
    protected $queryRunnerMock;

    /**
     * @var \Netgen\BlockManager\Collection\ResultGenerator\ResultItemBuilderInterface
     */
    protected $resultItemBuilder;

    /**
     * @var \Netgen\BlockManager\Item\ItemBuilderInterface
     */
    protected $itemBuilder;

    /**
     * @var \PHPUnit_Framework_MockObject_MockObject
     */
    protected $valueLoaderRegistryMock;

    /**
     * @var \Netgen\BlockManager\Collection\ResultGeneratorInterface
     */
    protected $generator;

    public function setUp()
    {
        $this->queryRunnerMock = $this->createMock(QueryRunnerInterface::class);

        $this->valueLoaderRegistryMock = $this->createMock(ValueLoaderRegistryInterface::class);

        $this->valueLoaderRegistryMock
            ->expects($this->any())
            ->method('getValueLoader')
            ->will($this->returnValue(new ValueLoader()));

        $this->itemBuilder = new ItemBuilder(
            $this->valueLoaderRegistryMock,
            array(new ValueConverter())
        );

        $this->resultItemBuilder = new ResultItemBuilder(
            $this->itemBuilder
        );

        $this->generator = new ResultGenerator(
            $this->queryRunnerMock,
            $this->resultItemBuilder
        );
    }

    /**
     * @param array $collectionItems
     * @param array $values
     * @param int $totalCount
     * @param int $offset
     * @param int $limit
     *
     * @covers \Netgen\BlockManager\Collection\ResultGenerator::__construct
     * @covers \Netgen\BlockManager\Collection\ResultGenerator::generateResult
     * @covers \Netgen\BlockManager\Collection\ResultGenerator::generateItems
     * @covers \Netgen\BlockManager\Collection\ResultGenerator::getResultCount
     * @covers \Netgen\BlockManager\Collection\ResultGenerator::filterInvisibleItems
     * @dataProvider generateResultForManualCollectionProvider
     */
    public function testGenerateResultForManualCollection(array $collectionItems, array $values, $totalCount, $offset = 0, $limit = null)
    {
        $collection = $this->generateManualCollection($collectionItems);
        $result = $this->generator->generateResult($collection, $offset, $limit);

        $this->assertInstanceOf(Result::class, $result);
        $this->assertEquals($collection, $result->getCollection());
        $this->assertEquals($totalCount, $result->getTotalCount());
        $this->assertEquals($offset, $result->getOffset());
        $this->assertEquals($limit, $result->getLimit());

        $items = array();
        foreach ($result->getResults() as $resultItem) {
            $items[] = $resultItem->getItem();
            $this->assertEquals(ResultItem::TYPE_MANUAL, $resultItem->getType());
            // Test items and positions?
        }

        $this->assertEquals($this->buildExpectedValues($values), $items);
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
     * @param int $queryOffset
     * @param int $queryLimit
     *
     * @covers \Netgen\BlockManager\Collection\ResultGenerator::generateResult
     * @covers \Netgen\BlockManager\Collection\ResultGenerator::generateItems
     * @covers \Netgen\BlockManager\Collection\ResultGenerator::getResultCount
     * @covers \Netgen\BlockManager\Collection\ResultGenerator::filterInvisibleItems
     * @covers \Netgen\BlockManager\Collection\ResultGenerator::getNumberOfItemsBeforeOffset
     * @covers \Netgen\BlockManager\Collection\ResultGenerator::getNumberOfItemsAtOffset
     * @dataProvider generateResultForDynamicCollectionProvider
     */
    public function testGenerateResultForDynamicCollection(
        array $manualItems,
        array $overrideItems,
        array $queryItems,
        $queryCount,
        array $values,
        $totalCount,
        $offset = 0,
        $limit = null,
        $queryOffset = 0,
        $queryLimit = null
    ) {
        $collection = $this->generateDynamicCollection($manualItems, $overrideItems);

        $this->queryRunnerMock
            ->expects($this->once())
            ->method('runQueries')
            ->with(
                $this->equalTo($collection->getQueries()),
                $this->equalTo($queryOffset),
                $this->equalTo($queryLimit)
            )
            ->will(
                $this->returnCallback(
                    function () use ($queryItems) {
                        return $this->buildQueryValues($queryItems);
                    }
                )
            );

        $this->queryRunnerMock
            ->expects($this->once())
            ->method('getTotalCount')
            ->with(
                $this->equalTo($collection->getQueries())
            )
            ->will($this->returnValue($queryCount));

        $result = $this->generator->generateResult($collection, $offset, $limit);

        $this->assertInstanceOf(Result::class, $result);
        $this->assertEquals($collection, $result->getCollection());
        $this->assertEquals($totalCount, $result->getTotalCount());
        $this->assertEquals($offset, $result->getOffset());
        $this->assertEquals($limit, $result->getLimit());

        $items = array();
        foreach ($result->getResults() as $resultItem) {
            $items[] = $resultItem->getItem();
            // Test type, items and positions?
        }

        $this->assertEquals($this->buildExpectedValues($values), $items);
    }

    /**
     * @covers \Netgen\BlockManager\Collection\ResultGenerator::generateResult
     * @covers \Netgen\BlockManager\Collection\ResultGenerator::generateItems
     * @expectedException \Exception
     */
    public function testGenerateResultThrowsException()
    {
        $this->queryRunnerMock
            ->expects($this->any())
            ->method('runQueries')
            ->will($this->throwException(new Exception()));

        $this->generator->generateResult(
            $this->generateDynamicCollection()
        );
    }

    /**
     * @covers \Netgen\BlockManager\Collection\ResultGenerator::generateResult
     * @covers \Netgen\BlockManager\Collection\ResultGenerator::getResultCount
     * @expectedException \Exception
     */
    public function testGenerateResultCountThrowsException()
    {
        $this->queryRunnerMock
            ->expects($this->any())
            ->method('getTotalCount')
            ->will($this->throwException(new Exception()));

        $this->generator->generateResult(
            $this->generateDynamicCollection()
        );
    }

    /**
     * @covers \Netgen\BlockManager\Collection\ResultGenerator::generateResult
     * @covers \Netgen\BlockManager\Collection\ResultGenerator::generateItems
     */
    public function testGenerateResultThrowsIgnoresException()
    {
        $this->queryRunnerMock
            ->expects($this->any())
            ->method('runQueries')
            ->will($this->throwException(new Exception()));

        $result = $this->generator->generateResult(
            $this->generateDynamicCollection(),
            0, null, ResultGeneratorInterface::IGNORE_EXCEPTIONS
        );

        $this->assertInstanceOf(Result::class, $result);
    }

    /**
     * @covers \Netgen\BlockManager\Collection\ResultGenerator::generateResult
     * @covers \Netgen\BlockManager\Collection\ResultGenerator::getResultCount
     */
    public function testGenerateResultCountIgnoresException()
    {
        $this->queryRunnerMock
            ->expects($this->any())
            ->method('getTotalCount')
            ->will($this->throwException(new Exception()));

        $result = $this->generator->generateResult(
            $this->generateDynamicCollection(),
            0, null, ResultGeneratorInterface::IGNORE_EXCEPTIONS
        );

        $this->assertInstanceOf(Result::class, $result);
    }

    /**
     * Builds data providers for generating result from manual collection.
     *
     * IDs are identifiers of 3rd party values (for example eZ content)
     * All values which have ID >= 100 are considered invisible and are filtered out on display
     *
     * @return array
     */
    public function generateResultForManualCollectionProvider()
    {
        return array(
            array(
                array(42, 143, 44, 145, 46, 47, 48, 49, 50, 151, 152, 53, 54),
                array(42, 44, 46),
                13,
                0,
                5,
            ),
            array(
                array(42, 143, 44, 145, 46, 47, 48, 49, 50, 151, 152, 53, 54),
                array(42, 44, 46, 47, 48, 49, 50, 53, 54),
                13,
            ),
            array(
                array(42, 143, 44, 145, 46, 47, 48, 49, 50, 151, 152, 53, 54),
                array(48, 49, 50),
                13,
                6,
                5,
            ),
            array(
                array(42, 143, 44, 145, 46, 47, 48, 49, 50, 151, 152, 53, 54),
                array(48, 49, 50, 53, 54),
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
     * Builds data providers for generating result from dynamic collection.
     *
     * @return array
     */
    public function generateResultForDynamicCollectionProvider()
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
                array(42, 143, 44, 145), 13,
                array(42, 10, 25), 17,
                0, 5, 0, 4,
            ),
            array(
                array(2 => 10, 7 => 14, 8 => 16, 11 => 20),
                array(3 => 25, 9 => 26),
                array(42, 143, 44, 145, 46, 47, 48, 49, 50, 151, 152, 53, 54), 13,
                array(42, 10, 25, 46, 47, 14, 16, 26, 49, 20, 50, 53, 54), 17,
            ),
            array(
                array(2 => 10, 7 => 14, 8 => 16, 11 => 20),
                array(3 => 25, 9 => 26),
                array(42, 143, 44), 13,
                array(42, 14, 16, 26, 44), 17,
                6, 5, 5, 3,
            ),
            array(
                array(2 => 10, 7 => 14, 8 => 16, 11 => 20),
                array(3 => 25, 9 => 26),
                array(42, 143, 44, 145, 46, 47, 48, 49, 50, 151, 152, 53, 54), 13,
                array(42, 14, 16, 26, 44, 20, 46, 47, 48, 49, 50, 53, 54), 17,
                6, null, 5, null,
            ),
            array(
                array(),
                array(3 => 25, 9 => 26),
                array(42, 143, 44, 145, 46, 47, 48, 49, 50, 151, 152, 53, 54), 13,
                array(42, 44, 25, 46, 47, 48, 49, 50, 26, 53, 54), 13,
            ),
            array(
                array(),
                array(3 => 25, 9 => 26),
                array(42, 143, 44, 145, 46, 47, 48, 49, 50, 151, 152, 53, 54), 13,
                array(42, 44, 26, 46, 47, 48, 49, 50, 53, 54), 13,
                6, null, 6, null,
            ),
            array(
                array(2 => 10, 7 => 14, 8 => 16, 11 => 20),
                array(),
                array(42, 143, 44, 145, 46, 47, 48, 49, 50, 151, 152, 53, 54), 13,
                array(42, 10, 44, 46, 47, 14, 16, 48, 49, 20, 50, 53, 54), 17,
            ),
            array(
                array(2 => 10, 7 => 14, 8 => 16, 11 => 20),
                array(),
                array(42, 143, 44, 145, 46, 47, 48, 49, 50, 151, 152, 53, 54), 13,
                array(42, 14, 16, 44, 20, 46, 47, 48, 49, 50, 53, 54), 17,
                6, null, 5, null,
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

    /**
     * Builds the list of value objects as returned by queries from provided IDs.
     *
     * @param array $ids
     *
     * @return \Netgen\BlockManager\Tests\Item\Stubs\Value[]
     */
    protected function buildQueryValues(array $ids = array())
    {
        return array_map(
            function ($id) {
                return new Value($id);
            },
            $ids
        );
    }

    /**
     * Builds the list of Item objects from provided IDs.
     *
     * @param array $ids
     *
     * @return \Netgen\BlockManager\Item\Item[]
     */
    protected function buildExpectedValues(array $ids = array())
    {
        return array_map(
            function ($id) {
                return $this->itemBuilder->buildFromObject(new Value($id));
            },
            $ids
        );
    }

    /**
     * Generates the manual collection for provided type and list of value IDs.
     *
     * @param array $ids
     *
     * @return \Netgen\BlockManager\Core\Values\Collection\Collection
     */
    protected function generateManualCollection(array $ids = array())
    {
        $items = array();

        foreach ($ids as $position => $id) {
            $items[] = new Item(
                array(
                    'position' => $position,
                    'type' => Item::TYPE_MANUAL,
                    'valueId' => $id,
                    'valueType' => 'value',
                )
            );
        }

        $collection = new Collection(
            array(
                'type' => Collection::TYPE_MANUAL,
                'items' => $items,
            )
        );

        return $collection;
    }

    /**
     * Generates the dynamic collection for provided type and list of value IDs.
     *
     * @param array $manualIds
     * @param array $overrideIds
     *
     * @return \Netgen\BlockManager\Core\Values\Collection\Collection
     */
    protected function generateDynamicCollection(array $manualIds = array(), array $overrideIds = array())
    {
        $items = array();

        foreach ($manualIds as $position => $id) {
            $items[] = new Item(
                array(
                    'position' => $position,
                    'type' => Item::TYPE_MANUAL,
                    'valueId' => $id,
                    'valueType' => 'value',
                )
            );
        }

        foreach ($overrideIds as $position => $id) {
            $items[] = new Item(
                array(
                    'position' => $position,
                    'type' => Item::TYPE_OVERRIDE,
                    'valueId' => $id,
                    'valueType' => 'value',
                )
            );
        }

        $queries = array(
            new Query(array('identifier' => 'first', 'type' => 'ezcontent_search')),
            new Query(array('identifier' => 'second', 'type' => 'ezcontent_search')),
            new Query(array('identifier' => 'third', 'type' => 'ezcontent_search')),
        );

        $collection = new Collection(
            array(
                'type' => Collection::TYPE_DYNAMIC,
                'items' => $items,
                'queries' => $queries,
            )
        );

        return $collection;
    }
}
