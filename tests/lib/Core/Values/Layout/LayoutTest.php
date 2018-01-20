<?php

namespace Netgen\BlockManager\Tests\Core\Values\Layout;

use DateTime;
use Netgen\BlockManager\API\Values\Value;
use Netgen\BlockManager\Core\Values\Layout\Layout;
use Netgen\BlockManager\Core\Values\Layout\Zone;
use Netgen\BlockManager\Exception\RuntimeException;
use Netgen\BlockManager\Layout\Type\LayoutType;
use PHPUnit\Framework\TestCase;
use Traversable;

final class LayoutTest extends TestCase
{
    /**
     * @covers \Netgen\BlockManager\Core\Values\Layout\Layout::__construct
     * @covers \Netgen\BlockManager\Core\Values\Layout\Layout::getId
     * @covers \Netgen\BlockManager\Core\Values\Layout\Layout::getLayoutType
     * @covers \Netgen\BlockManager\Core\Values\Layout\Layout::getName
     * @covers \Netgen\BlockManager\Core\Values\Layout\Layout::getDescription
     * @covers \Netgen\BlockManager\Core\Values\Layout\Layout::getCreated
     * @covers \Netgen\BlockManager\Core\Values\Layout\Layout::getModified
     * @covers \Netgen\BlockManager\Core\Values\Layout\Layout::getStatus
     * @covers \Netgen\BlockManager\Core\Values\Layout\Layout::isShared
     * @covers \Netgen\BlockManager\Core\Values\Layout\Layout::getZones
     * @covers \Netgen\BlockManager\Core\Values\Layout\Layout::getZone
     * @covers \Netgen\BlockManager\Core\Values\Layout\Layout::hasZone
     * @covers \Netgen\BlockManager\Core\Values\Layout\Layout::isPublished
     * @covers \Netgen\BlockManager\Core\Values\Layout\Layout::getMainLocale
     * @covers \Netgen\BlockManager\Core\Values\Layout\Layout::getAvailableLocales
     * @covers \Netgen\BlockManager\Core\Values\Layout\Layout::hasLocale
     */
    public function testSetDefaultProperties()
    {
        $layout = new Layout();

        $this->assertNull($layout->getId());
        $this->assertNull($layout->getLayoutType());
        $this->assertNull($layout->getName());
        $this->assertNull($layout->getDescription());
        $this->assertNull($layout->getCreated());
        $this->assertNull($layout->getModified());
        $this->assertNull($layout->getStatus());
        $this->assertNull($layout->isShared());
        $this->assertEquals(array(), $layout->getZones());
        $this->assertNull($layout->getZone('test'));
        $this->assertFalse($layout->hasZone('test'));
        $this->assertNull($layout->isPublished());
        $this->assertNull($layout->getMainLocale());
        $this->assertEquals(array(), $layout->getAvailableLocales());
        $this->assertFalse($layout->hasLocale('en'));
    }

    /**
     * @covers \Netgen\BlockManager\Core\Values\Layout\Layout::__construct
     * @covers \Netgen\BlockManager\Core\Values\Layout\Layout::getId
     * @covers \Netgen\BlockManager\Core\Values\Layout\Layout::getLayoutType
     * @covers \Netgen\BlockManager\Core\Values\Layout\Layout::getName
     * @covers \Netgen\BlockManager\Core\Values\Layout\Layout::getDescription
     * @covers \Netgen\BlockManager\Core\Values\Layout\Layout::getCreated
     * @covers \Netgen\BlockManager\Core\Values\Layout\Layout::getModified
     * @covers \Netgen\BlockManager\Core\Values\Layout\Layout::getStatus
     * @covers \Netgen\BlockManager\Core\Values\Layout\Layout::isShared
     * @covers \Netgen\BlockManager\Core\Values\Layout\Layout::getZones
     * @covers \Netgen\BlockManager\Core\Values\Layout\Layout::getZone
     * @covers \Netgen\BlockManager\Core\Values\Layout\Layout::hasZone
     * @covers \Netgen\BlockManager\Core\Values\Layout\Layout::isPublished
     * @covers \Netgen\BlockManager\Core\Values\Layout\Layout::getIterator
     * @covers \Netgen\BlockManager\Core\Values\Layout\Layout::count
     * @covers \Netgen\BlockManager\Core\Values\Layout\Layout::offsetExists
     * @covers \Netgen\BlockManager\Core\Values\Layout\Layout::offsetGet
     * @covers \Netgen\BlockManager\Core\Values\Layout\Layout::offsetSet
     * @covers \Netgen\BlockManager\Core\Values\Layout\Layout::offsetUnset
     * @covers \Netgen\BlockManager\Core\Values\Layout\Layout::getMainLocale
     * @covers \Netgen\BlockManager\Core\Values\Layout\Layout::getAvailableLocales
     * @covers \Netgen\BlockManager\Core\Values\Layout\Layout::hasLocale
     */
    public function testSetProperties()
    {
        $createdDate = new DateTime();
        $createdDate->setTimestamp(123);

        $modifiedDate = new DateTime();
        $modifiedDate->setTimestamp(456);

        $zones = array(
            'left' => new Zone(array('identifier' => 'left')),
            'right' => new Zone(array('identifier' => 'right', 'linkedZone' => new Zone())),
        );

        $layout = new Layout(
            array(
                'id' => 42,
                'layoutType' => new LayoutType(array('identifier' => '4_zones_a')),
                'name' => 'My layout',
                'description' => 'My description',
                'created' => $createdDate,
                'modified' => $modifiedDate,
                'status' => Value::STATUS_PUBLISHED,
                'shared' => true,
                'zones' => $zones,
                'published' => true,
                'mainLocale' => 'en',
                'availableLocales' => array('en'),
            )
        );

        $this->assertEquals(42, $layout->getId());
        $this->assertEquals(new LayoutType(array('identifier' => '4_zones_a')), $layout->getLayoutType());
        $this->assertEquals('My layout', $layout->getName());
        $this->assertEquals('My description', $layout->getDescription());
        $this->assertEquals($createdDate, $layout->getCreated());
        $this->assertEquals($modifiedDate, $layout->getModified());
        $this->assertTrue($layout->isPublished());
        $this->assertTrue($layout->isShared());
        $this->assertEquals($zones, $layout->getZones());
        $this->assertNull($layout->getZone('test'));
        $this->assertFalse($layout->hasZone('test'));
        $this->assertEquals($zones['right']->getLinkedZone(), $layout->getZone('right'));
        $this->assertEquals($zones['right'], $layout->getZone('right', true));
        $this->assertTrue($layout->hasZone('right'));
        $this->assertTrue($layout->isPublished());
        $this->assertEquals('en', $layout->getMainLocale());
        $this->assertEquals(array('en'), $layout->getAvailableLocales());
        $this->assertTrue($layout->hasLocale('en'));
        $this->assertFalse($layout->hasLocale('hr'));

        $this->assertInstanceOf(Traversable::class, $layout->getIterator());
        $this->assertEquals($zones, iterator_to_array($layout->getIterator()));

        $this->assertCount(2, $layout);

        $this->assertTrue(isset($layout['left']));
        $this->assertEquals($zones['left'], $layout['left']);

        try {
            $layout['left'] = new Zone();
            $this->fail('Succeeded in setting a new zone to layout.');
        } catch (RuntimeException $e) {
            // Do nothing
        }

        try {
            unset($layout['left']);
            $this->fail('Succeeded in unsetting a zone in layout.');
        } catch (RuntimeException $e) {
            // Do nothing
        }
    }
}
