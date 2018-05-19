<?php

namespace Netgen\BlockManager\Tests\Core\Values\Collection;

use Doctrine\Common\Collections\ArrayCollection;
use Netgen\BlockManager\API\Values\Value;
use Netgen\BlockManager\Core\Values\Collection\Collection;
use Netgen\BlockManager\Core\Values\Collection\Item;
use Netgen\BlockManager\Core\Values\Collection\Query;
use PHPUnit\Framework\TestCase;

final class CollectionTest extends TestCase
{
    public function testInstance()
    {
        $this->assertInstanceOf(Value::class, new Collection());
    }

    /**
     * @covers \Netgen\BlockManager\Core\Values\Collection\Collection::__construct
     * @covers \Netgen\BlockManager\Core\Values\Collection\Collection::getAvailableLocales
     * @covers \Netgen\BlockManager\Core\Values\Collection\Collection::getItems
     */
    public function testDefaultProperties()
    {
        $collection = new Collection();

        $this->assertEquals([], $collection->getAvailableLocales());
        $this->assertEquals([], $collection->getItems());
    }

    /**
     * @covers \Netgen\BlockManager\Core\Values\Collection\Collection::__construct
     * @covers \Netgen\BlockManager\Core\Values\Collection\Collection::filterItems
     * @covers \Netgen\BlockManager\Core\Values\Collection\Collection::getAvailableLocales
     * @covers \Netgen\BlockManager\Core\Values\Collection\Collection::getId
     * @covers \Netgen\BlockManager\Core\Values\Collection\Collection::getItem
     * @covers \Netgen\BlockManager\Core\Values\Collection\Collection::getItems
     * @covers \Netgen\BlockManager\Core\Values\Collection\Collection::getLimit
     * @covers \Netgen\BlockManager\Core\Values\Collection\Collection::getLocale
     * @covers \Netgen\BlockManager\Core\Values\Collection\Collection::getMainLocale
     * @covers \Netgen\BlockManager\Core\Values\Collection\Collection::getManualItem
     * @covers \Netgen\BlockManager\Core\Values\Collection\Collection::getManualItems
     * @covers \Netgen\BlockManager\Core\Values\Collection\Collection::getOffset
     * @covers \Netgen\BlockManager\Core\Values\Collection\Collection::getOverrideItem
     * @covers \Netgen\BlockManager\Core\Values\Collection\Collection::getOverrideItems
     * @covers \Netgen\BlockManager\Core\Values\Collection\Collection::getQuery
     * @covers \Netgen\BlockManager\Core\Values\Collection\Collection::getType
     * @covers \Netgen\BlockManager\Core\Values\Collection\Collection::hasItem
     * @covers \Netgen\BlockManager\Core\Values\Collection\Collection::hasManualItem
     * @covers \Netgen\BlockManager\Core\Values\Collection\Collection::hasOverrideItem
     * @covers \Netgen\BlockManager\Core\Values\Collection\Collection::hasQuery
     * @covers \Netgen\BlockManager\Core\Values\Collection\Collection::isAlwaysAvailable
     * @covers \Netgen\BlockManager\Core\Values\Collection\Collection::isTranslatable
     */
    public function testSetProperties()
    {
        $items = [
            new Item(['type' => Item::TYPE_MANUAL, 'position' => 3]),
            new Item(['type' => Item::TYPE_OVERRIDE, 'position' => 5]),
        ];

        $collection = new Collection(
            [
                'id' => 42,
                'offset' => 5,
                'limit' => 10,
                'mainLocale' => 'en',
                'availableLocales' => ['en', 'hr'],
                'isTranslatable' => true,
                'alwaysAvailable' => false,
                'locale' => 'en',
                'items' => new ArrayCollection($items),
                'query' => new Query(),
            ]
        );

        $this->assertEquals(42, $collection->getId());
        $this->assertEquals(Collection::TYPE_DYNAMIC, $collection->getType());
        $this->assertEquals(5, $collection->getOffset());
        $this->assertEquals(10, $collection->getLimit());
        $this->assertEquals('en', $collection->getMainLocale());
        $this->assertEquals(['en', 'hr'], $collection->getAvailableLocales());
        $this->assertTrue($collection->isTranslatable());
        $this->assertFalse($collection->isAlwaysAvailable());
        $this->assertEquals('en', $collection->getLocale());
        $this->assertCount(2, $collection->getItems());
        $this->assertCount(1, $collection->getManualItems());
        $this->assertCount(1, $collection->getOverrideItems());

        $this->assertEquals(Item::TYPE_MANUAL, $collection->getItem(3)->getType());
        $this->assertEquals(Item::TYPE_OVERRIDE, $collection->getItem(5)->getType());
        $this->assertEquals(Item::TYPE_MANUAL, $collection->getManualItem(3)->getType());
        $this->assertEquals(Item::TYPE_OVERRIDE, $collection->getOverrideItem(5)->getType());

        $this->assertEquals(new Query(), $collection->getQuery());
        $this->assertTrue($collection->hasQuery());

        $this->assertFalse($collection->hasItem(2));
        $this->assertTrue($collection->hasItem(3));
        $this->assertTrue($collection->hasItem(5));

        $this->assertEquals($items[0], $collection->getItem(3));
        $this->assertEquals($items[1], $collection->getItem(5));

        $this->assertFalse($collection->hasManualItem(2));
        $this->assertTrue($collection->hasManualItem(3));

        $this->assertEquals($items[0], $collection->getManualItem(3));
        $this->assertNull($collection->getManualItem(2));

        $this->assertFalse($collection->hasOverrideItem(4));
        $this->assertTrue($collection->hasOverrideItem(5));

        $this->assertEquals($items[1], $collection->getOverrideItem(5));
        $this->assertNull($collection->getOverrideItem(4));
    }

    /**
     * @covers \Netgen\BlockManager\Core\Values\Collection\Collection::getOffset
     */
    public function testGetOffsetForManualCollection()
    {
        $collection = new Collection(
            [
                'offset' => 5,
            ]
        );

        $this->assertEquals(0, $collection->getOffset());
    }
}
