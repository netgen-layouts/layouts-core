<?php

declare(strict_types=1);

namespace Netgen\Layouts\Tests\HttpCache;

use FOS\HttpCache\CacheInvalidator;
use FOS\HttpCache\Exception\ExceptionCollection;
use FOS\HttpCache\ProxyClient\Invalidation\TagCapable;
use Netgen\Layouts\HttpCache\Varnish\HostHeaderProviderInterface;
use Netgen\Layouts\HttpCache\VarnishClient;
use PHPUnit\Framework\TestCase;

final class VarnishClientTest extends TestCase
{
    /**
     * @var \PHPUnit\Framework\MockObject\MockObject
     */
    private $fosInvalidatorMock;

    /**
     * @var \PHPUnit\Framework\MockObject\MockObject
     */
    private $hostHeaderProviderMock;

    /**
     * @var \Netgen\Layouts\HttpCache\VarnishClient
     */
    private $client;

    protected function setUp(): void
    {
        $this->fosInvalidatorMock = $this->createMock(CacheInvalidator::class);
        $this->hostHeaderProviderMock = $this->createMock(HostHeaderProviderInterface::class);

        $this->client = new VarnishClient(
            $this->fosInvalidatorMock,
            $this->hostHeaderProviderMock
        );
    }

    /**
     * @covers \Netgen\Layouts\HttpCache\VarnishClient::__construct
     * @covers \Netgen\Layouts\HttpCache\VarnishClient::purge
     */
    public function testPurge(): void
    {
        if (!interface_exists(TagCapable::class)) {
            self::markTestSkipped('This test is only valid for FOS HTTP Cache v2');
        }

        $this->fosInvalidatorMock
            ->expects(self::at(0))
            ->method('invalidateTags')
            ->with(self::identicalTo(['tag-1', 'tag-2']));

        $this->client->purge(['tag-1', 'tag-2']);
    }

    /**
     * @covers \Netgen\Layouts\HttpCache\VarnishClient::commit
     */
    public function testCommit(): void
    {
        $this->fosInvalidatorMock
            ->expects(self::once())
            ->method('flush');

        self::assertTrue($this->client->commit());
    }

    /**
     * @covers \Netgen\Layouts\HttpCache\VarnishClient::commit
     */
    public function testCommitReturnsFalse(): void
    {
        $this->fosInvalidatorMock
            ->expects(self::once())
            ->method('flush')
            ->willThrowException(new ExceptionCollection());

        self::assertFalse($this->client->commit());
    }
}
