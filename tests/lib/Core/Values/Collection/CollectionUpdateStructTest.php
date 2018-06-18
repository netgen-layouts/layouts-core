<?php

declare(strict_types=1);

namespace Netgen\BlockManager\Tests\Core\Values\Collection;

use Netgen\BlockManager\API\Values\Collection\CollectionUpdateStruct;
use PHPUnit\Framework\TestCase;

final class CollectionUpdateStructTest extends TestCase
{
    public function testSetProperties(): void
    {
        $collectionUpdateStruct = new CollectionUpdateStruct(
            [
                'offset' => 6,
                'limit' => 3,
            ]
        );

        $this->assertSame(6, $collectionUpdateStruct->offset);
        $this->assertSame(3, $collectionUpdateStruct->limit);
    }
}
