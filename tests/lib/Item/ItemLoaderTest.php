<?php

declare(strict_types=1);

namespace Netgen\BlockManager\Tests\Item;

use Netgen\BlockManager\Item\Item;
use Netgen\BlockManager\Item\ItemBuilderInterface;
use Netgen\BlockManager\Item\ItemLoader;
use Netgen\BlockManager\Item\NullItem;
use Netgen\BlockManager\Tests\Item\Stubs\Value;
use Netgen\BlockManager\Tests\Item\Stubs\ValueLoader;
use PHPUnit\Framework\TestCase;

final class ItemLoaderTest extends TestCase
{
    /**
     * @var \Netgen\BlockManager\Item\ItemBuilderInterface&\PHPUnit\Framework\MockObject\MockObject
     */
    private $itemBuilderMock;

    /**
     * @var \Netgen\BlockManager\Item\ItemLoaderInterface
     */
    private $itemLoader;

    public function setUp(): void
    {
        $this->itemBuilderMock = $this->createMock(ItemBuilderInterface::class);
    }

    /**
     * @covers \Netgen\BlockManager\Item\ItemLoader::__construct
     * @covers \Netgen\BlockManager\Item\ItemLoader::load
     */
    public function testLoad(): void
    {
        $item = new Item(
            [
                'value' => 42,
                'remoteId' => 'abc',
                'name' => 'Some value',
                'valueType' => 'value',
                'isVisible' => true,
                'object' => new Value(42, 'abc'),
            ]
        );

        $this->itemLoader = new ItemLoader(
            $this->itemBuilderMock,
            ['value' => new ValueLoader()]
        );

        $this->itemBuilderMock
            ->expects($this->any())
            ->method('build')
            ->will($this->returnValue($item));

        $this->assertEquals($item, $this->itemLoader->load(42, 'value'));
    }

    /**
     * @covers \Netgen\BlockManager\Item\ItemLoader::load
     */
    public function testLoadItemWithNoItem(): void
    {
        $this->itemLoader = new ItemLoader(
            $this->itemBuilderMock,
            ['value' => new ValueLoader(true)]
        );

        $this->assertEquals(new NullItem('value'), $this->itemLoader->load(42, 'value'));
    }

    /**
     * @covers \Netgen\BlockManager\Item\ItemLoader::load
     * @expectedException \Netgen\BlockManager\Exception\Item\ItemException
     * @expectedExceptionMessage Value type "value" does not exist.
     */
    public function testLoadItemThrowsItemException(): void
    {
        $this->itemLoader = new ItemLoader($this->itemBuilderMock);

        $this->itemLoader->load(42, 'value');
    }

    /**
     * @covers \Netgen\BlockManager\Item\ItemLoader::loadByRemoteId
     */
    public function testLoadByRemoteId(): void
    {
        $item = new Item(
            [
                'value' => 42,
                'remoteId' => 'abc',
                'name' => 'Some value',
                'valueType' => 'value',
                'isVisible' => true,
                'object' => new Value(42, 'abc'),
            ]
        );

        $this->itemLoader = new ItemLoader(
            $this->itemBuilderMock,
            ['value' => new ValueLoader()]
        );

        $this->itemBuilderMock
            ->expects($this->any())
            ->method('build')
            ->will($this->returnValue($item));

        $this->assertEquals($item, $this->itemLoader->loadByRemoteId(42, 'value'));
    }

    /**
     * @covers \Netgen\BlockManager\Item\ItemLoader::loadByRemoteId
     */
    public function testLoadByRemoteIdItemThrowsItemExceptionWithNoItem(): void
    {
        $this->itemLoader = new ItemLoader(
            $this->itemBuilderMock,
            ['value' => new ValueLoader(true)]
        );

        $this->assertEquals(new NullItem('value'), $this->itemLoader->loadByRemoteId(42, 'value'));
    }

    /**
     * @covers \Netgen\BlockManager\Item\ItemLoader::loadByRemoteId
     * @expectedException \Netgen\BlockManager\Exception\Item\ItemException
     * @expectedExceptionMessage Value type "value" does not exist.
     */
    public function testLoadByRemoteIdItemThrowsItemException(): void
    {
        $this->itemLoader = new ItemLoader($this->itemBuilderMock);

        $this->itemLoader->loadByRemoteId(42, 'value');
    }
}
