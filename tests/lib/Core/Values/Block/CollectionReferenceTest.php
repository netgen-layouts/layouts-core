<?php

namespace Netgen\BlockManager\Tests\Core\Values\Block;

use Netgen\BlockManager\Core\Values\Block\Block;
use Netgen\BlockManager\Core\Values\Block\CollectionReference;
use Netgen\BlockManager\Core\Values\Collection\Collection;
use PHPUnit\Framework\TestCase;

class CollectionReferenceTest extends TestCase
{
    /**
     * @covers \Netgen\BlockManager\Core\Values\Block\CollectionReference::__construct
     * @covers \Netgen\BlockManager\Core\Values\Block\CollectionReference::getBlock
     * @covers \Netgen\BlockManager\Core\Values\Block\CollectionReference::getCollection
     * @covers \Netgen\BlockManager\Core\Values\Block\CollectionReference::getIdentifier
     * @covers \Netgen\BlockManager\Core\Values\Block\CollectionReference::getOffset
     * @covers \Netgen\BlockManager\Core\Values\Block\CollectionReference::getLimit
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
     * @covers \Netgen\BlockManager\Core\Values\Block\CollectionReference::__construct
     * @covers \Netgen\BlockManager\Core\Values\Block\CollectionReference::getBlock
     * @covers \Netgen\BlockManager\Core\Values\Block\CollectionReference::getCollection
     * @covers \Netgen\BlockManager\Core\Values\Block\CollectionReference::getIdentifier
     * @covers \Netgen\BlockManager\Core\Values\Block\CollectionReference::getOffset
     * @covers \Netgen\BlockManager\Core\Values\Block\CollectionReference::getLimit
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
