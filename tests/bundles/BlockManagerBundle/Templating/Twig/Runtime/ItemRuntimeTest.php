<?php

declare(strict_types=1);

namespace Netgen\Bundle\BlockManagerBundle\Tests\Templating\Twig\Runtime;

use Netgen\BlockManager\Item\CmsItem;
use Netgen\BlockManager\Item\CmsItemLoaderInterface;
use Netgen\BlockManager\Item\UrlGeneratorInterface;
use Netgen\BlockManager\Tests\Stubs\ErrorHandler;
use Netgen\Bundle\BlockManagerBundle\Templating\Twig\Runtime\ItemRuntime;
use PHPUnit\Framework\TestCase;

final class ItemRuntimeTest extends TestCase
{
    /**
     * @var \Netgen\BlockManager\Item\CmsItemLoaderInterface&\PHPUnit\Framework\MockObject\MockObject
     */
    private $cmsItemLoaderMock;

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
        $this->cmsItemLoaderMock = $this->createMock(CmsItemLoaderInterface::class);
        $this->urlGeneratorMock = $this->createMock(UrlGeneratorInterface::class);

        $this->runtime = new ItemRuntime(
            $this->cmsItemLoaderMock,
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
        $this->cmsItemLoaderMock
            ->expects($this->once())
            ->method('load')
            ->with($this->equalTo(42), $this->equalTo('value'))
            ->will($this->returnValue(new CmsItem()));

        $this->urlGeneratorMock
            ->expects($this->once())
            ->method('generate')
            ->with($this->equalTo(new CmsItem()))
            ->will($this->returnValue('/item/path'));

        $itemPath = $this->runtime->getItemPath(42, 'value');

        $this->assertSame('/item/path', $itemPath);
    }

    /**
     * @covers \Netgen\Bundle\BlockManagerBundle\Templating\Twig\Runtime\ItemRuntime::getItemPath
     */
    public function testGetItemPathWithUri(): void
    {
        $this->cmsItemLoaderMock
            ->expects($this->once())
            ->method('load')
            ->with($this->equalTo(42), $this->equalTo('value'))
            ->will($this->returnValue(new CmsItem()));

        $this->urlGeneratorMock
            ->expects($this->once())
            ->method('generate')
            ->with($this->equalTo(new CmsItem()))
            ->will($this->returnValue('/item/path'));

        $itemPath = $this->runtime->getItemPath('value://42');

        $this->assertSame('/item/path', $itemPath);
    }

    /**
     * @covers \Netgen\Bundle\BlockManagerBundle\Templating\Twig\Runtime\ItemRuntime::getItemPath
     */
    public function testGetItemPathWithItem(): void
    {
        $this->cmsItemLoaderMock
            ->expects($this->never())
            ->method('load');

        $this->urlGeneratorMock
            ->expects($this->once())
            ->method('generate')
            ->with($this->equalTo(new CmsItem()))
            ->will($this->returnValue('/item/path'));

        $itemPath = $this->runtime->getItemPath(new CmsItem());

        $this->assertSame('/item/path', $itemPath);
    }

    /**
     * @covers \Netgen\Bundle\BlockManagerBundle\Templating\Twig\Runtime\ItemRuntime::getItemPath
     */
    public function testGetItemPathWithUrlGeneratorReturningNull(): void
    {
        $this->cmsItemLoaderMock
            ->expects($this->never())
            ->method('load');

        $this->urlGeneratorMock
            ->expects($this->once())
            ->method('generate')
            ->with($this->equalTo(new CmsItem()))
            ->will($this->returnValue(null));

        $itemPath = $this->runtime->getItemPath(new CmsItem());

        $this->assertSame('', $itemPath);
    }

    /**
     * @covers \Netgen\Bundle\BlockManagerBundle\Templating\Twig\Runtime\ItemRuntime::getItemPath
     */
    public function testGetItemPathWithInvalidValue(): void
    {
        $this->cmsItemLoaderMock
            ->expects($this->never())
            ->method('load');

        $this->urlGeneratorMock
            ->expects($this->never())
            ->method('generate');

        $this->assertSame('', $this->runtime->getItemPath('value'));
    }

    /**
     * @covers \Netgen\Bundle\BlockManagerBundle\Templating\Twig\Runtime\ItemRuntime::getItemPath
     */
    public function testGetItemPathWithUnsupportedValue(): void
    {
        $this->cmsItemLoaderMock
            ->expects($this->never())
            ->method('load');

        $this->urlGeneratorMock
            ->expects($this->never())
            ->method('generate');

        $this->assertSame('', $this->runtime->getItemPath(42));
    }
}
