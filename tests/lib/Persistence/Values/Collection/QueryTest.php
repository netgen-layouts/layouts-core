<?php

declare(strict_types=1);

namespace Netgen\BlockManager\Tests\Persistence\Values\Collection;

use Netgen\BlockManager\Persistence\Values\Collection\Query;
use Netgen\BlockManager\Persistence\Values\Value;
use PHPUnit\Framework\TestCase;

final class QueryTest extends TestCase
{
    public function testSetProperties(): void
    {
        $query = Query::fromArray(
            [
                'id' => 42,
                'collectionId' => 30,
                'type' => 'my_query_type',
                'parameters' => ['param' => ['value']],
                'status' => Value::STATUS_PUBLISHED,
            ]
        );

        self::assertSame(42, $query->id);
        self::assertSame(30, $query->collectionId);
        self::assertSame('my_query_type', $query->type);
        self::assertSame(['param' => ['value']], $query->parameters);
        self::assertSame(Value::STATUS_PUBLISHED, $query->status);
    }
}
