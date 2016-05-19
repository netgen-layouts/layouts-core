<?php

namespace Netgen\BlockManager\Tests\Collection;

use Netgen\BlockManager\Collection\ResultGenerator\QueryRunnerInterface;
use Netgen\BlockManager\Collection\Registry\ValueLoaderRegistryInterface;
use Netgen\BlockManager\Collection\Result;
use Netgen\BlockManager\Collection\ResultGenerator;
use Netgen\BlockManager\Collection\ResultGenerator\ResultValueBuilder;
use Netgen\BlockManager\Collection\ResultGenerator\ResultItemBuilder;
use Netgen\BlockManager\Collection\ResultItem;
use Netgen\BlockManager\Core\Values\Collection\Collection;
use Netgen\BlockManager\Core\Values\Collection\Item;
use Netgen\BlockManager\Core\Values\Collection\Query;
use Netgen\BlockManager\Tests\Collection\Stubs\Value;
use Netgen\BlockManager\Tests\Collection\Stubs\ValueConverter;
use Netgen\BlockManager\Tests\Collection\Stubs\ValueLoader;

class ResultGeneratorTest extends \PHPUnit_Framework_TestCase
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
     * @var \Netgen\BlockManager\Collection\ResultGenerator\ResultValueBuilderInterface
     */
    protected $resultValueBuilder;

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
        $this->queryRunnerMock = $this->getMock(QueryRunnerInterface::class);

        $this->valueLoaderRegistryMock = $this->getMock(ValueLoaderRegistryInterface::class);

        $this->valueLoaderRegistryMock
            ->expects($this->any())
            ->method('getValueLoader')
            ->will($this->returnValue(new ValueLoader()));

        $this->resultValueBuilder = new ResultValueBuilder(
            $this->valueLoaderRegistryMock,
            array(new ValueConverter())
        );

        $this->resultItemBuilder = new ResultItemBuilder(
            $this->resultValueBuilder
        );

        $this->generator = new ResultGenerator(
            $this->queryRunnerMock,
            $this->resultItemBuilder
        );
    }

    /**
     * @param array $items
     * @param array $values
     * @param int $offset
     * @param int $limit
     *
     * @covers \Netgen\BlockManager\Collection\ResultGenerator::__construct
     * @covers \Netgen\BlockManager\Collection\ResultGenerator::generateResult
     * @covers \Netgen\BlockManager\Collection\ResultGenerator::generateFromManualCollection
     * @covers \Netgen\BlockManager\Collection\ResultGenerator::filterInvisibleItems
     * @dataProvider generateResultForManualCollectionProvider
     */
    public function testGenerateResultForManualCollection(array $items, array $values, $offset = 0, $limit = null)
    {
        $collection = $this->generateManualCollection($items);
        $result = $this->generator->generateResult($collection, $offset, $limit);

        self::assertInstanceOf(Result::class, $result);
        self::assertEquals($collection, $result->getCollection());
        self::assertEquals($offset, $result->getOffset());
        self::assertEquals($limit, $result->getLimit());

        $resultValues = array();
        foreach ($result->getItems() as $resultItem) {
            $resultValues[] = $resultItem->getValue();
            self::assertEquals(ResultItem::TYPE_MANUAL, $resultItem->getType());
            // Test items and positions?
        }

        self::assertEquals($this->buildExpectedResultValues($values), $resultValues);
    }

    /**
     * @param array $manualItems
     * @param array $overrideItems
     * @param array $queryItems
     * @param array $values
     * @param int $offset
     * @param int $limit
     * @param int $queryOffset
     * @param int $queryLimit
     *
     * @covers \Netgen\BlockManager\Collection\ResultGenerator::generateResult
     * @covers \Netgen\BlockManager\Collection\ResultGenerator::generateFromDynamicCollection
     * @covers \Netgen\BlockManager\Collection\ResultGenerator::filterInvisibleItems
     * @covers \Netgen\BlockManager\Collection\ResultGenerator::getNumberOfItemsBeforeOffset
     * @covers \Netgen\BlockManager\Collection\ResultGenerator::getNumberOfItemsAtOffset
     * @dataProvider generateResultForDynamicCollectionProvider
     */
    public function testGenerateResultForDynamicCollection(
        array $manualItems,
        array $overrideItems,
        array $queryItems,
        array $values,
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

        $result = $this->generator->generateResult($collection, $offset, $limit);

        self::assertInstanceOf(Result::class, $result);
        self::assertEquals($collection, $result->getCollection());
        self::assertEquals($offset, $result->getOffset());
        self::assertEquals($limit, $result->getLimit());

        $resultValues = array();
        foreach ($result->getItems() as $resultItem) {
            $resultValues[] = $resultItem->getValue();
            // Test type, items and positions?
        }

        self::assertEquals($this->buildExpectedResultValues($values), $resultValues);
    }

    /**
     * @covers \Netgen\BlockManager\Collection\ResultGenerator::generateResult
     * @covers \Netgen\BlockManager\Collection\ResultGenerator::generateFromDynamicCollection
     * @expectedException \RuntimeException
     */
    public function testGenerateResultForDynamicCollectionThrowsRuntimeException()
    {
        $collection = new Collection(array('type' => Collection::TYPE_DYNAMIC));
        $this->generator->generateResult($collection);
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
                0,
                5,
            ),
            array(
                array(42, 143, 44, 145, 46, 47, 48, 49, 50, 151, 152, 53, 54),
                array(42, 44, 46, 47, 48, 49, 50, 53, 54),
            ),
            array(
                array(42, 143, 44, 145, 46, 47, 48, 49, 50, 151, 152, 53, 54),
                array(48, 49, 50),
                6,
                5,
            ),
            array(
                array(42, 143, 44, 145, 46, 47, 48, 49, 50, 151, 152, 53, 54),
                array(48, 49, 50, 53, 54),
                6,
            ),
            array(
                array(),
                array(),
            ),
            array(
                array(),
                array(),
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
                array(2 => 10, 7 => 14, 8 => 16, 11 => 20),
                array(3 => 25, 9 => 26),
                array(42, 143, 44, 145),
                array(42, 10, 25),
                0, 5, 0, 4,
            ),
            array(
                array(2 => 10, 7 => 14, 8 => 16, 11 => 20),
                array(3 => 25, 9 => 26),
                array(42, 143, 44, 145, 46, 47, 48, 49, 50, 151, 152, 53, 54),
                array(42, 10, 25, 46, 47, 14, 16, 26, 49, 20, 50, 53, 54),
            ),
            array(
                array(2 => 10, 7 => 14, 8 => 16, 11 => 20),
                array(3 => 25, 9 => 26),
                array(42, 143, 44),
                array(42, 14, 16, 26, 44),
                6, 5, 5, 3,
            ),
            array(
                array(2 => 10, 7 => 14, 8 => 16, 11 => 20),
                array(3 => 25, 9 => 26),
                array(42, 143, 44, 145, 46, 47, 48, 49, 50, 151, 152, 53, 54),
                array(42, 14, 16, 26, 44, 20, 46, 47, 48, 49, 50, 53, 54),
                6, null, 5, null,
            ),
            array(
                array(),
                array(3 => 25, 9 => 26),
                array(42, 143, 44, 145, 46, 47, 48, 49, 50, 151, 152, 53, 54),
                array(42, 44, 25, 46, 47, 48, 49, 50, 26, 53, 54),
            ),
            array(
                array(),
                array(3 => 25, 9 => 26),
                array(42, 143, 44, 145, 46, 47, 48, 49, 50, 151, 152, 53, 54),
                array(42, 44, 26, 46, 47, 48, 49, 50, 53, 54),
                6, null, 6, null,
            ),
            array(
                array(2 => 10, 7 => 14, 8 => 16, 11 => 20),
                array(),
                array(42, 143, 44, 145, 46, 47, 48, 49, 50, 151, 152, 53, 54),
                array(42, 10, 44, 46, 47, 14, 16, 48, 49, 20, 50, 53, 54),
            ),
            array(
                array(2 => 10, 7 => 14, 8 => 16, 11 => 20),
                array(),
                array(42, 143, 44, 145, 46, 47, 48, 49, 50, 151, 152, 53, 54),
                array(42, 14, 16, 44, 20, 46, 47, 48, 49, 50, 53, 54),
                6, null, 5, null,
            ),
            array(
                array(2 => 10, 7 => 14, 8 => 16, 11 => 20),
                array(3 => 25, 9 => 26),
                array(),
                array(),
            ),
            array(
                array(0 => 10, 7 => 14, 8 => 16, 11 => 20),
                array(3 => 25, 9 => 26),
                array(),
                array(10),
            ),
            array(
                array(2 => 10, 7 => 14, 8 => 16, 11 => 20),
                array(0 => 25, 9 => 26),
                array(),
                array(25),
            ),
            array(
                array(),
                array(),
                array(),
                array(),
            ),
        );
    }

    /**
     * Builds the list of value objects as returned by queries from provided IDs.
     *
     * @param array $ids
     *
     * @return \Netgen\BlockManager\Tests\Collection\Stubs\Value[]
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
     * Builds the list of ResultValue objects from provided IDs.
     *
     * @param array $ids
     *
     * @return \Netgen\BlockManager\Collection\ResultValue[]
     */
    protected function buildExpectedResultValues(array $ids = array())
    {
        return array_map(
            function ($id) {
                return $this->resultValueBuilder->build(new Value($id));
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
