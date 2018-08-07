<?php

declare(strict_types=1);

namespace Netgen\BlockManager\Tests\Persistence\Values\Collection;

use Netgen\BlockManager\Persistence\Values\Collection\Collection;
use Netgen\BlockManager\Persistence\Values\Value;
use PHPUnit\Framework\TestCase;

final class CollectionTest extends TestCase
{
    public function testSetProperties(): void
    {
        $collection = Collection::fromArray(
            [
                'id' => 42,
                'status' => Value::STATUS_PUBLISHED,
            ]
        );

        self::assertSame(42, $collection->id);
        self::assertSame(Value::STATUS_PUBLISHED, $collection->status);
    }
}
