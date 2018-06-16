<?php

declare(strict_types=1);

namespace Netgen\Bundle\BlockManagerBundle\Tests\Templating\Twig\Runtime;

use Netgen\BlockManager\Item\Item;
use Netgen\BlockManager\Item\ItemLoaderInterface;
use Netgen\BlockManager\Item\UrlGeneratorInterface;
use Netgen\BlockManager\Tests\Stubs\ErrorHandler;
use Netgen\Bundle\BlockManagerBundle\Templating\Twig\Runtime\ItemRuntime;
use PHPUnit\Framework\TestCase;

final class ItemRuntimeTest extends TestCase
{
    /**
     * @var \Netgen\BlockManager\Item\ItemLoaderInterface&\PHPUnit\Framework\MockObject\MockObject
     */
    private $itemLoaderMock;

    /**
     * @var \Netgen\BlockManager\Item\UrlGeneratorInterface&\PHPUnit\Framework\MockObject\MockObject
     */
    private $urlGeneratorMock;

    /**
     * @var \Netgen\Bundle\BlockManagerBundle\Templating\Twig\Runtime\ItemRuntime
     */
    private $runtime;

    public function setUp(): void
    {
        $this->itemLoaderMock = $this->createMock(ItemLoaderInterface::class);
        $this->urlGeneratorMock = $this->createMock(UrlGeneratorInterface::class);

        $this->runtime = new ItemRuntime(
            $this->itemLoaderMock,
            $this->urlGeneratorMock,
            new ErrorHandler()
        );
    }

    /**
     * @covers \Netgen\Bundle\BlockManagerBundle\Templating\Twig\Runtime\ItemRuntime::__construct
     * @covers \Netgen\Bundle\BlockManagerBundle\Templating\Twig\Runtime\ItemRuntime::getItemPath
     */
    public function testGetItemPath(): void
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
    public function testGetItemPathWithUri(): void
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
    public function testGetItemPathWithItem(): void
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
    public function testGetItemPathWithUrlGeneratorReturningNull(): void
    {
        $this->itemLoaderMock
            ->expects($this->never())
            ->method('load');

        $this->urlGeneratorMock
            ->expects($this->once())
            ->method('generate')
            ->with($this->equalTo(new Item()))
            ->will($this->returnValue(null));

        $itemPath = $this->runtime->getItemPath(new Item());

        $this->assertEquals('', $itemPath);
    }

    /**
     * @covers \Netgen\Bundle\BlockManagerBundle\Templating\Twig\Runtime\ItemRuntime::getItemPath
     */
    public function testGetItemPathWithInvalidValue(): void
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
     */
    public function testGetItemPathWithUnsupportedValue(): void
    {
        $this->itemLoaderMock
            ->expects($this->never())
            ->method('load');

        $this->urlGeneratorMock
            ->expects($this->never())
            ->method('generate');

        $this->assertEquals('', $this->runtime->getItemPath(42));
    }
}
