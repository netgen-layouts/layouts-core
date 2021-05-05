<?php

declare(strict_types=1);

namespace Netgen\Layouts\Tests\Persistence\Values\Collection;

use Netgen\Layouts\Persistence\Values\Collection\Query;
use Netgen\Layouts\Persistence\Values\Value;
use PHPUnit\Framework\TestCase;

final class QueryTest extends TestCase
{
    /**
     * @coversNothing
     */
    public function testSetProperties(): void
    {
        $query = Query::fromArray(
            [
                'id' => 42,
                'uuid' => '4adf0f00-f6c2-5297-9f96-039bfabe8d3b',
                'collectionId' => 30,
                'collectionUuid' => 'f06f245a-f951-52c8-bfa3-84c80154eadc',
                'type' => 'my_query_type',
                'parameters' => ['en' => ['param' => 'value']],
                'status' => Value::STATUS_PUBLISHED,
                'isTranslatable' => true,
                'alwaysAvailable' => true,
                'mainLocale' => 'en',
                'availableLocales' => ['en'],
            ],
        );

        self::assertSame(42, $query->id);
        self::assertSame('4adf0f00-f6c2-5297-9f96-039bfabe8d3b', $query->uuid);
        self::assertSame(30, $query->collectionId);
        self::assertSame('f06f245a-f951-52c8-bfa3-84c80154eadc', $query->collectionUuid);
        self::assertSame('my_query_type', $query->type);
        self::assertSame(['en' => ['param' => 'value']], $query->parameters);
        self::assertSame(Value::STATUS_PUBLISHED, $query->status);
        self::assertSame('en', $query->mainLocale);
        self::assertTrue($query->isTranslatable);
        self::assertTrue($query->alwaysAvailable);
        self::assertSame(['en'], $query->availableLocales);
    }
}
