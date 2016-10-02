<?php

namespace Netgen\BlockManager\Tests\Collection\Result;

use Netgen\BlockManager\Collection\Result\ResultIteratorFactory;
use Netgen\BlockManager\Item\ItemLoader;
use Netgen\BlockManager\Item\Registry\ValueLoaderRegistry;
use Netgen\BlockManager\Collection\Result\ResultSet;
use Netgen\BlockManager\Collection\Result\ResultLoader;
use Netgen\BlockManager\Item\ItemBuilder;
use Netgen\BlockManager\Collection\Result\Result;
use Netgen\BlockManager\Core\Values\Collection\Item;
use Netgen\BlockManager\Core\Values\Collection\Query;
use Netgen\BlockManager\Core\Values\Collection\Collection;
use Netgen\BlockManager\Tests\Collection\Stubs\QueryType;
use Netgen\BlockManager\Tests\Item\Stubs\Value;
use Netgen\BlockManager\Tests\Item\Stubs\ValueConverter;
use Netgen\BlockManager\Tests\Item\Stubs\ValueLoader;
use PHPUnit\Framework\TestCase;

class ResultLoaderTest extends TestCase
{
    /**
     * @var \Netgen\BlockManager\Item\ItemBuilderInterface
     */
    protected $itemBuilder;

    /**
     * @var \Netgen\BlockManager\Item\ItemLoaderInterface
     */
    protected $itemLoader;

    /**
     * @var \Netgen\BlockManager\Item\Registry\ValueLoaderRegistryInterface
     */
    protected $valueLoaderRegistry;

    /**
     * @var \Netgen\BlockManager\Collection\Result\ResultLoaderInterface
     */
    protected $resultLoader;

    public function setUp()
    {
        $this->valueLoaderRegistry = new ValueLoaderRegistry();
        $this->valueLoaderRegistry->addValueLoader(new ValueLoader());

        $this->itemBuilder = new ItemBuilder(
            array(new ValueConverter())
        );

        $this->itemLoader = new ItemLoader(
            $this->valueLoaderRegistry,
            $this->itemBuilder
        );

        $this->resultLoader = new ResultLoader(
            $this->itemLoader,
            $this->itemBuilder,
            new ResultIteratorFactory()
        );
    }

    /**
     * @covers \Netgen\BlockManager\Collection\Result\ResultLoader::__construct
     * @covers \Netgen\BlockManager\Collection\Result\ResultLoader::load
     */
    public function testLoadForManualCollection()
    {
        $collection = $this->buildCollection(
            array(42, 43, 44, 45, 46, 47, 48, 49, 50, 51, 52, 53, 54)
        );

        $result = $this->resultLoader->load($collection, 0, 5);

        $this->assertInstanceOf(ResultSet::class, $result);
        $this->assertEquals($collection, $result->getCollection());
        $this->assertEquals(0, $result->getOffset());
        $this->assertEquals(5, $result->getLimit());

        foreach ($result->getResults() as $index => $resultItem) {
            $this->assertInstanceOf(Result::class, $resultItem);
            $this->assertEquals(Result::TYPE_MANUAL, $resultItem->getType());
            $this->assertEquals($index, $resultItem->getPosition());
        }
    }

    /**
     * @covers \Netgen\BlockManager\Collection\Result\ResultLoader::load
     */
    public function testLoadForDynamicCollection()
    {
        $collection = $this->buildCollection(
            array(2 => 10, 7 => 14, 8 => 16, 11 => 20),
            array(3 => 25, 9 => 26),
            array(array(42, 43, 44, 45, 0, 0, 0, 0, 0, 0, 0, 0, 0)),
            array(13)
        );

        $result = $this->resultLoader->load($collection, 0, 5);

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
