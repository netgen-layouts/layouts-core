<?php

declare(strict_types=1);

namespace Netgen\Layouts\Tests\API\Values\Layout;

use DateTimeImmutable;
use Doctrine\Common\Collections\ArrayCollection;
use Netgen\Layouts\API\Values\Layout\Layout;
use Netgen\Layouts\API\Values\Layout\Zone;
use Netgen\Layouts\Exception\RuntimeException;
use Netgen\Layouts\Layout\Type\LayoutType;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\TestCase;
use Ramsey\Uuid\Uuid;

#[CoversClass(Layout::class)]
final class LayoutTest extends TestCase
{
    public function testSetProperties(): void
    {
        $createdDate = new DateTimeImmutable()->setTimestamp(123);
        $modifiedDate = new DateTimeImmutable()->setTimestamp(456);

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

        self::assertSame($zones, [...$layout]);

        self::assertCount(2, $layout);

        self::assertTrue(isset($layout['left']));
        self::assertSame($zones['left'], $layout['left']);
    }

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
