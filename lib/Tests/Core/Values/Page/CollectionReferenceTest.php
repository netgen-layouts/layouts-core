<?php

namespace Netgen\BlockManager\Tests\Core\Values\Page;

use Netgen\BlockManager\API\Values\Collection\Collection;
use Netgen\BlockManager\API\Values\Page\Layout;
use Netgen\BlockManager\Core\Values\Page\CollectionReference;

class CollectionReferenceTest extends \PHPUnit\Framework\TestCase
{
    /**
     * @covers \Netgen\BlockManager\Core\Values\Page\CollectionReference::__construct
     * @covers \Netgen\BlockManager\Core\Values\Page\CollectionReference::getBlockId
     * @covers \Netgen\BlockManager\Core\Values\Page\CollectionReference::getBlockStatus
     * @covers \Netgen\BlockManager\Core\Values\Page\CollectionReference::getCollectionId
     * @covers \Netgen\BlockManager\Core\Values\Page\CollectionReference::getCollectionStatus
     * @covers \Netgen\BlockManager\Core\Values\Page\CollectionReference::getIdentifier
     * @covers \Netgen\BlockManager\Core\Values\Page\CollectionReference::getOffset
     * @covers \Netgen\BlockManager\Core\Values\Page\CollectionReference::getLimit
     */
    public function testSetDefaultProperties()
    {
        $collectionReference = new CollectionReference();

        self::assertNull($collectionReference->getBlockId());
        self::assertNull($collectionReference->getBlockStatus());
        self::assertNull($collectionReference->getCollectionId());
        self::assertNull($collectionReference->getCollectionStatus());
        self::assertNull($collectionReference->getIdentifier());
        self::assertNull($collectionReference->getOffset());
        self::assertNull($collectionReference->getLimit());
    }

    /**
     * @covers \Netgen\BlockManager\Core\Values\Page\CollectionReference::__construct
     * @covers \Netgen\BlockManager\Core\Values\Page\CollectionReference::getBlockId
     * @covers \Netgen\BlockManager\Core\Values\Page\CollectionReference::getBlockStatus
     * @covers \Netgen\BlockManager\Core\Values\Page\CollectionReference::getCollectionId
     * @covers \Netgen\BlockManager\Core\Values\Page\CollectionReference::getCollectionStatus
     * @covers \Netgen\BlockManager\Core\Values\Page\CollectionReference::getIdentifier
     * @covers \Netgen\BlockManager\Core\Values\Page\CollectionReference::getOffset
     * @covers \Netgen\BlockManager\Core\Values\Page\CollectionReference::getLimit
     */
    public function testSetProperties()
    {
        $collectionReference = new CollectionReference(
            array(
                'blockId' => 42,
                'blockStatus' => Layout::STATUS_PUBLISHED,
                'collectionId' => 84,
                'collectionStatus' => Collection::STATUS_PUBLISHED,
                'identifier' => 'default',
                'offset' => 3,
                'limit' => 10,
            )
        );

        self::assertEquals(42, $collectionReference->getBlockId());
        self::assertEquals(Layout::STATUS_PUBLISHED, $collectionReference->getBlockStatus());
        self::assertEquals(84, $collectionReference->getCollectionId());
        self::assertEquals(Collection::STATUS_PUBLISHED, $collectionReference->getCollectionStatus());
        self::assertEquals('default', $collectionReference->getIdentifier());
        self::assertEquals(3, $collectionReference->getOffset());
        self::assertEquals(10, $collectionReference->getLimit());
    }
}
