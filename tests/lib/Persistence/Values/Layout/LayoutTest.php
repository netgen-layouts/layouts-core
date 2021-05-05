<?php

declare(strict_types=1);

namespace Netgen\Layouts\Tests\Persistence\Values\Layout;

use Netgen\Layouts\Persistence\Values\Layout\Layout;
use Netgen\Layouts\Persistence\Values\Value;
use PHPUnit\Framework\TestCase;

final class LayoutTest extends TestCase
{
    /**
     * @coversNothing
     */
    public function testSetProperties(): void
    {
        $layout = Layout::fromArray(
            [
                'id' => 42,
                'uuid' => 'f06f245a-f951-52c8-bfa3-84c80154eadc',
                'type' => '4_zones_a',
                'name' => 'My layout',
                'description' => 'My description',
                'shared' => true,
                'created' => 123,
                'modified' => 456,
                'status' => Value::STATUS_PUBLISHED,
            ],
        );

        self::assertSame(42, $layout->id);
        self::assertSame('f06f245a-f951-52c8-bfa3-84c80154eadc', $layout->uuid);
        self::assertSame('4_zones_a', $layout->type);
        self::assertSame('My layout', $layout->name);
        self::assertSame('My description', $layout->description);
        self::assertTrue($layout->shared);
        self::assertSame(123, $layout->created);
        self::assertSame(456, $layout->modified);
        self::assertSame(Value::STATUS_PUBLISHED, $layout->status);
    }
}
