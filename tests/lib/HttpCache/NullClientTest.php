<?php

declare(strict_types=1);

namespace Netgen\Layouts\Tests\HttpCache;

use Netgen\Layouts\HttpCache\NullClient;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\DoesNotPerformAssertions;
use PHPUnit\Framework\TestCase;

#[CoversClass(NullClient::class)]
final class NullClientTest extends TestCase
{
    private NullClient $client;

    protected function setUp(): void
    {
        $this->client = new NullClient();
    }

    #[DoesNotPerformAssertions]
    public function testPurge(): void
    {
        $this->client->purge(['ngl-block-1', 'ngl-block-2']);
    }
}
