<?php

namespace Netgen\BlockManager\Tests\Collection\Result;

use Netgen\BlockManager\Item\Registry\ValueLoaderRegistryInterface;
use Netgen\BlockManager\Collection\Result\Result;
use Netgen\BlockManager\Collection\Result\ResultBuilder;
use Netgen\BlockManager\Item\ItemBuilder;
use Netgen\BlockManager\Collection\Result\ResultItem;
use Netgen\BlockManager\Core\Values\Collection\Item;
use Netgen\BlockManager\Core\Values\Collection\Query;
use Netgen\BlockManager\Core\Values\Collection\Collection;
use Netgen\BlockManager\Tests\Collection\Stubs\QueryType;
use Netgen\BlockManager\Tests\Item\Stubs\Value;
use Netgen\BlockManager\Tests\Item\Stubs\ValueConverter;
use Netgen\BlockManager\Tests\Item\Stubs\ValueLoader;
use PHPUnit\Framework\TestCase;

class ResultBuilderTest extends TestCase
{
    /**
     * @var \Netgen\BlockManager\Item\ItemBuilderInterface
     */
    protected $itemBuilder;

    /**
     * @var \PHPUnit_Framework_MockObject_MockObject
     */
    protected $valueLoaderRegistryMock;

    /**
     * @var \Netgen\BlockManager\Collection\Result\ResultBuilderInterface
     */
    protected $resultBuilder;

    public function setUp()
    {
        $this->valueLoaderRegistryMock = $this->createMock(ValueLoaderRegistryInterface::class);

        $this->valueLoaderRegistryMock
            ->expects($this->any())
            ->method('getValueLoader')
            ->will($this->returnValue(new ValueLoader()));

        $this->itemBuilder = new ItemBuilder(
            $this->valueLoaderRegistryMock,
            array(new ValueConverter())
        );

        $this->resultBuilder = new ResultBuilder($this->itemBuilder);
    }

    /**
     * @covers \Netgen\BlockManager\Collection\Result\ResultBuilder::__construct
     * @covers \Netgen\BlockManager\Collection\Result\ResultBuilder::buildResult
     */
    public function testBuildResultForManualCollection()
    {
        $collection = $this->buildCollection(
            array(42, 43, 44, 45, 46, 47, 48, 49, 50, 51, 52, 53, 54)
        );

        $result = $this->resultBuilder->buildResult($collection, 0, 5);

        $this->assertInstanceOf(Result::class, $result);
        $this->assertEquals($collection, $result->getCollection());
        $this->assertEquals(0, $result->getOffset());
        $this->assertEquals(5, $result->getLimit());

        foreach ($result->getResults() as $index => $resultItem) {
            $this->assertInstanceOf(ResultItem::class, $resultItem);
            $this->assertEquals(ResultItem::TYPE_MANUAL, $resultItem->getType());
            $this->assertEquals($index, $resultItem->getPosition());
        }
    }

    /**
     * @covers \Netgen\BlockManager\Collection\Result\ResultBuilder::buildResult
     */
    public function testBuildResultForDynamicCollection()
    {
        $collection = $this->buildCollection(
            array(2 => 10, 7 => 14, 8 => 16, 11 => 20),
            array(3 => 25, 9 => 26),
            array(array(42, 43, 44, 45, 0, 0, 0, 0, 0, 0, 0, 0, 0)),
            array(13)
        );

        $result = $this->resultBuilder->buildResult($collection, 0, 5);

        $this->assertInstanceOf(Result::class, $result);
        $this->assertEquals($collection, $result->getCollection());
        $this->assertEquals(0, $result->getOffset());
        $this->assertEquals(5, $result->getLimit());

        foreach ($result->getResults() as $index => $resultItem) {
            $this->assertInstanceOf(ResultItem::class, $resultItem);
            $this->assertEquals($index, $resultItem->getPosition());
            // @todo Test item types
        }
    }

    /**
     * Builds the dynamic collection for provided type and list of value IDs.
     *
     * @param array $manualIds
     * @param array $overrideIds
     * @param array $queryValues
     * @param array $queryCounts
     *
     * @return \Netgen\BlockManager\Core\Values\Collection\Collection
     */
    protected function buildCollection(
        array $manualIds = array(),
        array $overrideIds = array(),
        array $queryValues = array(),
        array $queryCounts = array()
    ) {
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

        $queries = array();

        foreach ($queryValues as $index => $singleQueryValues) {
            $queries[] = new Query(
                array(
                    'identifier' => $index,
                    'queryType' => new QueryType(
                        'ezcontent_search',
                        $this->buildQueryValues($singleQueryValues),
                        isset($queryCounts[$index]) ? $queryCounts[$index] : null
                    ),
                )
            );
        }

        $collection = new Collection(
            array(
                'items' => $items,
                'queries' => $queries,
            )
        );

        return $collection;
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
}
