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
            ->expects($this->any())
            ->method('load')
            ->with($this->identicalTo('42'), $this->identicalTo('my_value_type'))
            ->will(
                $this->returnValue(
                    new CmsItem(
                        [
                            'value' => 42,
                            'remoteId' => 'abc',
                        ]
                    )
                )
            );

        $this->assertSame('my-value-type://abc', $this->remoteIdConverter->convertToRemoteId('my-value-type://42'));
    }

    /**
     * @covers \Netgen\BlockManager\Parameters\ParameterType\ItemLink\RemoteIdConverter::convertToRemoteId
     */
    public function testCovertToRemoteIdWithInvalidLink(): void
    {
        $this->assertSame('null://0', $this->remoteIdConverter->convertToRemoteId('invalid'));
    }

    /**
     * @covers \Netgen\BlockManager\Parameters\ParameterType\ItemLink\RemoteIdConverter::convertToRemoteId
     */
    public function testConvertToRemoteIdWithNullCmsItem(): void
    {
        $this->cmsItemLoaderMock
            ->expects($this->any())
            ->method('load')
            ->with($this->identicalTo('42'), $this->identicalTo('my_value_type'))
            ->will($this->returnValue(new NullCmsItem('my_value_type')));

        $this->assertSame('null://0', $this->remoteIdConverter->convertToRemoteId('my-value-type://42'));
    }

    /**
     * @covers \Netgen\BlockManager\Parameters\ParameterType\ItemLink\RemoteIdConverter::convertFromRemoteId
     */
    public function testConvertFromRemoteId(): void
    {
        $this->cmsItemLoaderMock
            ->expects($this->any())
            ->method('loadByRemoteId')
            ->with($this->identicalTo('abc'), $this->identicalTo('my_value_type'))
            ->will(
                $this->returnValue(
                    new CmsItem(
                        [
                            'value' => 42,
                            'remoteId' => 'abc',
                        ]
                    )
                )
            );

        $this->assertSame('my-value-type://42', $this->remoteIdConverter->convertFromRemoteId('my-value-type://abc'));
    }

    /**
     * @covers \Netgen\BlockManager\Parameters\ParameterType\ItemLink\RemoteIdConverter::convertFromRemoteId
     */
    public function testConvertFromRemoteIdWithInvalidLink(): void
    {
        $this->assertSame('null://0', $this->remoteIdConverter->convertFromRemoteId('invalid'));
    }

    /**
     * @covers \Netgen\BlockManager\Parameters\ParameterType\ItemLink\RemoteIdConverter::convertFromRemoteId
     */
    public function testConvertFromRemoteIdWithNullCmsItem(): void
    {
        $this->cmsItemLoaderMock
            ->expects($this->any())
            ->method('loadByRemoteId')
            ->with($this->identicalTo('abc'), $this->identicalTo('my_value_type'))
            ->will($this->returnValue(new NullCmsItem('my_value_type')));

        $this->assertSame('null://0', $this->remoteIdConverter->convertFromRemoteId('my-value-type://abc'));
    }
}
