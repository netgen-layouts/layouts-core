<?php

namespace Netgen\BlockManager\Tests\Core\Values\Collection;

use DateTime;
use DateTimeInterface;
use Netgen\BlockManager\API\Values\Value;
use Netgen\BlockManager\Core\Values\Collection\Item;
use Netgen\BlockManager\Core\Values\Config\Config;
use Netgen\BlockManager\Parameters\Parameter;
use Netgen\BlockManager\Tests\Collection\Stubs\ItemDefinition;
use PHPUnit\Framework\TestCase;

final class ItemTest extends TestCase
{
    /**
     * @covers \Netgen\BlockManager\Core\Values\Collection\Item::__construct
     * @covers \Netgen\BlockManager\Core\Values\Collection\Item::getId
     * @covers \Netgen\BlockManager\Core\Values\Collection\Item::getStatus
     * @covers \Netgen\BlockManager\Core\Values\Collection\Item::getCollectionId
     * @covers \Netgen\BlockManager\Core\Values\Collection\Item::getDefinition
     * @covers \Netgen\BlockManager\Core\Values\Collection\Item::getPosition
     * @covers \Netgen\BlockManager\Core\Values\Collection\Item::getType
     * @covers \Netgen\BlockManager\Core\Values\Collection\Item::getValue
     * @covers \Netgen\BlockManager\Core\Values\Collection\Item::getValueType
     * @covers \Netgen\BlockManager\Core\Values\Collection\Item::isPublished
     */
    public function testSetDefaultProperties()
    {
        $item = new Item();

        $this->assertNull($item->getId());
        $this->assertNull($item->getStatus());
        $this->assertNull($item->getCollectionId());
        $this->assertNull($item->getDefinition());
        $this->assertNull($item->getPosition());
        $this->assertNull($item->getType());
        $this->assertNull($item->getValue());
        $this->assertNull($item->getValueType());
        $this->assertNull($item->isPublished());
    }

    /**
     * @covers \Netgen\BlockManager\Core\Values\Collection\Item::__construct
     * @covers \Netgen\BlockManager\Core\Values\Collection\Item::getId
     * @covers \Netgen\BlockManager\Core\Values\Collection\Item::getStatus
     * @covers \Netgen\BlockManager\Core\Values\Collection\Item::getCollectionId
     * @covers \Netgen\BlockManager\Core\Values\Collection\Item::getDefinition
     * @covers \Netgen\BlockManager\Core\Values\Collection\Item::getPosition
     * @covers \Netgen\BlockManager\Core\Values\Collection\Item::getType
     * @covers \Netgen\BlockManager\Core\Values\Collection\Item::getValue
     * @covers \Netgen\BlockManager\Core\Values\Collection\Item::getValueType
     * @covers \Netgen\BlockManager\Core\Values\Collection\Item::isPublished
     */
    public function testSetProperties()
    {
        $item = new Item(
            array(
                'id' => 42,
                'status' => Value::STATUS_PUBLISHED,
                'collectionId' => 30,
                'definition' => new ItemDefinition('ezlocation'),
                'position' => 3,
                'type' => Item::TYPE_OVERRIDE,
                'value' => 32,
                'valueType' => 'ezcontent',
                'published' => true,
            )
        );

        $this->assertEquals(42, $item->getId());
        $this->assertTrue($item->isPublished());
        $this->assertEquals(30, $item->getCollectionId());
        $this->assertEquals(new ItemDefinition('ezlocation'), $item->getDefinition());
        $this->assertEquals(3, $item->getPosition());
        $this->assertEquals(Item::TYPE_OVERRIDE, $item->getType());
        $this->assertEquals(32, $item->getValue());
        $this->assertEquals('ezcontent', $item->getValueType());
        $this->assertTrue($item->isPublished());
    }

    /**
     * @param bool $visibleInConfig
     * @param \DateTimeInterface $visibleFrom
     * @param \DateTimeInterface $visibleTo
     * @param bool $itemVisible
     *
     * @covers \Netgen\BlockManager\Core\Values\Collection\Item::isVisible
     *
     * @dataProvider visibilityProvider
     */
    public function testVisibility(
        $visibleInConfig = true,
        DateTimeInterface $visibleFrom = null,
        DateTimeInterface $visibleTo = null,
        $itemVisible = true
    ) {
        $item = new Item(
            array(
                'configs' => array(
                    'visibility' => new Config(
                        array(
                            'parameters' => array(
                                'visible' => new Parameter(
                                    array(
                                        'value' => $visibleInConfig,
                                    )
                                ),
                                'visible_from' => new Parameter(
                                    array(
                                        'value' => $visibleFrom,
                                    )
                                ),
                                'visible_to' => new Parameter(
                                    array(
                                        'value' => $visibleTo,
                                    )
                                ),
                            ),
                        )
                    ),
                ),
            )
        );

        $this->assertEquals($itemVisible, $item->isVisible(new DateTime('@15000')));
    }

    public function visibilityProvider()
    {
        return array(
            array(true, new DateTime('@10000'), new DateTime('@20000'), true),
            array(true, new DateTime('@17000'), new DateTime('@20000'), false),
            array(true, new DateTime('@10000'), new DateTime('@13000'), false),
            array(true, new DateTime('@10000'), new DateTime('@15000'), true),
            array(true, new DateTime('@15000'), new DateTime('@20000'), true),
            array(true, new DateTime('@20000'), new DateTime('@10000'), false),
            array(true, new DateTime('@20000'), new DateTime('@17000'), false),
            array(true, new DateTime('@13000'), new DateTime('@10000'), false),
            array(true, new DateTime('@15000'), new DateTime('@10000'), false),
            array(true, new DateTime('@20000'), new DateTime('@15000'), false),
            array(true, null, new DateTime('@20000'), true),
            array(true, null, new DateTime('@15000'), true),
            array(true, null, new DateTime('@10000'), false),
            array(true, new DateTime('@10000'), null, true),
            array(true, new DateTime('@15000'), null, true),
            array(true, new DateTime('@20000'), null, false),
            array(true, null, null, true),
            array(false, new DateTime('@10000'), new DateTime('@20000'), false),
            array(false, new DateTime('@17000'), new DateTime('@20000'), false),
            array(false, new DateTime('@10000'), new DateTime('@13000'), false),
            array(false, new DateTime('@10000'), new DateTime('@15000'), false),
            array(false, new DateTime('@15000'), new DateTime('@20000'), false),
            array(false, new DateTime('@20000'), new DateTime('@10000'), false),
            array(false, new DateTime('@20000'), new DateTime('@17000'), false),
            array(false, new DateTime('@13000'), new DateTime('@10000'), false),
            array(false, new DateTime('@15000'), new DateTime('@10000'), false),
            array(false, new DateTime('@20000'), new DateTime('@15000'), false),
            array(false, null, new DateTime('@20000'), false),
            array(false, null, new DateTime('@15000'), false),
            array(false, null, new DateTime('@10000'), false),
            array(false, new DateTime('@10000'), null, false),
            array(false, new DateTime('@15000'), null, false),
            array(false, new DateTime('@20000'), null, false),
            array(false, null, null, false),
        );
    }
}
