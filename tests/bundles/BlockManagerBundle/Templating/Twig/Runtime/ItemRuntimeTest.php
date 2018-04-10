<?php

namespace Netgen\Bundle\BlockManagerBundle\Tests\Templating\Twig\Runtime;

use Netgen\BlockManager\Item\Item;
use Netgen\BlockManager\Item\ItemLoaderInterface;
use Netgen\BlockManager\Item\UrlGeneratorInterface;
use Netgen\Bundle\BlockManagerBundle\Templating\Twig\Runtime\ItemRuntime;
use PHPUnit\Framework\TestCase;

final class ItemRuntimeTest extends TestCase
{
    /**
     * @var \PHPUnit\Framework\MockObject\MockObject
     */
    private $itemLoaderMock;

    /**
     * @var \PHPUnit\Framework\MockObject\MockObject
     */
    private $urlGeneratorMock;

    /**
     * @var \Netgen\Bundle\BlockManagerBundle\Templating\Twig\Runtime\ItemRuntime
     */
    private $runtime;

    public function setUp()
    {
        $this->itemLoaderMock = $this->createMock(ItemLoaderInterface::class);
        $this->urlGeneratorMock = $this->createMock(UrlGeneratorInterface::class);

        $this->runtime = new ItemRuntime(
            $this->itemLoaderMock,
            $this->urlGeneratorMock
        );
    }

    /**
     * @covers \Netgen\Bundle\BlockManagerBundle\Templating\Twig\Runtime\ItemRuntime::__construct
     * @covers \Netgen\Bundle\BlockManagerBundle\Templating\Twig\Runtime\ItemRuntime::getItemPath
     */
    public function testGetItemPath()
    {
        $this->itemLoaderMock
            ->expects($this->once())
            ->method('load')
            ->with($this->equalTo(42), $this->equalTo('value'))
            ->will($this->returnValue(new Item()));

        $this->urlGeneratorMock
            ->expects($this->once())
            ->method('generate')
            ->with($this->equalTo(new Item()))
            ->will($this->returnValue('/item/path'));

        $itemPath = $this->runtime->getItemPath(42, 'value');

        $this->assertEquals('/item/path', $itemPath);
    }

    /**
     * @covers \Netgen\Bundle\BlockManagerBundle\Templating\Twig\Runtime\ItemRuntime::getItemPath
     */
    public function testGetItemPathWithUri()
    {
        $this->itemLoaderMock
            ->expects($this->once())
            ->method('load')
            ->with($this->equalTo(42), $this->equalTo('value'))
            ->will($this->returnValue(new Item()));

        $this->urlGeneratorMock
            ->expects($this->once())
            ->method('generate')
            ->with($this->equalTo(new Item()))
            ->will($this->returnValue('/item/path'));

        $itemPath = $this->runtime->getItemPath('value://42');

        $this->assertEquals('/item/path', $itemPath);
    }

    /**
     * @covers \Netgen\Bundle\BlockManagerBundle\Templating\Twig\Runtime\ItemRuntime::getItemPath
     */
    public function testGetItemPathWithItem()
    {
        $this->itemLoaderMock
            ->expects($this->never())
            ->method('load');

        $this->urlGeneratorMock
            ->expects($this->once())
            ->method('generate')
            ->with($this->equalTo(new Item()))
            ->will($this->returnValue('/item/path'));

        $itemPath = $this->runtime->getItemPath(new Item());

        $this->assertEquals('/item/path', $itemPath);
    }

    /**
     * @covers \Netgen\Bundle\BlockManagerBundle\Templating\Twig\Runtime\ItemRuntime::getItemPath
     */
    public function testGetItemPathWithInvalidValue()
    {
        $this->itemLoaderMock
            ->expects($this->never())
            ->method('load');

        $this->urlGeneratorMock
            ->expects($this->never())
            ->method('generate');

        $this->assertEquals('', $this->runtime->getItemPath('value'));
    }

    /**
     * @covers \Netgen\Bundle\BlockManagerBundle\Templating\Twig\Runtime\ItemRuntime::getItemPath
     * @covers \Netgen\Bundle\BlockManagerBundle\Templating\Twig\Runtime\ItemRuntime::setDebug
     * @expectedException \Netgen\BlockManager\Exception\Item\ItemException
     * @expectedExceptionMessage Item "value" is not valid.
     */
    public function testGetItemPathThrowsItemExceptionInDebugModeWithInvalidValue()
    {
        $this->runtime->setDebug(true);

        $this->itemLoaderMock
            ->expects($this->never())
            ->method('load');

        $this->urlGeneratorMock
            ->expects($this->never())
            ->method('generate');

        $this->runtime->getItemPath('value');
    }

    /**
     * @covers \Netgen\Bundle\BlockManagerBundle\Templating\Twig\Runtime\ItemRuntime::getItemPath
     */
    public function testGetItemPathWithUnsupportedValue()
    {
        $this->itemLoaderMock
            ->expects($this->never())
            ->method('load');

        $this->urlGeneratorMock
            ->expects($this->never())
            ->method('generate');

        $this->assertEquals('', $this->runtime->getItemPath(42));
    }

    /**
     * @covers \Netgen\Bundle\BlockManagerBundle\Templating\Twig\Runtime\ItemRuntime::getItemPath
     * @covers \Netgen\Bundle\BlockManagerBundle\Templating\Twig\Runtime\ItemRuntime::setDebug
     * @expectedException \Netgen\BlockManager\Exception\Item\ItemException
     * @expectedExceptionMessage Item could not be loaded.
     */
    public function testGetItemPathThrowsItemExceptionInDebugModeWithUnsupportedValue()
    {
        $this->runtime->setDebug(true);

        $this->itemLoaderMock
            ->expects($this->never())
            ->method('load');

        $this->urlGeneratorMock
            ->expects($this->never())
            ->method('generate');

        $this->runtime->getItemPath(42);
    }
}
