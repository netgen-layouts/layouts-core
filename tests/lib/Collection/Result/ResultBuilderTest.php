<?php

namespace Netgen\BlockManager\Tests\Collection\Result;

use Netgen\BlockManager\Collection\Result\CollectionIteratorFactory;
use Netgen\BlockManager\Collection\Result\Result;
use Netgen\BlockManager\Collection\Result\ResultBuilder;
use Netgen\BlockManager\Collection\Result\ResultIteratorFactory;
use Netgen\BlockManager\Collection\Result\ResultSet;
use Netgen\BlockManager\Core\Values\Collection\Collection;
use Netgen\BlockManager\Core\Values\Collection\Item;
use Netgen\BlockManager\Core\Values\Collection\Query;
use Netgen\BlockManager\Item\ItemBuilder;
use Netgen\BlockManager\Item\ItemLoader;
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
    private $itemBuilder;

    /**
     * @var \Netgen\BlockManager\Item\ItemLoaderInterface
     */
    private $itemLoader;

    /**
     * @var \Netgen\BlockManager\Collection\Result\ResultBuilderInterface
     */
    private $resultBuilder;

    public function setUp()
    {
        $this->itemBuilder = new ItemBuilder(
            array(new ValueConverter())
        );

        $this->itemLoader = new ItemLoader(
            $this->itemBuilder,
            array('value' => new ValueLoader())
        );

        $this->resultBuilder = $this->buildResultBuilder(200);
    }

    /**
     * @covers \Netgen\BlockManager\Collection\Result\ResultBuilder::__construct
     * @covers \Netgen\BlockManager\Collection\Result\ResultBuilder::build
     */
    public function testBuildForManualCollection()
    {
        $collection = $this->buildCollection(
            array(42, 43, 44, 45, 46, 47, 48, 49, 50, 51, 52, 53, 54)
        );

        $resultSet = $this->resultBuilder->build($collection, 0, 5);

        $this->assertInstanceOf(ResultSet::class, $resultSet);
        $this->assertEquals($collection, $resultSet->getCollection());
        $this->assertEquals(0, $resultSet->getOffset());
        $this->assertEquals(5, $resultSet->getLimit());

        foreach ($resultSet as $index => $result) {
            $this->assertInstanceOf(Result::class, $result);
            $this->assertEquals(Result::TYPE_MANUAL, $result->getType());
            $this->assertEquals($index, $result->getPosition());
        }
    }

    /**
     * @covers \Netgen\BlockManager\Collection\Result\ResultBuilder::__construct
     * @covers \Netgen\BlockManager\Collection\Result\ResultBuilder::build
     */
    public function testBuildWithLimitLargerThanMaxLimit()
    {
        $resultBuilder = $this->buildResultBuilder(3);

        $collection = $this->buildCollection(
            array(42, 43, 44, 45, 46, 47, 48, 49, 50, 51, 52, 53, 54)
        );

        $resultSet = $resultBuilder->build($collection, 0, 5);

        $this->assertInstanceOf(ResultSet::class, $resultSet);
        $this->assertEquals($collection, $resultSet->getCollection());
        $this->assertEquals(0, $resultSet->getOffset());
        $this->assertEquals(3, $resultSet->getLimit());

        foreach ($resultSet as $index => $result) {
            $this->assertInstanceOf(Result::class, $result);
            $this->assertEquals(Result::TYPE_MANUAL, $result->getType());
            $this->assertEquals($index, $result->getPosition());
        }
    }

    /**
     * @covers \Netgen\BlockManager\Collection\Result\ResultBuilder::build
     */
    public function testBuildForDynamicCollection()
    {
        $collection = $this->buildCollection(
            array(2 => 10, 7 => 14, 8 => 16, 11 => 20),
            array(3 => 25, 9 => 26),
            array(42, 43, 44, 45, 0, 0, 0, 0, 0, 0, 0, 0, 0),
            13
        );

        $result = $this->resultBuilder->build($collection, 0, 5);

        $this->assertInstanceOf(ResultSet::class, $result);
        $this->assertEquals($collection, $result->getCollection());
        $this->assertEquals(0, $result->getOffset());
        $this->assertEquals(5, $result->getLimit());

        foreach ($result->getResults() as $index => $resultItem) {
            $this->assertInstanceOf(Result::class, $resultItem);
            $this->assertEquals($index, $resultItem->getPosition());
            // @todo Test item types
        }
    }

    private function buildResultBuilder($maxLimit)
    {
        return new ResultBuilder(
            new ResultIteratorFactory(
                $this->itemLoader,
                $this->itemBuilder
            ),
            new CollectionIteratorFactory(12),
            $maxLimit
        );
    }

    /**
     * Builds the dynamic collection for provided type and list of value IDs.
     *
     * @param array $manualIds
     * @param array $overrideIds
     * @param array $queryValues
     * @param int $queryCount
     *
     * @return \Netgen\BlockManager\Core\Values\Collection\Collection
     */
    private function buildCollection(
        array $manualIds = array(),
        array $overrideIds = array(),
        array $queryValues = array(),
        $queryCount = 0
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

        $collection = new Collection(
            array(
                'items' => $items,
                'query' => new Query(
                    array(
                        'queryType' => new QueryType(
                            'ezcontent_search',
                            $this->buildQueryValues($queryValues),
                            $queryCount
                        ),
                    )
                ),
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
    private function buildQueryValues(array $ids = array())
    {
        return array_map(
            function ($id) {
                return new Value($id, '');
            },
            $ids
        );
    }
}
