<?php

declare(strict_types=1);

namespace Netgen\Layouts\Tests\Parameters\ParameterType\ItemLink;

use Netgen\Layouts\Item\CmsItem;
use Netgen\Layouts\Item\CmsItemLoaderInterface;
use Netgen\Layouts\Item\NullCmsItem;
use Netgen\Layouts\Parameters\ParameterType\ItemLink\RemoteIdConverter;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;

#[CoversClass(RemoteIdConverter::class)]
final class RemoteIdConverterTest extends TestCase
{
    private MockObject&CmsItemLoaderInterface $cmsItemLoaderMock;

    private RemoteIdConverter $remoteIdConverter;

    protected function setUp(): void
    {
        $this->cmsItemLoaderMock = $this->createMock(CmsItemLoaderInterface::class);

        $this->remoteIdConverter = new RemoteIdConverter($this->cmsItemLoaderMock);
    }

    public function testCovertToRemoteId(): void
    {
        $this->cmsItemLoaderMock
            ->method('load')
            ->with(self::identicalTo('42'), self::identicalTo('my_value_type'))
            ->willReturn(
                CmsItem::fromArray(
                    [
                        'value' => 42,
                        'remoteId' => 'abc',
                    ],
                ),
            );

        self::assertSame('my-value-type://abc', $this->remoteIdConverter->convertToRemoteId('my-value-type://42'));
    }

    public function testCovertToRemoteIdWithInvalidLink(): void
    {
        self::assertSame('null://0', $this->remoteIdConverter->convertToRemoteId('invalid'));
    }

    public function testConvertToRemoteIdWithNullCmsItem(): void
    {
        $this->cmsItemLoaderMock
            ->method('load')
            ->with(self::identicalTo('42'), self::identicalTo('my_value_type'))
            ->willReturn(new NullCmsItem('my_value_type'));

        self::assertSame('null://0', $this->remoteIdConverter->convertToRemoteId('my-value-type://42'));
    }

    public function testConvertFromRemoteId(): void
    {
        $this->cmsItemLoaderMock
            ->method('loadByRemoteId')
            ->with(self::identicalTo('abc'), self::identicalTo('my_value_type'))
            ->willReturn(
                CmsItem::fromArray(
                    [
                        'value' => 42,
                        'remoteId' => 'abc',
                    ],
                ),
            );

        self::assertSame('my-value-type://42', $this->remoteIdConverter->convertFromRemoteId('my-value-type://abc'));
    }

    public function testConvertFromRemoteIdWithInvalidLink(): void
    {
        self::assertSame('null://0', $this->remoteIdConverter->convertFromRemoteId('invalid'));
    }

    public function testConvertFromRemoteIdWithNullCmsItem(): void
    {
        $this->cmsItemLoaderMock
            ->method('loadByRemoteId')
            ->with(self::identicalTo('abc'), self::identicalTo('my_value_type'))
            ->willReturn(new NullCmsItem('my_value_type'));

        self::assertSame('null://0', $this->remoteIdConverter->convertFromRemoteId('my-value-type://abc'));
    }
}
