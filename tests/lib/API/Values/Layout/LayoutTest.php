<?php

declare(strict_types=1);

namespace Netgen\BlockManager\Tests\API\Values\Layout;

use DateTimeImmutable;
use Doctrine\Common\Collections\ArrayCollection;
use Netgen\BlockManager\API\Values\Layout\Layout;
use Netgen\BlockManager\API\Values\Layout\Zone;
use Netgen\BlockManager\API\Values\Value;
use Netgen\BlockManager\Exception\API\LayoutException;
use Netgen\BlockManager\Exception\RuntimeException;
use Netgen\BlockManager\Layout\Type\LayoutType;
use PHPUnit\Framework\TestCase;
use Traversable;

final class LayoutTest extends TestCase
{
    public function testInstance(): void
    {
        self::assertInstanceOf(Value::class, new Layout());
    }

    /**
     * @covers \Netgen\BlockManager\API\Values\Layout\Layout::__construct
     * @covers \Netgen\BlockManager\API\Values\Layout\Layout::getAvailableLocales
     * @covers \Netgen\BlockManager\API\Values\Layout\Layout::getZones
     */
    public function testDefaultProperties(): void
    {
        $layout = new Layout();

        self::assertCount(0, $layout->getZones());
        self::assertSame([], $layout->getAvailableLocales());
    }

    /**
     * @covers \Netgen\BlockManager\API\Values\Layout\Layout::__construct
     * @covers \Netgen\BlockManager\API\Values\Layout\Layout::count
     * @covers \Netgen\BlockManager\API\Values\Layout\Layout::getAvailableLocales
     * @covers \Netgen\BlockManager\API\Values\Layout\Layout::getCreated
     * @covers \Netgen\BlockManager\API\Values\Layout\Layout::getDescription
     * @covers \Netgen\BlockManager\API\Values\Layout\Layout::getId
     * @covers \Netgen\BlockManager\API\Values\Layout\Layout::getIterator
     * @covers \Netgen\BlockManager\API\Values\Layout\Layout::getLayoutType
     * @covers \Netgen\BlockManager\API\Values\Layout\Layout::getMainLocale
     * @covers \Netgen\BlockManager\API\Values\Layout\Layout::getModified
     * @covers \Netgen\BlockManager\API\Values\Layout\Layout::getName
     * @covers \Netgen\BlockManager\API\Values\Layout\Layout::getZone
     * @covers \Netgen\BlockManager\API\Values\Layout\Layout::getZones
     * @covers \Netgen\BlockManager\API\Values\Layout\Layout::hasLocale
     * @covers \Netgen\BlockManager\API\Values\Layout\Layout::hasZone
     * @covers \Netgen\BlockManager\API\Values\Layout\Layout::isShared
     * @covers \Netgen\BlockManager\API\Values\Layout\Layout::offsetExists
     * @covers \Netgen\BlockManager\API\Values\Layout\Layout::offsetGet
     * @covers \Netgen\BlockManager\API\Values\Layout\Layout::offsetSet
     * @covers \Netgen\BlockManager\API\Values\Layout\Layout::offsetUnset
     */
    public function testSetProperties(): void
    {
        $createdDate = new DateTimeImmutable();
        $createdDate->setTimestamp(123);

        $modifiedDate = new DateTimeImmutable();
        $modifiedDate->setTimestamp(456);

        $layoutType = LayoutType::fromArray(['identifier' => '4_zones_a']);

        $zones = [
            'left' => Zone::fromArray(['identifier' => 'left']),
            'right' => Zone::fromArray(['identifier' => 'right', 'linkedZone' => new Zone()]),
        ];

        $layout = Layout::fromArray(
            [
                'id' => 42,
                'layoutType' => $layoutType,
                'name' => 'My layout',
                'description' => 'My description',
                'created' => $createdDate,
                'modified' => $modifiedDate,
                'shared' => true,
                'zones' => new ArrayCollection($zones),
                'mainLocale' => 'en',
                'availableLocales' => ['en'],
            ]
        );

        self::assertSame(42, $layout->getId());
        self::assertSame($layoutType, $layout->getLayoutType());
        self::assertSame('My layout', $layout->getName());
        self::assertSame('My description', $layout->getDescription());
        self::assertSame($createdDate, $layout->getCreated());
        self::assertSame($modifiedDate, $layout->getModified());
        self::assertTrue($layout->isShared());
        self::assertFalse($layout->hasZone('test'));
        self::assertSame($zones['right'], $layout->getZone('right'));
        self::assertTrue($layout->hasZone('right'));
        self::assertSame('en', $layout->getMainLocale());
        self::assertSame(['en'], $layout->getAvailableLocales());
        self::assertTrue($layout->hasLocale('en'));
        self::assertFalse($layout->hasLocale('hr'));

        self::assertCount(2, $layout->getZones());
        self::assertSame($zones['left'], $layout->getZones()['left']);
        self::assertSame($zones['right'], $layout->getZones()['right']);

        self::assertInstanceOf(Traversable::class, $layout->getIterator());
        self::assertSame($zones, iterator_to_array($layout->getIterator()));

        self::assertCount(2, $layout);

        self::assertTrue(isset($layout['left']));
        self::assertSame($zones['left'], $layout['left']);

        try {
            $layout->getZone('test');
        } catch (LayoutException $e) {
            // Do nothing
        }

        try {
            $layout['left'] = new Zone();
            self::fail('Succeeded in setting a new zone to layout.');
        } catch (RuntimeException $e) {
            // Do nothing
        }

        try {
            unset($layout['left']);
            self::fail('Succeeded in unsetting a zone in layout.');
        } catch (RuntimeException $e) {
            // Do nothing
        }
    }
}
