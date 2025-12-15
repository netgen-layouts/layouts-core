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
use PHPUnit\Framework\MockObject\Stub;
use PHPUnit\Framework\TestCase;

#[CoversClass(ItemRuntime::class)]
final class ItemRuntimeTest extends TestCase
{
    private Stub&CmsItemLoaderInterface $cmsItemLoaderStub;

    private Stub&UrlGeneratorInterface $urlGeneratorStub;

    private ErrorHandler $errorHandler;

    private ItemRuntime $runtime;

    protected function setUp(): void
    {
        $this->cmsItemLoaderStub = self::createStub(CmsItemLoaderInterface::class);
        $this->urlGeneratorStub = self::createStub(UrlGeneratorInterface::class);
        $this->errorHandler = new ErrorHandler();

        $this->runtime = new ItemRuntime(
            $this->cmsItemLoaderStub,
            $this->urlGeneratorStub,
            $this->errorHandler,
        );
    }

    public function testGetItemPath(): void
    {
        $cmsItem = new CmsItem();

        $this->cmsItemLoaderStub
            ->method('load')
            ->with(self::identicalTo(42), self::identicalTo('value'))
            ->willReturn($cmsItem);

        $this->urlGeneratorStub
            ->method('generate')
            ->with(self::identicalTo($cmsItem), self::identicalTo(UrlType::Default))
            ->willReturn('/item/path');

        $itemPath = $this->runtime->getItemPath([42, 'value']);

        self::assertSame('/item/path', $itemPath);
    }

    public function testGetItemPathWithUri(): void
    {
        $cmsItem = new CmsItem();

        $this->cmsItemLoaderStub
            ->method('load')
            ->with(self::identicalTo('42'), self::identicalTo('value'))
            ->willReturn($cmsItem);

        $this->urlGeneratorStub
            ->method('generate')
            ->with(self::identicalTo($cmsItem), self::identicalTo(UrlType::Default))
            ->willReturn('/item/path');

        $itemPath = $this->runtime->getItemPath('value://42');

        self::assertSame('/item/path', $itemPath);
    }

    public function testGetItemPathWithItem(): void
    {
        $cmsItem = new CmsItem();

        $this->urlGeneratorStub
            ->method('generate')
            ->with(self::identicalTo($cmsItem), self::identicalTo(UrlType::Default))
            ->willReturn('/item/path');

        $itemPath = $this->runtime->getItemPath($cmsItem);

        self::assertSame('/item/path', $itemPath);
    }

    public function testGetItemPathWithInvalidValue(): void
    {
        self::assertSame('', $this->runtime->getItemPath('value'));
    }

    public function testGetItemPathWithInvalidValueThrowsItemExceptionInDebugMode(): void
    {
        $this->expectException(ItemException::class);
        $this->expectExceptionMessage('Item "value" is not valid.');

        $this->errorHandler->setThrow(true);

        $this->runtime->getItemPath('value');
    }

    public function testGetItemAdminPath(): void
    {
        $cmsItem = new CmsItem();

        $this->cmsItemLoaderStub
            ->method('load')
            ->with(self::identicalTo(42), self::identicalTo('value'))
            ->willReturn($cmsItem);

        $this->urlGeneratorStub
            ->method('generate')
            ->with(self::identicalTo($cmsItem), self::identicalTo(UrlType::Admin))
            ->willReturn('/item/path');

        $itemPath = $this->runtime->getItemAdminPath([42, 'value']);

        self::assertSame('/item/path', $itemPath);
    }

    public function testGetItemAdminPathWithUri(): void
    {
        $cmsItem = new CmsItem();

        $this->cmsItemLoaderStub
            ->method('load')
            ->with(self::identicalTo('42'), self::identicalTo('value'))
            ->willReturn($cmsItem);

        $this->urlGeneratorStub
            ->method('generate')
            ->with(self::identicalTo($cmsItem), self::identicalTo(UrlType::Admin))
            ->willReturn('/item/path');

        $itemPath = $this->runtime->getItemAdminPath('value://42');

        self::assertSame('/item/path', $itemPath);
    }

    public function testGetItemAdminPathWithItem(): void
    {
        $cmsItem = new CmsItem();

        $this->urlGeneratorStub
            ->method('generate')
            ->with(self::identicalTo($cmsItem), self::identicalTo(UrlType::Admin))
            ->willReturn('/item/path');

        $itemPath = $this->runtime->getItemAdminPath($cmsItem);

        self::assertSame('/item/path', $itemPath);
    }

    public function testGetItemAdminPathWithInvalidValue(): void
    {
        self::assertSame('', $this->runtime->getItemAdminPath('value'));
    }

    public function testGetItemAdminPathWithInvalidValueThrowsItemExceptionInDebugMode(): void
    {
        $this->expectException(ItemException::class);
        $this->expectExceptionMessage('Item "value" is not valid.');

        $this->errorHandler->setThrow(true);

        $this->runtime->getItemAdminPath('value');
    }
}
