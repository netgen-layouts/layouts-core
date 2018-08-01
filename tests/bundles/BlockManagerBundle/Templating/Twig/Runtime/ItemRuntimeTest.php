<?php

declare(strict_types=1);

namespace Netgen\Bundle\BlockManagerBundle\Tests\Templating\Twig\Runtime;

use Netgen\BlockManager\Item\CmsItem;
use Netgen\BlockManager\Item\CmsItemLoaderInterface;
use Netgen\BlockManager\Item\UrlGeneratorInterface;
use Netgen\BlockManager\Tests\Stubs\ErrorHandler;
use Netgen\Bundle\BlockManagerBundle\Templating\Twig\Runtime\ItemRuntime;
use PHPUnit\Framework\TestCase;
use stdClass;

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
     * @var \Netgen\BlockManager\Tests\Stubs\ErrorHandler
     */
    private $errorHandler;

    /**
     * @var \Netgen\Bundle\BlockManagerBundle\Templating\Twig\Runtime\ItemRuntime
     */
    private $runtime;

    public function setUp(): void
    {
        $this->cmsItemLoaderMock = $this->createMock(CmsItemLoaderInterface::class);
        $this->urlGeneratorMock = $this->createMock(UrlGeneratorInterface::class);
        $this->errorHandler = new ErrorHandler();

        $this->runtime = new ItemRuntime(
            $this->cmsItemLoaderMock,
            $this->urlGeneratorMock,
            $this->errorHandler
        );
    }

    /**
     * @covers \Netgen\Bundle\BlockManagerBundle\Templating\Twig\Runtime\ItemRuntime::__construct
     * @covers \Netgen\Bundle\BlockManagerBundle\Templating\Twig\Runtime\ItemRuntime::getItemPath
     */
    public function testGetItemPath(): void
    {
        $cmsItem = new CmsItem();

        $this->cmsItemLoaderMock
            ->expects($this->once())
            ->method('load')
            ->with($this->identicalTo(42), $this->identicalTo('value'))
            ->will($this->returnValue($cmsItem));

        $this->urlGeneratorMock
            ->expects($this->once())
            ->method('generate')
            ->with($this->identicalTo($cmsItem))
            ->will($this->returnValue('/item/path'));

        $itemPath = $this->runtime->getItemPath(42, 'value');

        $this->assertSame('/item/path', $itemPath);
    }

    /**
     * @covers \Netgen\Bundle\BlockManagerBundle\Templating\Twig\Runtime\ItemRuntime::getItemPath
     */
    public function testGetItemPathWithUri(): void
    {
        $cmsItem = new CmsItem();

        $this->cmsItemLoaderMock
            ->expects($this->once())
            ->method('load')
            ->with($this->identicalTo('42'), $this->identicalTo('value'))
            ->will($this->returnValue($cmsItem));

        $this->urlGeneratorMock
            ->expects($this->once())
            ->method('generate')
            ->with($this->identicalTo($cmsItem))
            ->will($this->returnValue('/item/path'));

        $itemPath = $this->runtime->getItemPath('value://42');

        $this->assertSame('/item/path', $itemPath);
    }

    /**
     * @covers \Netgen\Bundle\BlockManagerBundle\Templating\Twig\Runtime\ItemRuntime::getItemPath
     */
    public function testGetItemPathWithItem(): void
    {
        $cmsItem = new CmsItem();

        $this->cmsItemLoaderMock
            ->expects($this->never())
            ->method('load');

        $this->urlGeneratorMock
            ->expects($this->once())
            ->method('generate')
            ->with($this->identicalTo($cmsItem))
            ->will($this->returnValue('/item/path'));

        $itemPath = $this->runtime->getItemPath($cmsItem);

        $this->assertSame('/item/path', $itemPath);
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

    /**
     * @covers \Netgen\Bundle\BlockManagerBundle\Templating\Twig\Runtime\ItemRuntime::getItemPath
     */
    public function testGetItemPathWithObjectAndValueType(): void
    {
        $this->cmsItemLoaderMock
            ->expects($this->never())
            ->method('load');

        $this->urlGeneratorMock
            ->expects($this->never())
            ->method('generate');

        $this->assertSame('', $this->runtime->getItemPath(new stdClass(), 'value'));
    }

    /**
     * @covers \Netgen\Bundle\BlockManagerBundle\Templating\Twig\Runtime\ItemRuntime::getItemPath
     * @expectedException \Netgen\BlockManager\Exception\Item\ItemException
     * @expectedExceptionMessage Item "value" is not valid.
     */
    public function testGetItemPathWithInvalidValueThrowsItemExceptionInDebugMode(): void
    {
        $this->errorHandler->setThrow(true);

        $this->cmsItemLoaderMock
            ->expects($this->never())
            ->method('load');

        $this->urlGeneratorMock
            ->expects($this->never())
            ->method('generate');

        $this->runtime->getItemPath('value');
    }

    /**
     * @covers \Netgen\Bundle\BlockManagerBundle\Templating\Twig\Runtime\ItemRuntime::getItemPath
     * @expectedException \Netgen\BlockManager\Exception\Item\ItemException
     * @expectedExceptionMessage Item could not be loaded.
     */
    public function testGetItemPathWithUnsupportedValueThrowsItemExceptionInDebugMode(): void
    {
        $this->errorHandler->setThrow(true);

        $this->cmsItemLoaderMock
            ->expects($this->never())
            ->method('load');

        $this->urlGeneratorMock
            ->expects($this->never())
            ->method('generate');

        $this->runtime->getItemPath(new stdClass());
    }
}
