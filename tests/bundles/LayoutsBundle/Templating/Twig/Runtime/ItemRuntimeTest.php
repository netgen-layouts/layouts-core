<?php

declare(strict_types=1);

namespace Netgen\Bundle\LayoutsBundle\Tests\Templating\Twig\Runtime;

use Netgen\Bundle\LayoutsBundle\Templating\Twig\Runtime\ItemRuntime;
use Netgen\Layouts\Exception\Item\ItemException;
use Netgen\Layouts\Item\CmsItem;
use Netgen\Layouts\Item\CmsItemLoaderInterface;
use Netgen\Layouts\Item\UrlGeneratorInterface;
use Netgen\Layouts\Tests\Stubs\ErrorHandler;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use stdClass;

final class ItemRuntimeTest extends TestCase
{
    private MockObject $cmsItemLoaderMock;

    private MockObject $urlGeneratorMock;

    private ErrorHandler $errorHandler;

    private ItemRuntime $runtime;

    protected function setUp(): void
    {
        $this->cmsItemLoaderMock = $this->createMock(CmsItemLoaderInterface::class);
        $this->urlGeneratorMock = $this->createMock(UrlGeneratorInterface::class);
        $this->errorHandler = new ErrorHandler();

        $this->runtime = new ItemRuntime(
            $this->cmsItemLoaderMock,
            $this->urlGeneratorMock,
            $this->errorHandler,
        );
    }

    /**
     * @covers \Netgen\Bundle\LayoutsBundle\Templating\Twig\Runtime\ItemRuntime::__construct
     * @covers \Netgen\Bundle\LayoutsBundle\Templating\Twig\Runtime\ItemRuntime::getItemPath
     */
    public function testGetItemPath(): void
    {
        $cmsItem = new CmsItem();

        $this->cmsItemLoaderMock
            ->expects(self::once())
            ->method('load')
            ->with(self::identicalTo(42), self::identicalTo('value'))
            ->willReturn($cmsItem);

        $this->urlGeneratorMock
            ->expects(self::once())
            ->method('generate')
            ->with(self::identicalTo($cmsItem), self::identicalTo('type'))
            ->willReturn('/item/path');

        $itemPath = $this->runtime->getItemPath([42, 'value'], 'type');

        self::assertSame('/item/path', $itemPath);
    }

    /**
     * @covers \Netgen\Bundle\LayoutsBundle\Templating\Twig\Runtime\ItemRuntime::getItemPath
     */
    public function testGetItemPathWithUri(): void
    {
        $cmsItem = new CmsItem();

        $this->cmsItemLoaderMock
            ->expects(self::once())
            ->method('load')
            ->with(self::identicalTo('42'), self::identicalTo('value'))
            ->willReturn($cmsItem);

        $this->urlGeneratorMock
            ->expects(self::once())
            ->method('generate')
            ->with(self::identicalTo($cmsItem), self::identicalTo('type'))
            ->willReturn('/item/path');

        $itemPath = $this->runtime->getItemPath('value://42', 'type');

        self::assertSame('/item/path', $itemPath);
    }

    /**
     * @covers \Netgen\Bundle\LayoutsBundle\Templating\Twig\Runtime\ItemRuntime::getItemPath
     */
    public function testGetItemPathWithItem(): void
    {
        $cmsItem = new CmsItem();

        $this->cmsItemLoaderMock
            ->expects(self::never())
            ->method('load');

        $this->urlGeneratorMock
            ->expects(self::once())
            ->method('generate')
            ->with(self::identicalTo($cmsItem), self::identicalTo('type'))
            ->willReturn('/item/path');

        $itemPath = $this->runtime->getItemPath($cmsItem, 'type');

        self::assertSame('/item/path', $itemPath);
    }

    /**
     * @covers \Netgen\Bundle\LayoutsBundle\Templating\Twig\Runtime\ItemRuntime::getItemPath
     */
    public function testGetItemPathWithDefaultType(): void
    {
        $cmsItem = new CmsItem();

        $this->cmsItemLoaderMock
            ->expects(self::never())
            ->method('load');

        $this->urlGeneratorMock
            ->expects(self::once())
            ->method('generate')
            ->with(self::identicalTo($cmsItem), self::identicalTo(UrlGeneratorInterface::TYPE_DEFAULT))
            ->willReturn('/item/path');

        $itemPath = $this->runtime->getItemPath($cmsItem);

        self::assertSame('/item/path', $itemPath);
    }

    /**
     * @covers \Netgen\Bundle\LayoutsBundle\Templating\Twig\Runtime\ItemRuntime::getItemPath
     */
    public function testGetItemPathWithInvalidValue(): void
    {
        $this->cmsItemLoaderMock
            ->expects(self::never())
            ->method('load');

        $this->urlGeneratorMock
            ->expects(self::never())
            ->method('generate');

        self::assertSame('', $this->runtime->getItemPath('value', 'type'));
    }

    /**
     * @covers \Netgen\Bundle\LayoutsBundle\Templating\Twig\Runtime\ItemRuntime::getItemPath
     */
    public function testGetItemPathWithUnsupportedValue(): void
    {
        $this->cmsItemLoaderMock
            ->expects(self::never())
            ->method('load');

        $this->urlGeneratorMock
            ->expects(self::never())
            ->method('generate');

        self::assertSame('', $this->runtime->getItemPath(42, 'type'));
    }

    /**
     * @covers \Netgen\Bundle\LayoutsBundle\Templating\Twig\Runtime\ItemRuntime::getItemPath
     */
    public function testGetItemPathWithInvalidValueThrowsItemExceptionInDebugMode(): void
    {
        $this->expectException(ItemException::class);
        $this->expectExceptionMessage('Item "value" is not valid.');

        $this->errorHandler->setThrow(true);

        $this->cmsItemLoaderMock
            ->expects(self::never())
            ->method('load');

        $this->urlGeneratorMock
            ->expects(self::never())
            ->method('generate');

        $this->runtime->getItemPath('value', 'type');
    }

    /**
     * @covers \Netgen\Bundle\LayoutsBundle\Templating\Twig\Runtime\ItemRuntime::getItemPath
     */
    public function testGetItemPathWithUnsupportedValueThrowsItemExceptionInDebugMode(): void
    {
        $this->expectException(ItemException::class);
        $this->expectExceptionMessage('Item could not be loaded.');

        $this->errorHandler->setThrow(true);

        $this->cmsItemLoaderMock
            ->expects(self::never())
            ->method('load');

        $this->urlGeneratorMock
            ->expects(self::never())
            ->method('generate');

        $this->runtime->getItemPath(new stdClass(), 'type');
    }
}
