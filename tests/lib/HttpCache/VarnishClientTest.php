<?php

declare(strict_types=1);

namespace Netgen\Layouts\Tests\HttpCache;

use FOS\HttpCache\CacheInvalidator;
use FOS\HttpCache\Exception\ExceptionCollection;
use Netgen\Layouts\HttpCache\VarnishClient;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;

#[CoversClass(VarnishClient::class)]
final class VarnishClientTest extends TestCase
{
    private MockObject&CacheInvalidator $fosInvalidatorMock;

    private VarnishClient $client;

    protected function setUp(): void
    {
        $this->fosInvalidatorMock = $this->createMock(CacheInvalidator::class);

        $this->client = new VarnishClient(
            $this->fosInvalidatorMock,
        );
    }

    public function testPurge(): void
    {
        $this->fosInvalidatorMock
            ->expects($this->once())
            ->method('invalidateTags')
            ->with(self::identicalTo(['tag-1', 'tag-2']));

        $this->client->purge(['tag-1', 'tag-2']);
    }

    public function testCommit(): void
    {
        $this->fosInvalidatorMock
            ->expects($this->once())
            ->method('flush');

        self::assertTrue($this->client->commit());
    }

    public function testCommitReturnsFalse(): void
    {
        $this->fosInvalidatorMock
            ->expects($this->once())
            ->method('flush')
            ->willThrowException(new ExceptionCollection());

        self::assertFalse($this->client->commit());
    }
}
