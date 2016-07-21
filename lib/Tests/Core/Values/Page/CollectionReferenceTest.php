<?php

namespace Netgen\BlockManager\Tests\Core\Values\Page;

use Netgen\BlockManager\Core\Values\Collection\Collection;
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

        $this->assertNull($collectionReference->getBlock());
        $this->assertNull($collectionReference->getCollection());
        $this->assertNull($collectionReference->getIdentifier());
        $this->assertNull($collectionReference->getOffset());
        $this->assertNull($collectionReference->getLimit());
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

        $this->assertEquals(new Block(), $collectionReference->getBlock());
        $this->assertEquals(new Collection(), $collectionReference->getCollection());
        $this->assertEquals('default', $collectionReference->getIdentifier());
        $this->assertEquals(3, $collectionReference->getOffset());
        $this->assertEquals(10, $collectionReference->getLimit());
    }
}
