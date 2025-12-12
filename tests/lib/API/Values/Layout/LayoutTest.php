<?php

declare(strict_types=1);

namespace Netgen\Layouts\Tests\API\Values\Layout;

use DateTimeImmutable;
use Netgen\Layouts\API\Values\Layout\Layout;
use Netgen\Layouts\API\Values\Layout\Zone;
use Netgen\Layouts\API\Values\Layout\ZoneList;
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

        $layoutType = LayoutType::fromArray(['identifier' => 'test_layout_1']);

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
                'isShared' => true,
                'zones' => ZoneList::fromArray($zones),
                'mainLocale' => 'en',
                'availableLocales' => ['en'],
            ],
        );

        self::assertSame($uuid, $layout->id);
        self::assertSame($layoutType, $layout->layoutType);
        self::assertSame('My layout', $layout->name);
        self::assertSame('My description', $layout->description);
        self::assertSame($createdDate, $layout->created);
        self::assertSame($modifiedDate, $layout->modified);
        self::assertTrue($layout->isShared);
        self::assertFalse($layout->hasZone('test'));
        self::assertSame($zones['right'], $layout->getZone('right'));
        self::assertTrue($layout->hasZone('right'));
        self::assertSame('en', $layout->mainLocale);
        self::assertSame(['en'], $layout->availableLocales);
        self::assertTrue($layout->hasLocale('en'));
        self::assertFalse($layout->hasLocale('hr'));

        self::assertCount(2, $layout->zones);
        self::assertSame($zones['left'], $layout->zones['left']);
        self::assertSame($zones['right'], $layout->zones['right']);

        self::assertSame($zones, [...$layout]);

        self::assertCount(2, $layout);

        self::assertArrayHasKey('left', $layout);
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
                'zones' => ZoneList::fromArray($zones),
            ],
        );

        $layout->offsetSet('left', new Zone());
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
                'zones' => ZoneList::fromArray($zones),
            ],
        );

        unset($layout['left']);
    }
}
