<?php

declare(strict_types=1);

namespace Netgen\Layouts\Tests\API\Values\Layout;

use DateTimeImmutable;
use Doctrine\Common\Collections\ArrayCollection;
use Netgen\Layouts\API\Values\Layout\Layout;
use Netgen\Layouts\API\Values\Layout\Zone;
use Netgen\Layouts\Exception\RuntimeException;
use Netgen\Layouts\Layout\Type\LayoutType;
use PHPUnit\Framework\TestCase;
use Ramsey\Uuid\Uuid;

use function iterator_to_array;

final class LayoutTest extends TestCase
{
    /**
     * @covers \Netgen\Layouts\API\Values\Layout\Layout::count
     * @covers \Netgen\Layouts\API\Values\Layout\Layout::getAvailableLocales
     * @covers \Netgen\Layouts\API\Values\Layout\Layout::getCreated
     * @covers \Netgen\Layouts\API\Values\Layout\Layout::getDescription
     * @covers \Netgen\Layouts\API\Values\Layout\Layout::getId
     * @covers \Netgen\Layouts\API\Values\Layout\Layout::getIterator
     * @covers \Netgen\Layouts\API\Values\Layout\Layout::getLayoutType
     * @covers \Netgen\Layouts\API\Values\Layout\Layout::getMainLocale
     * @covers \Netgen\Layouts\API\Values\Layout\Layout::getModified
     * @covers \Netgen\Layouts\API\Values\Layout\Layout::getName
     * @covers \Netgen\Layouts\API\Values\Layout\Layout::getZone
     * @covers \Netgen\Layouts\API\Values\Layout\Layout::getZones
     * @covers \Netgen\Layouts\API\Values\Layout\Layout::hasLocale
     * @covers \Netgen\Layouts\API\Values\Layout\Layout::hasZone
     * @covers \Netgen\Layouts\API\Values\Layout\Layout::isShared
     * @covers \Netgen\Layouts\API\Values\Layout\Layout::offsetExists
     * @covers \Netgen\Layouts\API\Values\Layout\Layout::offsetGet
     */
    public function testSetProperties(): void
    {
        $createdDate = (new DateTimeImmutable())->setTimestamp(123);
        $modifiedDate = (new DateTimeImmutable())->setTimestamp(456);

        $layoutType = LayoutType::fromArray(['identifier' => '4_zones_a']);

        $zones = [
            'left' => Zone::fromArray(['identifier' => 'left']),
            'right' => Zone::fromArray(['identifier' => 'right', 'linkedZone' => new Zone()]),
        ];

        $uuid = Uuid::uuid4();

        $layout = Layout::fromArray(
            [
                'id' => $uuid,
                'layoutType' => $layoutType,
                'name' => 'My layout',
                'description' => 'My description',
                'created' => $createdDate,
                'modified' => $modifiedDate,
                'shared' => true,
                'zones' => new ArrayCollection($zones),
                'mainLocale' => 'en',
                'availableLocales' => ['en'],
            ],
        );

        self::assertSame($uuid, $layout->getId());
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

        self::assertSame($zones, iterator_to_array($layout));

        self::assertCount(2, $layout);

        self::assertTrue(isset($layout['left']));
        self::assertSame($zones['left'], $layout['left']);
    }

    /**
     * @covers \Netgen\Layouts\API\Values\Layout\Layout::offsetSet
     */
    public function testSet(): void
    {
        $this->expectException(RuntimeException::class);
        $this->expectExceptionMessage('Method call not supported.');

        $zones = [
            'left' => Zone::fromArray(['identifier' => 'left']),
            'right' => Zone::fromArray(['identifier' => 'right', 'linkedZone' => new Zone()]),
        ];

        $layout = Layout::fromArray(
            [
                'zones' => new ArrayCollection($zones),
            ],
        );

        $layout['left'] = new Zone();
    }

    /**
     * @covers \Netgen\Layouts\API\Values\Layout\Layout::offsetUnset
     */
    public function testUnset(): void
    {
        $this->expectException(RuntimeException::class);
        $this->expectExceptionMessage('Method call not supported.');

        $zones = [
            'left' => Zone::fromArray(['identifier' => 'left']),
            'right' => Zone::fromArray(['identifier' => 'right', 'linkedZone' => new Zone()]),
        ];

        $layout = Layout::fromArray(
            [
                'zones' => new ArrayCollection($zones),
            ],
        );

        unset($layout['left']);
    }
}
