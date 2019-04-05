<?php

declare(strict_types=1);

namespace Netgen\BlockManager\Tests\Parameters\ParameterType\ItemLink;

use Netgen\BlockManager\Item\CmsItem;
use Netgen\BlockManager\Item\CmsItemLoaderInterface;
use Netgen\BlockManager\Item\NullCmsItem;
use Netgen\BlockManager\Parameters\ParameterType\ItemLink\RemoteIdConverter;
use PHPUnit\Framework\TestCase;

final class RemoteIdConverterTest extends TestCase
{
    /**
     * @var \PHPUnit\Framework\MockObject\MockObject
     */
    private $cmsItemLoaderMock;

    /**
     * @var \Netgen\BlockManager\Parameters\ParameterType\ItemLink\RemoteIdConverter
     */
    private $remoteIdConverter;

    public function setUp(): void
    {
        $this->cmsItemLoaderMock = $this->createMock(CmsItemLoaderInterface::class);

        $this->remoteIdConverter = new RemoteIdConverter($this->cmsItemLoaderMock);
    }

    /**
     * @covers \Netgen\BlockManager\Parameters\ParameterType\ItemLink\RemoteIdConverter::__construct
     * @covers \Netgen\BlockManager\Parameters\ParameterType\ItemLink\RemoteIdConverter::convertToRemoteId
     */
    public function testCovertToRemoteId(): void
    {
        $this->cmsItemLoaderMock
            ->expects(self::any())
            ->method('load')
            ->with(self::identicalTo('42'), self::identicalTo('my_value_type'))
            ->willReturn(
                CmsItem::fromArray(
                    [
                        'value' => 42,
                        'remoteId' => 'abc',
                    ]
                )
            );

        self::assertSame('my-value-type://abc', $this->remoteIdConverter->convertToRemoteId('my-value-type://42'));
    }

    /**
     * @covers \Netgen\BlockManager\Parameters\ParameterType\ItemLink\RemoteIdConverter::convertToRemoteId
     */
    public function testCovertToRemoteIdWithInvalidLink(): void
    {
        self::assertSame('null://0', $this->remoteIdConverter->convertToRemoteId('invalid'));
    }

    /**
     * @covers \Netgen\BlockManager\Parameters\ParameterType\ItemLink\RemoteIdConverter::convertToRemoteId
     */
    public function testConvertToRemoteIdWithNullCmsItem(): void
    {
        $this->cmsItemLoaderMock
            ->expects(self::any())
            ->method('load')
            ->with(self::identicalTo('42'), self::identicalTo('my_value_type'))
            ->willReturn(new NullCmsItem('my_value_type'));

        self::assertSame('null://0', $this->remoteIdConverter->convertToRemoteId('my-value-type://42'));
    }

    /**
     * @covers \Netgen\BlockManager\Parameters\ParameterType\ItemLink\RemoteIdConverter::convertFromRemoteId
     */
    public function testConvertFromRemoteId(): void
    {
        $this->cmsItemLoaderMock
            ->expects(self::any())
            ->method('loadByRemoteId')
            ->with(self::identicalTo('abc'), self::identicalTo('my_value_type'))
            ->willReturn(
                CmsItem::fromArray(
                    [
                        'value' => 42,
                        'remoteId' => 'abc',
                    ]
                )
            );

        self::assertSame('my-value-type://42', $this->remoteIdConverter->convertFromRemoteId('my-value-type://abc'));
    }

    /**
     * @covers \Netgen\BlockManager\Parameters\ParameterType\ItemLink\RemoteIdConverter::convertFromRemoteId
     */
    public function testConvertFromRemoteIdWithInvalidLink(): void
    {
        self::assertSame('null://0', $this->remoteIdConverter->convertFromRemoteId('invalid'));
    }

    /**
     * @covers \Netgen\BlockManager\Parameters\ParameterType\ItemLink\RemoteIdConverter::convertFromRemoteId
     */
    public function testConvertFromRemoteIdWithNullCmsItem(): void
    {
        $this->cmsItemLoaderMock
            ->expects(self::any())
            ->method('loadByRemoteId')
            ->with(self::identicalTo('abc'), self::identicalTo('my_value_type'))
            ->willReturn(new NullCmsItem('my_value_type'));

        self::assertSame('null://0', $this->remoteIdConverter->convertFromRemoteId('my-value-type://abc'));
    }
}
