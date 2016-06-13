<?php

namespace Netgen\BlockManager\Tests\Core\Values\Page;

use Netgen\BlockManager\Core\Values\Collection\Collection;
use Netgen\BlockManager\API\Values\Page\Layout;
use Netgen\BlockManager\Core\Values\Page\Block;
use Netgen\BlockManager\Core\Values\Page\CollectionReference;
use PHPUnit\Framework\TestCase;

class CollectionReferenceTest extends TestCase
{
    /**
     * @covers \Netgen\BlockManager\Core\Values\Page\CollectionReference::__construct
     * @covers \Netgen\BlockManager\Core\Values\Page\CollectionReference::getBlock
     * @covers \Netgen\BlockManager\Core\Values\Page\CollectionReference::getCollection
     * @covers \Netgen\BlockManager\Core\Values\Page\CollectionReference::getIdentifier
     * @covers \Netgen\BlockManager\Core\Values\Page\CollectionReference::getOffset
     * @covers \Netgen\BlockManager\Core\Values\Page\CollectionReference::getLimit
     */
    public function testSetDefaultProperties()
    {
        $collectionReference = new CollectionReference();

        self::assertNull($collectionReference->getBlock());
        self::assertNull($collectionReference->getCollection());
        self::assertNull($collectionReference->getIdentifier());
        self::assertNull($collectionReference->getOffset());
        self::assertNull($collectionReference->getLimit());
    }

    /**
     * @covers \Netgen\BlockManager\Core\Values\Page\CollectionReference::__construct
     * @covers \Netgen\BlockManager\Core\Values\Page\CollectionReference::getBlock
     * @covers \Netgen\BlockManager\Core\Values\Page\CollectionReference::getCollection
     * @covers \Netgen\BlockManager\Core\Values\Page\CollectionReference::getIdentifier
     * @covers \Netgen\BlockManager\Core\Values\Page\CollectionReference::getOffset
     * @covers \Netgen\BlockManager\Core\Values\Page\CollectionReference::getLimit
     */
    public function testSetProperties()
    {
        $collectionReference = new CollectionReference(
            array(
                'block' => new Block(),
                'collection' => new Collection(),
                'identifier' => 'default',
                'offset' => 3,
                'limit' => 10,
            )
        );

        self::assertEquals(new Block(), $collectionReference->getBlock());
        self::assertEquals(new Collection(), $collectionReference->getCollection());
        self::assertEquals('default', $collectionReference->getIdentifier());
        self::assertEquals(3, $collectionReference->getOffset());
        self::assertEquals(10, $collectionReference->getLimit());
    }
}
