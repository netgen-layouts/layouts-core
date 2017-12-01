<?php

namespace Netgen\BlockManager\Tests\Collection\Result;

use ArrayIterator;
use Iterator;
use Netgen\BlockManager\Collection\Result\CollectionIterator;
use Netgen\BlockManager\Collection\Result\ResultIteratorFactory;
use Netgen\BlockManager\Core\Values\Collection\Collection;
use Netgen\BlockManager\Item\ItemBuilderInterface;
use Netgen\BlockManager\Item\ItemLoaderInterface;
use PHPUnit\Framework\TestCase;

class ResultIteratorFactoryTest extends TestCase
{
    /**
     * @var \PHPUnit\Framework\MockObject\MockObject
     */
    private $itemLoaderMock;

    /**
     * @var \PHPUnit\Framework\MockObject\MockObject
     */
    private $itemBuilderMock;

    public function setUp()
    {
        $this->itemLoaderMock = $this->createMock(ItemLoaderInterface::class);
        $this->itemBuilderMock = $this->createMock(ItemBuilderInterface::class);
    }

    /**
     * @covers \Netgen\BlockManager\Collection\Result\ResultIteratorFactory::__construct
     * @covers \Netgen\BlockManager\Collection\Result\ResultIteratorFactory::getResultIterator
     */
    public function testGetResultIterator()
    {
        $collectionIterator = new CollectionIterator(new Collection(), new ArrayIterator(), 0, 200);
        $factory = new ResultIteratorFactory(
            $this->itemLoaderMock,
            $this->itemBuilderMock
        );

        $this->assertInstanceOf(
            Iterator::class,
            $factory->getResultIterator($collectionIterator)
        );
    }
}
