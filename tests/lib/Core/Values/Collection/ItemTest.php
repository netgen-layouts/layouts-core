<?php

namespace Netgen\BlockManager\Tests\Core\Values\Collection;

use DateTime;
use DateTimeInterface;
use Netgen\BlockManager\API\Values\Value;
use Netgen\BlockManager\Core\Values\Collection\Item;
use Netgen\BlockManager\Core\Values\Config\Config;
use Netgen\BlockManager\Item\Item as CmsItem;
use Netgen\BlockManager\Item\NullItem;
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
     * @covers \Netgen\BlockManager\Core\Values\Collection\Item::getCmsItem
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
        $this->assertNull($item->getCmsItem());
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
     * @covers \Netgen\BlockManager\Core\Values\Collection\Item::getCmsItem
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
                'cmsItem' => function () {
                    return new CmsItem();
                },
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
        $this->assertEquals(new CmsItem(), $item->getCmsItem());
        $this->assertTrue($item->isPublished());
    }

    /**
     * @param string $visibilityStatus
     * @param \DateTimeInterface $visibleFrom
     * @param \DateTimeInterface $visibleTo
     * @param bool $itemVisible
     *
     * @covers \Netgen\BlockManager\Core\Values\Collection\Item::isVisible
     *
     * @dataProvider visibilityProvider
     */
    public function testVisibility(
        $visibilityStatus,
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
                                'visibility_status' => new Parameter(
                                    array(
                                        'value' => $visibilityStatus,
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

    /**
     * @param string $visibilityStatus
     * @param bool $itemScheduled
     *
     * @covers \Netgen\BlockManager\Core\Values\Collection\Item::isScheduled
     *
     * @dataProvider scheduledProvider
     */
    public function testIsScheduled(
        $visibilityStatus,
        $itemScheduled = true
    ) {
        $item = new Item(
            array(
                'configs' => array(
                    'visibility' => new Config(
                        array(
                            'parameters' => array(
                                'visibility_status' => new Parameter(
                                    array(
                                        'value' => $visibilityStatus,
                                    )
                                ),
                            ),
                        )
                    ),
                ),
            )
        );

        $this->assertEquals($itemScheduled, $item->isScheduled());
    }

    /**
     * @param bool $itemVisible
     * @param bool $cmsItemVisible
     * @param bool $isValid
     *
     * @covers \Netgen\BlockManager\Core\Values\Collection\Item::isValid
     * @dataProvider isValidProvider
     */
    public function testIsValid($itemVisible, $cmsItemVisible, $isValid)
    {
        $item = new Item(
            array(
                'cmsItem' => new CmsItem(array('isVisible' => $cmsItemVisible)),
                'configs' => array(
                    'visibility' => new Config(
                        array(
                            'parameters' => array(
                                'visibility_status' => new Parameter(
                                    array(
                                        'value' => $itemVisible ? Item::VISIBILITY_VISIBLE : Item::VISIBILITY_HIDDEN,
                                    )
                                ),
                            ),
                        )
                    ),
                ),
            )
        );

        $this->assertEquals($isValid, $item->isValid());
    }

    /**
     * @param bool $itemVisible
     *
     * @covers \Netgen\BlockManager\Core\Values\Collection\Item::isValid
     * @dataProvider isValidProvider
     */
    public function testIsValidWithNullItem($itemVisible)
    {
        $item = new Item(
            array(
                'cmsItem' => new NullItem(42),
                'configs' => array(
                    'visibility' => new Config(
                        array(
                            'parameters' => array(
                                'visibility_status' => new Parameter(
                                    array(
                                        'value' => $itemVisible ? Item::VISIBILITY_VISIBLE : Item::VISIBILITY_HIDDEN,
                                    )
                                ),
                            ),
                        )
                    ),
                ),
            )
        );

        $this->assertFalse($item->isValid());
    }

    public function isValidProvider()
    {
        return array(
            array(true, true, true),
            array(false, true, false),
            array(true, false, false),
            array(false, false, false),
        );
    }

    public function visibilityProvider()
    {
        return array(
            array(Item::VISIBILITY_VISIBLE, new DateTime('@10000'), new DateTime('@20000'), true),
            array(Item::VISIBILITY_VISIBLE, new DateTime('@17000'), new DateTime('@20000'), true),
            array(Item::VISIBILITY_VISIBLE, new DateTime('@10000'), new DateTime('@13000'), true),
            array(Item::VISIBILITY_VISIBLE, new DateTime('@10000'), new DateTime('@15000'), true),
            array(Item::VISIBILITY_VISIBLE, new DateTime('@15000'), new DateTime('@20000'), true),
            array(Item::VISIBILITY_VISIBLE, new DateTime('@20000'), new DateTime('@10000'), true),
            array(Item::VISIBILITY_VISIBLE, new DateTime('@20000'), new DateTime('@17000'), true),
            array(Item::VISIBILITY_VISIBLE, new DateTime('@13000'), new DateTime('@10000'), true),
            array(Item::VISIBILITY_VISIBLE, new DateTime('@15000'), new DateTime('@10000'), true),
            array(Item::VISIBILITY_VISIBLE, new DateTime('@20000'), new DateTime('@15000'), true),
            array(Item::VISIBILITY_VISIBLE, null, new DateTime('@20000'), true),
            array(Item::VISIBILITY_VISIBLE, null, new DateTime('@15000'), true),
            array(Item::VISIBILITY_VISIBLE, null, new DateTime('@10000'), true),
            array(Item::VISIBILITY_VISIBLE, new DateTime('@10000'), null, true),
            array(Item::VISIBILITY_VISIBLE, new DateTime('@15000'), null, true),
            array(Item::VISIBILITY_VISIBLE, new DateTime('@20000'), null, true),
            array(Item::VISIBILITY_VISIBLE, null, null, true),
            array(Item::VISIBILITY_SCHEDULED, new DateTime('@10000'), new DateTime('@20000'), true),
            array(Item::VISIBILITY_SCHEDULED, new DateTime('@17000'), new DateTime('@20000'), false),
            array(Item::VISIBILITY_SCHEDULED, new DateTime('@10000'), new DateTime('@13000'), false),
            array(Item::VISIBILITY_SCHEDULED, new DateTime('@10000'), new DateTime('@15000'), true),
            array(Item::VISIBILITY_SCHEDULED, new DateTime('@15000'), new DateTime('@20000'), true),
            array(Item::VISIBILITY_SCHEDULED, new DateTime('@20000'), new DateTime('@10000'), false),
            array(Item::VISIBILITY_SCHEDULED, new DateTime('@20000'), new DateTime('@17000'), false),
            array(Item::VISIBILITY_SCHEDULED, new DateTime('@13000'), new DateTime('@10000'), false),
            array(Item::VISIBILITY_SCHEDULED, new DateTime('@15000'), new DateTime('@10000'), false),
            array(Item::VISIBILITY_SCHEDULED, new DateTime('@20000'), new DateTime('@15000'), false),
            array(Item::VISIBILITY_SCHEDULED, null, new DateTime('@20000'), true),
            array(Item::VISIBILITY_SCHEDULED, null, new DateTime('@15000'), true),
            array(Item::VISIBILITY_SCHEDULED, null, new DateTime('@10000'), false),
            array(Item::VISIBILITY_SCHEDULED, new DateTime('@10000'), null, true),
            array(Item::VISIBILITY_SCHEDULED, new DateTime('@15000'), null, true),
            array(Item::VISIBILITY_SCHEDULED, new DateTime('@20000'), null, false),
            array(Item::VISIBILITY_SCHEDULED, null, null, true),
            array(Item::VISIBILITY_HIDDEN, new DateTime('@10000'), new DateTime('@20000'), false),
            array(Item::VISIBILITY_HIDDEN, new DateTime('@17000'), new DateTime('@20000'), false),
            array(Item::VISIBILITY_HIDDEN, new DateTime('@10000'), new DateTime('@13000'), false),
            array(Item::VISIBILITY_HIDDEN, new DateTime('@10000'), new DateTime('@15000'), false),
            array(Item::VISIBILITY_HIDDEN, new DateTime('@15000'), new DateTime('@20000'), false),
            array(Item::VISIBILITY_HIDDEN, new DateTime('@20000'), new DateTime('@10000'), false),
            array(Item::VISIBILITY_HIDDEN, new DateTime('@20000'), new DateTime('@17000'), false),
            array(Item::VISIBILITY_HIDDEN, new DateTime('@13000'), new DateTime('@10000'), false),
            array(Item::VISIBILITY_HIDDEN, new DateTime('@15000'), new DateTime('@10000'), false),
            array(Item::VISIBILITY_HIDDEN, new DateTime('@20000'), new DateTime('@15000'), false),
            array(Item::VISIBILITY_HIDDEN, null, new DateTime('@20000'), false),
            array(Item::VISIBILITY_HIDDEN, null, new DateTime('@15000'), false),
            array(Item::VISIBILITY_HIDDEN, null, new DateTime('@10000'), false),
            array(Item::VISIBILITY_HIDDEN, new DateTime('@10000'), null, false),
            array(Item::VISIBILITY_HIDDEN, new DateTime('@15000'), null, false),
            array(Item::VISIBILITY_HIDDEN, new DateTime('@20000'), null, false),
            array(Item::VISIBILITY_HIDDEN, null, null, false),
        );
    }

    public function scheduledProvider()
    {
        return array(
            array(Item::VISIBILITY_VISIBLE, false),
            array(Item::VISIBILITY_HIDDEN, false),
            array(Item::VISIBILITY_SCHEDULED, true),
        );
    }
}
