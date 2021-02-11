<?php

declare(strict_types=1);

namespace Netgen\Layouts\Tests\HttpCache;

use Netgen\Layouts\HttpCache\NullClient;
use PHPUnit\Framework\TestCase;

final class NullClientTest extends TestCase
{
    private NullClient $client;

    protected function setUp(): void
    {
        $this->client = new NullClient();
    }

    /**
     * @covers \Netgen\Layouts\HttpCache\NullClient::purge
     */
    public function testPurge(): void
    {
        $this->client->purge(['ngl-block-1', 'ngl-block-2']);

        // We fake the assertion count to disable risky warning
        $this->addToAssertionCount(1);
    }

    /**
     * @covers \Netgen\Layouts\HttpCache\NullClient::commit
     */
    public function testCommit(): void
    {
        self::assertTrue($this->client->commit());
    }
}
