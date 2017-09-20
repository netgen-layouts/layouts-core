<?php

namespace Netgen\BlockManager\Tests\Collection\Result;

use Netgen\BlockManager\Collection\Result\CollectionIterator;
use Netgen\BlockManager\Collection\Result\CollectionIteratorFactory;
use Netgen\BlockManager\Collection\Result\ResultSet;
use Netgen\BlockManager\Core\Values\Collection\Collection;
use Netgen\BlockManager\Core\Values\Collection\Query;
use Netgen\BlockManager\Tests\Collection\Stubs\QueryType;
use PHPUnit\Framework\TestCase;

class CollectionIteratorFactoryTest extends TestCase
{
    /**
     * @var \Netgen\BlockManager\Collection\Result\CollectionIteratorFactory
     */
    private $factory;

    public function setUp()
    {
        $this->factory = new CollectionIteratorFactory(12);
    }

    /**
     * @covers \Netgen\BlockManager\Collection\Result\CollectionIteratorFactory::__construct
     * @covers \Netgen\BlockManager\Collection\Result\CollectionIteratorFactory::getCollectionIterator
     * @covers \Netgen\BlockManager\Collection\Result\CollectionIteratorFactory::getQueryIterator
     */
    public function testGetCollectionIterator()
    {
        $iterator = $this->factory->getCollectionIterator(
            new Collection(
                array(
                    'query' => new Query(
                        array(
                            'queryType' => new QueryType('type'),
                        )
                    ),
                )
            )
        );

        $this->assertInstanceOf(CollectionIterator::class, $iterator);
    }

    /**
     * @covers \Netgen\BlockManager\Collection\Result\CollectionIteratorFactory::getCollectionIterator
     * @covers \Netgen\BlockManager\Collection\Result\CollectionIteratorFactory::getQueryIterator
     */
    public function testGetCollectionIteratorWithContextualQuery()
    {
        $iterator = $this->factory->getCollectionIterator(
            new Collection(
                array(
                    'query' => new Query(
                        array(
                            'queryType' => new QueryType('type', array(), null, true),
                        )
                    ),
                )
            ),
            ResultSet::INCLUDE_UNKNOWN_ITEMS
        );

        $this->assertInstanceOf(CollectionIterator::class, $iterator);
    }

    /**
     * @covers \Netgen\BlockManager\Collection\Result\CollectionIteratorFactory::getCollectionIterator
     * @covers \Netgen\BlockManager\Collection\Result\CollectionIteratorFactory::getQueryIterator
     */
    public function testGetCollectionIteratorWithNoQuery()
    {
        $iterator = $this->factory->getCollectionIterator(
            new Collection(),
            ResultSet::INCLUDE_UNKNOWN_ITEMS
        );

        $this->assertInstanceOf(CollectionIterator::class, $iterator);
    }
}
