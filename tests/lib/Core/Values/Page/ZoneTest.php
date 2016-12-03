<?php

namespace Netgen\BlockManager\Tests\Core\Values\Page;

use Netgen\BlockManager\API\Values\Value;
use Netgen\BlockManager\Core\Values\Page\Block;
use Netgen\BlockManager\Core\Values\Page\Zone;
use Netgen\BlockManager\Exception\RuntimeException;
use PHPUnit\Framework\TestCase;
use Traversable;

class ZoneTest extends TestCase
{
    /**
     * @covers \Netgen\BlockManager\Core\Values\Page\Zone::__construct
     * @covers \Netgen\BlockManager\Core\Values\Page\Zone::getIdentifier
     * @covers \Netgen\BlockManager\Core\Values\Page\Zone::getLayoutId
     * @covers \Netgen\BlockManager\Core\Values\Page\Zone::getStatus
     * @covers \Netgen\BlockManager\Core\Values\Page\Zone::getLinkedZone
     * @covers \Netgen\BlockManager\Core\Values\Page\Zone::getBlocks
     * @covers \Netgen\BlockManager\Core\Values\Page\Zone::isPublished
     * @covers \Netgen\BlockManager\Core\Values\Page\Zone::count
     */
    public function testSetDefaultProperties()
    {
        $zone = new Zone();

        $this->assertNull($zone->getIdentifier());
        $this->assertNull($zone->getLayoutId());
        $this->assertNull($zone->getStatus());
        $this->assertNull($zone->getLinkedZone());
        $this->assertEquals(array(), $zone->getBlocks());
        $this->assertNull($zone->isPublished());
        $this->assertEquals(0, count($zone));
    }

    /**
     * @covers \Netgen\BlockManager\Core\Values\Page\Zone::__construct
     * @covers \Netgen\BlockManager\Core\Values\Page\Zone::getIdentifier
     * @covers \Netgen\BlockManager\Core\Values\Page\Zone::getLayoutId
     * @covers \Netgen\BlockManager\Core\Values\Page\Zone::getStatus
     * @covers \Netgen\BlockManager\Core\Values\Page\Zone::getLinkedZone
     * @covers \Netgen\BlockManager\Core\Values\Page\Zone::getBlocks
     * @covers \Netgen\BlockManager\Core\Values\Page\Zone::isPublished
     * @covers \Netgen\BlockManager\Core\Values\Page\Zone::getIterator
     * @covers \Netgen\BlockManager\Core\Values\Page\Zone::count
     * @covers \Netgen\BlockManager\Core\Values\Page\Zone::offsetExists
     * @covers \Netgen\BlockManager\Core\Values\Page\Zone::offsetGet
     * @covers \Netgen\BlockManager\Core\Values\Page\Zone::offsetSet
     * @covers \Netgen\BlockManager\Core\Values\Page\Zone::offsetUnset
     */
    public function testSetProperties()
    {
        $zone = new Zone(
            array(
                'identifier' => 'left',
                'layoutId' => 84,
                'status' => Value::STATUS_PUBLISHED,
                'linkedZone' => null,
                'published' => true,
                'blocks' => array(
                    new Block(),
                ),
            )
        );

        $this->assertEquals('left', $zone->getIdentifier());
        $this->assertEquals(84, $zone->getLayoutId());
        $this->assertTrue($zone->isPublished());
        $this->assertEquals(array(new Block()), $zone->getBlocks());
        $this->assertNull($zone->getLinkedZone());
        $this->assertTrue($zone->isPublished());

        $this->assertInstanceOf(Traversable::class, $zone->getIterator());
        $this->assertEquals(array(new Block()), iterator_to_array($zone->getIterator()));

        $this->assertCount(1, $zone);

        $this->assertTrue(isset($zone[0]));
        $this->assertEquals(new Block(), $zone[0]);

        try {
            $zone[0] = new Block();
            $this->fail('Succeeded in setting a new block to zone.');
        } catch (RuntimeException $e) {
            // Do nothing
        }

        try {
            unset($zone[0]);
            $this->fail('Succeeded in unsetting a block in zone.');
        } catch (RuntimeException $e) {
            // Do nothing
        }
    }
}
