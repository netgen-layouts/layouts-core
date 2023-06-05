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
use Netgen\Layouts\Tests\Stubs\Container;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use stdClass;

final class CmsItemLoaderTest extends TestCase
{
    /**
     * @var \PHPUnit\Framework\MockObject\MockObject&\Netgen\Layouts\Item\CmsItemBuilderInterface
     */
    private MockObject $cmsItemBuilderMock;

    private CmsItemLoader $cmsItemLoader;

    protected function setUp(): void
    {
        $this->cmsItemBuilderMock = $this->createMock(CmsItemBuilderInterface::class);
    }

    /**
     * @covers \Netgen\Layouts\Item\CmsItemLoader::__construct
     * @covers \Netgen\Layouts\Item\CmsItemLoader::getValueLoader
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
            ],
        );

        $this->cmsItemLoader = new CmsItemLoader(
            $this->cmsItemBuilderMock,
            new Container(['value' => new ValueLoader(true)]),
        );

        $this->cmsItemBuilderMock
            ->method('build')
            ->willReturn($item);

        self::assertSame($item, $this->cmsItemLoader->load(42, 'value'));
    }

    /**
     * @covers \Netgen\Layouts\Item\CmsItemLoader::getValueLoader
     * @covers \Netgen\Layouts\Item\CmsItemLoader::load
     */
    public function testLoadItemWithNoItem(): void
    {
        $this->cmsItemLoader = new CmsItemLoader(
            $this->cmsItemBuilderMock,
            new Container(['value' => new ValueLoader(false)]),
        );

        $loadedValue = $this->cmsItemLoader->load(42, 'value');

        self::assertInstanceOf(NullCmsItem::class, $loadedValue);
        self::assertSame('value', $loadedValue->getValueType());
    }

    /**
     * @covers \Netgen\Layouts\Item\CmsItemLoader::getValueLoader
     * @covers \Netgen\Layouts\Item\CmsItemLoader::load
     */
    public function testLoadItemThrowsItemException(): void
    {
        $this->expectException(ItemException::class);
        $this->expectExceptionMessage('Value loader for "value" value type does not exist.');

        $this->cmsItemLoader = new CmsItemLoader($this->cmsItemBuilderMock, new Container());

        $this->cmsItemLoader->load(42, 'value');
    }

    /**
     * @covers \Netgen\Layouts\Item\CmsItemLoader::getValueLoader
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
            ],
        );

        $this->cmsItemLoader = new CmsItemLoader(
            $this->cmsItemBuilderMock,
            new Container(['value' => new ValueLoader(true)]),
        );

        $this->cmsItemBuilderMock
            ->method('build')
            ->willReturn($item);

        self::assertSame($item, $this->cmsItemLoader->loadByRemoteId(42, 'value'));
    }

    /**
     * @covers \Netgen\Layouts\Item\CmsItemLoader::getValueLoader
     * @covers \Netgen\Layouts\Item\CmsItemLoader::loadByRemoteId
     */
    public function testLoadByRemoteIdItemThrowsItemExceptionWithNoItem(): void
    {
        $this->cmsItemLoader = new CmsItemLoader(
            $this->cmsItemBuilderMock,
            new Container(['value' => new ValueLoader(false)]),
        );

        $loadedValue = $this->cmsItemLoader->loadByRemoteId(42, 'value');

        self::assertInstanceOf(NullCmsItem::class, $loadedValue);
        self::assertSame('value', $loadedValue->getValueType());
    }

    /**
     * @covers \Netgen\Layouts\Item\CmsItemLoader::getValueLoader
     * @covers \Netgen\Layouts\Item\CmsItemLoader::loadByRemoteId
     */
    public function testLoadByRemoteIdItemThrowsItemExceptionWithNoLoader(): void
    {
        $this->expectException(ItemException::class);
        $this->expectExceptionMessage('Value loader for "value" value type does not exist.');

        $this->cmsItemLoader = new CmsItemLoader($this->cmsItemBuilderMock, new Container());

        $this->cmsItemLoader->loadByRemoteId(42, 'value');
    }

    /**
     * @covers \Netgen\Layouts\Item\CmsItemLoader::getValueLoader
     * @covers \Netgen\Layouts\Item\CmsItemLoader::loadByRemoteId
     */
    public function testLoadByRemoteIdItemThrowsItemExceptionWithInvalidLoader(): void
    {
        $this->expectException(ItemException::class);
        $this->expectExceptionMessage('Value loader for "value" value type does not exist.');

        $this->cmsItemLoader = new CmsItemLoader($this->cmsItemBuilderMock, new Container(['value' => new stdClass()]));

        $this->cmsItemLoader->loadByRemoteId(42, 'value');
    }
}
