<?php

declare(strict_types=1);

namespace Netgen\Layouts\Tests\Item;

use Netgen\Layouts\Exception\Item\ItemException;
use Netgen\Layouts\Item\CmsItem;
use Netgen\Layouts\Item\CmsItemBuilderInterface;
use Netgen\Layouts\Item\CmsItemLoader;
use Netgen\Layouts\Item\NullCmsItem;
use Netgen\Layouts\Tests\Item\Stubs\Value;
use Netgen\Layouts\Tests\Item\Stubs\ValueLoader;
use PHPUnit\Framework\TestCase;

final class CmsItemLoaderTest extends TestCase
{
    /**
     * @var \Netgen\Layouts\Item\CmsItemBuilderInterface&\PHPUnit\Framework\MockObject\MockObject
     */
    private $cmsItemBuilderMock;

    /**
     * @var \Netgen\Layouts\Item\CmsItemLoaderInterface
     */
    private $cmsItemLoader;

    public function setUp(): void
    {
        $this->cmsItemBuilderMock = $this->createMock(CmsItemBuilderInterface::class);
    }

    /**
     * @covers \Netgen\Layouts\Item\CmsItemLoader::__construct
     * @covers \Netgen\Layouts\Item\CmsItemLoader::load
     */
    public function testLoad(): void
    {
        $item = CmsItem::fromArray(
            [
                'value' => 42,
                'remoteId' => 'abc',
                'name' => 'Some value',
                'valueType' => 'value',
                'isVisible' => true,
                'object' => new Value(42, 'abc'),
            ]
        );

        $this->cmsItemLoader = new CmsItemLoader(
            $this->cmsItemBuilderMock,
            ['value' => new ValueLoader(true)]
        );

        $this->cmsItemBuilderMock
            ->expects(self::any())
            ->method('build')
            ->willReturn($item);

        self::assertSame($item, $this->cmsItemLoader->load(42, 'value'));
    }

    /**
     * @covers \Netgen\Layouts\Item\CmsItemLoader::load
     */
    public function testLoadItemWithNoItem(): void
    {
        $this->cmsItemLoader = new CmsItemLoader(
            $this->cmsItemBuilderMock,
            ['value' => new ValueLoader(false)]
        );

        $loadedValue = $this->cmsItemLoader->load(42, 'value');

        self::assertInstanceOf(NullCmsItem::class, $loadedValue);
        self::assertSame('value', $loadedValue->getValueType());
    }

    /**
     * @covers \Netgen\Layouts\Item\CmsItemLoader::load
     */
    public function testLoadItemThrowsItemException(): void
    {
        $this->expectException(ItemException::class);
        $this->expectExceptionMessage('Value type "value" does not exist.');

        $this->cmsItemLoader = new CmsItemLoader($this->cmsItemBuilderMock, []);

        $this->cmsItemLoader->load(42, 'value');
    }

    /**
     * @covers \Netgen\Layouts\Item\CmsItemLoader::loadByRemoteId
     */
    public function testLoadByRemoteId(): void
    {
        $item = CmsItem::fromArray(
            [
                'value' => 42,
                'remoteId' => 'abc',
                'name' => 'Some value',
                'valueType' => 'value',
                'isVisible' => true,
                'object' => new Value(42, 'abc'),
            ]
        );

        $this->cmsItemLoader = new CmsItemLoader(
            $this->cmsItemBuilderMock,
            ['value' => new ValueLoader(true)]
        );

        $this->cmsItemBuilderMock
            ->expects(self::any())
            ->method('build')
            ->willReturn($item);

        self::assertSame($item, $this->cmsItemLoader->loadByRemoteId(42, 'value'));
    }

    /**
     * @covers \Netgen\Layouts\Item\CmsItemLoader::loadByRemoteId
     */
    public function testLoadByRemoteIdItemThrowsItemExceptionWithNoItem(): void
    {
        $this->cmsItemLoader = new CmsItemLoader(
            $this->cmsItemBuilderMock,
            ['value' => new ValueLoader(false)]
        );

        $loadedValue = $this->cmsItemLoader->loadByRemoteId(42, 'value');

        self::assertInstanceOf(NullCmsItem::class, $loadedValue);
        self::assertSame('value', $loadedValue->getValueType());
    }

    /**
     * @covers \Netgen\Layouts\Item\CmsItemLoader::loadByRemoteId
     */
    public function testLoadByRemoteIdItemThrowsItemException(): void
    {
        $this->expectException(ItemException::class);
        $this->expectExceptionMessage('Value type "value" does not exist.');

        $this->cmsItemLoader = new CmsItemLoader($this->cmsItemBuilderMock, []);

        $this->cmsItemLoader->loadByRemoteId(42, 'value');
    }
}
