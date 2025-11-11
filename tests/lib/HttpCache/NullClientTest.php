<?php

declare(strict_types=1);

namespace Netgen\Layouts\Tests\HttpCache;

use Netgen\Layouts\HttpCache\NullClient;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\TestCase;

#[CoversClass(NullClient::class)]
final class NullClientTest extends TestCase
{
    private NullClient $client;

    protected function setUp(): void
    {
        $this->client = new NullClient();
    }

    public function testPurge(): void
    {
        $this->client->purge(['ngl-block-1', 'ngl-block-2']);

        // We fake the assertion count to disable risky warning
        $this->addToAssertionCount(1);
    }
}
