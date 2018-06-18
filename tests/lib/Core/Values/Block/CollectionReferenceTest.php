<?php

declare(strict_types=1);

namespace Netgen\BlockManager\Tests\Core\Values\Block;

use Netgen\BlockManager\Core\Values\Block\CollectionReference;
use Netgen\BlockManager\Core\Values\Collection\Collection;
use PHPUnit\Framework\TestCase;

final class CollectionReferenceTest extends TestCase
{
    /**
     * @covers \Netgen\BlockManager\Core\Values\Block\CollectionReference::__construct
     * @covers \Netgen\BlockManager\Core\Values\Block\CollectionReference::getCollection
     * @covers \Netgen\BlockManager\Core\Values\Block\CollectionReference::getIdentifier
     */
    public function testSetProperties(): void
    {
        $collection = new Collection();

        $collectionReference = new CollectionReference(
            [
                'collection' => $collection,
                'identifier' => 'default',
            ]
        );

        $this->assertSame($collection, $collectionReference->getCollection());
        $this->assertSame('default', $collectionReference->getIdentifier());
    }
}
