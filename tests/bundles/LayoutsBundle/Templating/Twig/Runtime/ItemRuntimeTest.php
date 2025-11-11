<?php

declare(strict_types=1);

namespace Netgen\Bundle\LayoutsBundle\Tests\Templating\Twig\Runtime;

use Netgen\Bundle\LayoutsBundle\Templating\Twig\Runtime\ItemRuntime;
use Netgen\Layouts\Exception\Item\ItemException;
use Netgen\Layouts\Item\CmsItem;
use Netgen\Layouts\Item\CmsItemLoaderInterface;
use Netgen\Layouts\Item\UrlGeneratorInterface;
use Netgen\Layouts\Item\UrlType;
use Netgen\Layouts\Tests\Stubs\ErrorHandler;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;

#[CoversClass(ItemRuntime::class)]
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
            ->with(self::identicalTo($cmsItem), self::identicalTo(UrlType::Admin))
            ->willReturn('/item/path');

        $itemPath = $this->runtime->getItemPath([42, 'value'], UrlType::Admin->value);

        self::assertSame('/item/path', $itemPath);
    }

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
            ->with(self::identicalTo($cmsItem), self::identicalTo(UrlType::Admin))
            ->willReturn('/item/path');

        $itemPath = $this->runtime->getItemPath('value://42', UrlType::Admin->value);

        self::assertSame('/item/path', $itemPath);
    }

    public function testGetItemPathWithItem(): void
    {
        $cmsItem = new CmsItem();

        $this->cmsItemLoaderMock
            ->expects(self::never())
            ->method('load');

        $this->urlGeneratorMock
            ->expects(self::once())
            ->method('generate')
            ->with(self::identicalTo($cmsItem), self::identicalTo(UrlType::Admin))
            ->willReturn('/item/path');

        $itemPath = $this->runtime->getItemPath($cmsItem, UrlType::Admin->value);

        self::assertSame('/item/path', $itemPath);
    }

    public function testGetItemPathWithDefaultType(): void
    {
        $cmsItem = new CmsItem();

        $this->cmsItemLoaderMock
            ->expects(self::never())
            ->method('load');

        $this->urlGeneratorMock
            ->expects(self::once())
            ->method('generate')
            ->with(self::identicalTo($cmsItem), self::identicalTo(UrlType::Default))
            ->willReturn('/item/path');

        $itemPath = $this->runtime->getItemPath($cmsItem);

        self::assertSame('/item/path', $itemPath);
    }

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
}
