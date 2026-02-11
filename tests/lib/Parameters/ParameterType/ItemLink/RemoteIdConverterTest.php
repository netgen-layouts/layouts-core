<?php

declare(strict_types=1);

namespace Netgen\Layouts\Tests\Parameters\ParameterType\ItemLink;

use Netgen\Layouts\Item\CmsItem;
use Netgen\Layouts\Item\CmsItemLoaderInterface;
use Netgen\Layouts\Item\NullCmsItem;
use Netgen\Layouts\Parameters\ParameterType\ItemLink\RemoteIdConverter;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\MockObject\Stub;
use PHPUnit\Framework\TestCase;

#[CoversClass(RemoteIdConverter::class)]
final class RemoteIdConverterTest extends TestCase
{
    private Stub&CmsItemLoaderInterface $cmsItemLoaderStub;

    private RemoteIdConverter $remoteIdConverter;

    protected function setUp(): void
    {
        $this->cmsItemLoaderStub = self::createStub(CmsItemLoaderInterface::class);

        $this->remoteIdConverter = new RemoteIdConverter($this->cmsItemLoaderStub);
    }

    public function testCovertToRemoteId(): void
    {
        $this->cmsItemLoaderStub
            ->method('load')
            ->willReturn(
                CmsItem::fromArray(
                    [
                        'value' => 42,
                        'remoteId' => 'abc',
                    ],
                ),
            );

        self::assertSame('test-value-type://abc', $this->remoteIdConverter->convertToRemoteId('test-value-type://42'));
    }

    public function testCovertToRemoteIdWithInvalidLink(): void
    {
        self::assertSame('null://0', $this->remoteIdConverter->convertToRemoteId('invalid'));
    }

    public function testConvertToRemoteIdWithNullCmsItem(): void
    {
        $this->cmsItemLoaderStub
            ->method('load')
            ->willReturn(new NullCmsItem('test_value_type'));

        self::assertSame('null://0', $this->remoteIdConverter->convertToRemoteId('test-value-type://42'));
    }

    public function testConvertFromRemoteId(): void
    {
        $this->cmsItemLoaderStub
            ->method('loadByRemoteId')
            ->willReturn(
                CmsItem::fromArray(
                    [
                        'value' => 42,
                        'remoteId' => 'abc',
                    ],
                ),
            );

        self::assertSame('test-value-type://42', $this->remoteIdConverter->convertFromRemoteId('test-value-type://abc'));
    }

    public function testConvertFromRemoteIdWithInvalidLink(): void
    {
        self::assertSame('null://0', $this->remoteIdConverter->convertFromRemoteId('invalid'));
    }

    public function testConvertFromRemoteIdWithNullCmsItem(): void
    {
        $this->cmsItemLoaderStub
            ->method('loadByRemoteId')
            ->willReturn(new NullCmsItem('test_value_type'));

        self::assertSame('null://0', $this->remoteIdConverter->convertFromRemoteId('test-value-type://abc'));
    }
}
