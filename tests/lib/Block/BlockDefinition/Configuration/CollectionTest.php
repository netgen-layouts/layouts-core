<?php

namespace Netgen\BlockManager\Tests\Block\BlockDefinition\Configuration;

use Netgen\BlockManager\Block\BlockDefinition\Configuration\Collection;
use PHPUnit\Framework\TestCase;

class CollectionTest extends TestCase
{
    /**
     * @var \Netgen\BlockManager\Block\BlockDefinition\Configuration\Collection
     */
    protected $collection;

    public function setUp()
    {
        $this->collection = new Collection(
            array(
                'identifier' => 'collection',
                'validItemTypes' => array('item'),
                'validQueryTypes' => array('query'),
            )
        );
    }

    /**
     * @covers \Netgen\BlockManager\Block\BlockDefinition\Configuration\Collection::getIdentifier
     */
    public function testGetIdentifier()
    {
        $this->assertEquals('collection', $this->collection->getIdentifier());
    }

    /**
     * @covers \Netgen\BlockManager\Block\BlockDefinition\Configuration\Collection::getValidQueryTypes
     */
    public function testGetValidQueryTypes()
    {
        $this->assertEquals(array('query'), $this->collection->getValidQueryTypes());
    }

    /**
     * @covers \Netgen\BlockManager\Block\BlockDefinition\Configuration\Collection::isValidQueryType
     */
    public function testIsValidQueryType()
    {
        $this->assertTrue($this->collection->isValidQueryType('query'));
        $this->assertFalse($this->collection->isValidQueryType('query2'));
    }

    /**
     * @covers \Netgen\BlockManager\Block\BlockDefinition\Configuration\Collection::isValidQueryType
     */
    public function testIsValidQueryTypeWithAllValidTypes()
    {
        $this->collection = new Collection(
            array(
                'validQueryTypes' => null,
            )
        );

        $this->assertTrue($this->collection->isValidQueryType('query'));
        $this->assertTrue($this->collection->isValidQueryType('query2'));
    }

    /**
     * @covers \Netgen\BlockManager\Block\BlockDefinition\Configuration\Collection::isValidQueryType
     */
    public function testIsValidQueryTypeWithNoValidTypes()
    {
        $this->collection = new Collection(
            array(
                'validQueryTypes' => array(),
            )
        );

        $this->assertFalse($this->collection->isValidQueryType('query'));
        $this->assertFalse($this->collection->isValidQueryType('query2'));
    }

    /**
     * @covers \Netgen\BlockManager\Block\BlockDefinition\Configuration\Collection::getValidItemTypes
     */
    public function testGetValidItemTypes()
    {
        $this->assertEquals(array('item'), $this->collection->getValidItemTypes());
    }

    /**
     * @covers \Netgen\BlockManager\Block\BlockDefinition\Configuration\Collection::isValidItemType
     */
    public function testIsValidItemType()
    {
        $this->assertTrue($this->collection->isValidItemType('item'));
        $this->assertFalse($this->collection->isValidItemType('item2'));
    }

    /**
     * @covers \Netgen\BlockManager\Block\BlockDefinition\Configuration\Collection::isValidItemType
     */
    public function testIsValidItemTypeWithAllValidTypes()
    {
        $this->collection = new Collection(
            array(
                'validItemTypes' => null,
            )
        );

        $this->assertTrue($this->collection->isValidItemType('item'));
        $this->assertTrue($this->collection->isValidItemType('item2'));
    }

    /**
     * @covers \Netgen\BlockManager\Block\BlockDefinition\Configuration\Collection::isValidItemType
     */
    public function testIsValidItemTypeWithNoValidTypes()
    {
        $this->collection = new Collection(
            array(
                'validItemTypes' => array(),
            )
        );

        $this->assertFalse($this->collection->isValidItemType('item'));
        $this->assertFalse($this->collection->isValidItemType('item2'));
    }
}
