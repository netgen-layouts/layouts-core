<?php

declare(strict_types=1);

namespace Netgen\Layouts\Tests\Persistence\Values\Collection;

use Netgen\Layouts\Persistence\Values\Collection\Collection;
use Netgen\Layouts\Persistence\Values\Value;
use PHPUnit\Framework\TestCase;

final class CollectionTest extends TestCase
{
    /**
     * @coversNothing
     */
    public function testSetProperties(): void
    {
        $collection = Collection::fromArray(
            [
                'id' => 42,
                'uuid' => 'f06f245a-f951-52c8-bfa3-84c80154eadc',
                'blockId' => 24,
                'blockUuid' => '4adf0f00-f6c2-5297-9f96-039bfabe8d3b',
                'status' => Value::STATUS_PUBLISHED,
                'offset' => 10,
                'limit' => 20,
                'isTranslatable' => true,
                'alwaysAvailable' => true,
                'mainLocale' => 'en',
                'availableLocales' => ['en'],
            ],
        );

        self::assertSame(42, $collection->id);
        self::assertSame('f06f245a-f951-52c8-bfa3-84c80154eadc', $collection->uuid);
        self::assertSame(24, $collection->blockId);
        self::assertSame('4adf0f00-f6c2-5297-9f96-039bfabe8d3b', $collection->blockUuid);
        self::assertSame(Value::STATUS_PUBLISHED, $collection->status);
        self::assertSame(10, $collection->offset);
        self::assertSame(20, $collection->limit);
        self::assertSame('en', $collection->mainLocale);
        self::assertTrue($collection->isTranslatable);
        self::assertTrue($collection->alwaysAvailable);
        self::assertSame(['en'], $collection->availableLocales);
    }
}
