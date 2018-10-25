<?php

declare(strict_types=1);

namespace Netgen\Bundle\BlockManagerBundle\Tests\Templating\Twig\Runtime;

use Netgen\BlockManager\Exception\Item\ItemException;
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
            ->expects(self::once())
            ->method('load')
            ->with(self::identicalTo(42), self::identicalTo('value'))
            ->will(self::returnValue($cmsItem));

        $this->urlGeneratorMock
            ->expects(self::once())
            ->method('generate')
            ->with(self::identicalTo($cmsItem))
            ->will(self::returnValue('/item/path'));

        $itemPath = $this->runtime->getItemPath(42, 'value');

        self::assertSame('/item/path', $itemPath);
    }

    /**
     * @covers \Netgen\Bundle\BlockManagerBundle\Templating\Twig\Runtime\ItemRuntime::getItemPath
     */
    public function testGetItemPathWithUri(): void
    {
        $cmsItem = new CmsItem();

        $this->cmsItemLoaderMock
            ->expects(self::once())
            ->method('load')
            ->with(self::identicalTo('42'), self::identicalTo('value'))
            ->will(self::returnValue($cmsItem));

        $this->urlGeneratorMock
            ->expects(self::once())
            ->method('generate')
            ->with(self::identicalTo($cmsItem))
            ->will(self::returnValue('/item/path'));

        $itemPath = $this->runtime->getItemPath('value://42');

        self::assertSame('/item/path', $itemPath);
    }

    /**
     * @covers \Netgen\Bundle\BlockManagerBundle\Templating\Twig\Runtime\ItemRuntime::getItemPath
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
            ->with(self::identicalTo($cmsItem))
            ->will(self::returnValue('/item/path'));

        $itemPath = $this->runtime->getItemPath($cmsItem);

        self::assertSame('/item/path', $itemPath);
    }

    /**
     * @covers \Netgen\Bundle\BlockManagerBundle\Templating\Twig\Runtime\ItemRuntime::getItemPath
     */
    public function testGetItemPathWithInvalidValue(): void
    {
        $this->cmsItemLoaderMock
            ->expects(self::never())
            ->method('load');

        $this->urlGeneratorMock
            ->expects(self::never())
            ->method('generate');

        self::assertSame('', $this->runtime->getItemPath('value'));
    }

    /**
     * @covers \Netgen\Bundle\BlockManagerBundle\Templating\Twig\Runtime\ItemRuntime::getItemPath
     */
    public function testGetItemPathWithUnsupportedValue(): void
    {
        $this->cmsItemLoaderMock
            ->expects(self::never())
            ->method('load');

        $this->urlGeneratorMock
            ->expects(self::never())
            ->method('generate');

        self::assertSame('', $this->runtime->getItemPath(42));
    }

    /**
     * @covers \Netgen\Bundle\BlockManagerBundle\Templating\Twig\Runtime\ItemRuntime::getItemPath
     */
    public function testGetItemPathWithObjectAndValueType(): void
    {
        $this->cmsItemLoaderMock
            ->expects(self::never())
            ->method('load');

        $this->urlGeneratorMock
            ->expects(self::never())
            ->method('generate');

        self::assertSame('', $this->runtime->getItemPath(new stdClass(), 'value'));
    }

    /**
     * @covers \Netgen\Bundle\BlockManagerBundle\Templating\Twig\Runtime\ItemRuntime::getItemPath
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

        $this->runtime->getItemPath('value');
    }

    /**
     * @covers \Netgen\Bundle\BlockManagerBundle\Templating\Twig\Runtime\ItemRuntime::getItemPath
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

        $this->runtime->getItemPath(new stdClass());
    }
}
