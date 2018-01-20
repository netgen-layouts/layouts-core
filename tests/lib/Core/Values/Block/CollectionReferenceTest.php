<?php

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
    public function testSetDefaultProperties()
    {
        $collectionReference = new CollectionReference();

        $this->assertNull($collectionReference->getCollection());
        $this->assertNull($collectionReference->getIdentifier());
    }

    /**
     * @covers \Netgen\BlockManager\Core\Values\Block\CollectionReference::__construct
     * @covers \Netgen\BlockManager\Core\Values\Block\CollectionReference::getCollection
     * @covers \Netgen\BlockManager\Core\Values\Block\CollectionReference::getIdentifier
     */
    public function testSetProperties()
    {
        $collectionReference = new CollectionReference(
            array(
                'collection' => new Collection(),
                'identifier' => 'default',
            )
        );

        $this->assertEquals(new Collection(), $collectionReference->getCollection());
        $this->assertEquals('default', $collectionReference->getIdentifier());
    }
}
