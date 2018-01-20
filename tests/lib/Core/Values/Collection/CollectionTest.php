<?php

namespace Netgen\BlockManager\Tests\Core\Values\Collection;

use Netgen\BlockManager\API\Values\Value;
use Netgen\BlockManager\Core\Values\Collection\Collection;
use Netgen\BlockManager\Core\Values\Collection\Item;
use Netgen\BlockManager\Core\Values\Collection\Query;
use PHPUnit\Framework\TestCase;

final class CollectionTest extends TestCase
{
    /**
     * @covers \Netgen\BlockManager\Core\Values\Collection\Collection::__construct
     * @covers \Netgen\BlockManager\Core\Values\Collection\Collection::getId
     * @covers \Netgen\BlockManager\Core\Values\Collection\Collection::getStatus
     * @covers \Netgen\BlockManager\Core\Values\Collection\Collection::getType
     * @covers \Netgen\BlockManager\Core\Values\Collection\Collection::getOffset
     * @covers \Netgen\BlockManager\Core\Values\Collection\Collection::getLimit
     * @covers \Netgen\BlockManager\Core\Values\Collection\Collection::getItems
     * @covers \Netgen\BlockManager\Core\Values\Collection\Collection::getManualItems
     * @covers \Netgen\BlockManager\Core\Values\Collection\Collection::getOverrideItems
     * @covers \Netgen\BlockManager\Core\Values\Collection\Collection::getQuery
     * @covers \Netgen\BlockManager\Core\Values\Collection\Collection::hasQuery
     * @covers \Netgen\BlockManager\Core\Values\Collection\Collection::isPublished
     * @covers \Netgen\BlockManager\Core\Values\Collection\Collection::getMainLocale
     * @covers \Netgen\BlockManager\Core\Values\Collection\Collection::getAvailableLocales
     * @covers \Netgen\BlockManager\Core\Values\Collection\Collection::isTranslatable
     * @covers \Netgen\BlockManager\Core\Values\Collection\Collection::isAlwaysAvailable
     * @covers \Netgen\BlockManager\Core\Values\Collection\Collection::getLocale
     */
    public function testSetDefaultProperties()
    {
        $collection = new Collection();

        $this->assertNull($collection->getId());
        $this->assertNull($collection->getStatus());
        $this->assertNull($collection->getType());
        $this->assertNull($collection->getOffset());
        $this->assertNull($collection->getLimit());
        $this->assertNull($collection->isPublished());
        $this->assertNull($collection->getMainLocale());
        $this->assertEquals(array(), $collection->getAvailableLocales());
        $this->assertNull($collection->isTranslatable());
        $this->assertNull($collection->isAlwaysAvailable());
        $this->assertNull($collection->getLocale());
        $this->assertEquals(array(), $collection->getItems());
        $this->assertEquals(array(), $collection->getManualItems());
        $this->assertEquals(array(), $collection->getOverrideItems());
        $this->assertNull($collection->getQuery());
        $this->assertFalse($collection->hasQuery());
    }

    /**
     * @covers \Netgen\BlockManager\Core\Values\Collection\Collection::__construct
     * @covers \Netgen\BlockManager\Core\Values\Collection\Collection::getId
     * @covers \Netgen\BlockManager\Core\Values\Collection\Collection::getStatus
     * @covers \Netgen\BlockManager\Core\Values\Collection\Collection::getType
     * @covers \Netgen\BlockManager\Core\Values\Collection\Collection::getOffset
     * @covers \Netgen\BlockManager\Core\Values\Collection\Collection::getLimit
     * @covers \Netgen\BlockManager\Core\Values\Collection\Collection::hasItem
     * @covers \Netgen\BlockManager\Core\Values\Collection\Collection::getItem
     * @covers \Netgen\BlockManager\Core\Values\Collection\Collection::filterItems
     * @covers \Netgen\BlockManager\Core\Values\Collection\Collection::getItems
     * @covers \Netgen\BlockManager\Core\Values\Collection\Collection::hasManualItem
     * @covers \Netgen\BlockManager\Core\Values\Collection\Collection::getManualItem
     * @covers \Netgen\BlockManager\Core\Values\Collection\Collection::getManualItems
     * @covers \Netgen\BlockManager\Core\Values\Collection\Collection::hasOverrideItem
     * @covers \Netgen\BlockManager\Core\Values\Collection\Collection::getOverrideItem
     * @covers \Netgen\BlockManager\Core\Values\Collection\Collection::getOverrideItems
     * @covers \Netgen\BlockManager\Core\Values\Collection\Collection::getQuery
     * @covers \Netgen\BlockManager\Core\Values\Collection\Collection::hasQuery
     * @covers \Netgen\BlockManager\Core\Values\Collection\Collection::isPublished
     * @covers \Netgen\BlockManager\Core\Values\Collection\Collection::getMainLocale
     * @covers \Netgen\BlockManager\Core\Values\Collection\Collection::getAvailableLocales
     * @covers \Netgen\BlockManager\Core\Values\Collection\Collection::isTranslatable
     * @covers \Netgen\BlockManager\Core\Values\Collection\Collection::isAlwaysAvailable
     * @covers \Netgen\BlockManager\Core\Values\Collection\Collection::getLocale
     */
    public function testSetProperties()
    {
        $items = array(
            new Item(array('type' => Item::TYPE_MANUAL, 'position' => 3)),
            new Item(array('type' => Item::TYPE_OVERRIDE, 'position' => 5)),
        );

        $collection = new Collection(
            array(
                'id' => 42,
                'status' => Value::STATUS_PUBLISHED,
                'type' => Collection::TYPE_DYNAMIC,
                'offset' => 5,
                'limit' => 10,
                'published' => true,
                'mainLocale' => 'en',
                'availableLocales' => array('en', 'hr'),
                'isTranslatable' => true,
                'alwaysAvailable' => false,
                'locale' => 'en',
                'items' => $items,
                'query' => new Query(),
            )
        );

        $this->assertEquals(42, $collection->getId());
        $this->assertTrue($collection->isPublished());
        $this->assertEquals(Collection::TYPE_DYNAMIC, $collection->getType());
        $this->assertEquals(5, $collection->getOffset());
        $this->assertEquals(10, $collection->getLimit());
        $this->assertTrue($collection->isPublished());
        $this->assertEquals('en', $collection->getMainLocale());
        $this->assertEquals(array('en', 'hr'), $collection->getAvailableLocales());
        $this->assertTrue($collection->isTranslatable());
        $this->assertFalse($collection->isAlwaysAvailable());
        $this->assertEquals('en', $collection->getLocale());
        $this->assertCount(2, $collection->getItems());
        $this->assertCount(1, $collection->getManualItems());
        $this->assertCount(1, $collection->getOverrideItems());

        $this->assertEquals(Item::TYPE_MANUAL, $collection->getItems()[0]->getType());
        $this->assertEquals(Item::TYPE_OVERRIDE, $collection->getItems()[1]->getType());
        $this->assertEquals(Item::TYPE_MANUAL, $collection->getManualItems()[3]->getType());
        $this->assertEquals(Item::TYPE_OVERRIDE, $collection->getOverrideItems()[5]->getType());

        $this->assertEquals(new Query(), $collection->getQuery());
        $this->assertTrue($collection->hasQuery());

        $this->assertFalse($collection->hasManualItem(2));
        $this->assertTrue($collection->hasManualItem(3));

        $this->assertEquals($items[0], $collection->getManualItem(3));
        $this->assertNull($collection->getManualItem(2));

        $this->assertFalse($collection->hasOverrideItem(4));
        $this->assertTrue($collection->hasOverrideItem(5));

        $this->assertEquals($items[1], $collection->getOverrideItem(5));
        $this->assertNull($collection->getOverrideItem(4));
    }
}
