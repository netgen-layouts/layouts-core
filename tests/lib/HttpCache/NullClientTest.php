<?php

declare(strict_types=1);

namespace Netgen\Layouts\Tests\HttpCache;

use Netgen\Layouts\HttpCache\NullClient;
use PHPUnit\Framework\TestCase;
use Ramsey\Uuid\Uuid;

final class NullClientTest extends TestCase
{
    /**
     * @var \Netgen\Layouts\HttpCache\NullClient
     */
    private $client;

    protected function setUp(): void
    {
        $this->client = new NullClient();
    }

    /**
     * @covers \Netgen\Layouts\HttpCache\NullClient::invalidateLayouts
     */
    public function testInvalidateLayouts(): void
    {
        $this->client->invalidateLayouts([Uuid::uuid4()->toString(), Uuid::uuid4()->toString()]);

        // We fake the assertion count to disable risky warning
        $this->addToAssertionCount(1);
    }

    /**
     * @covers \Netgen\Layouts\HttpCache\NullClient::invalidateAllLayouts
     */
    public function testInvalidateAllLayouts(): void
    {
        $this->client->invalidateAllLayouts();

        // We fake the assertion count to disable risky warning
        $this->addToAssertionCount(1);
    }

    /**
     * @covers \Netgen\Layouts\HttpCache\NullClient::invalidateBlocks
     */
    public function testInvalidateBlocks(): void
    {
        $this->client->invalidateBlocks([Uuid::uuid4()->toString(), Uuid::uuid4()->toString()]);

        // We fake the assertion count to disable risky warning
        $this->addToAssertionCount(1);
    }

    /**
     * @covers \Netgen\Layouts\HttpCache\NullClient::invalidateLayoutBlocks
     */
    public function testInvalidateLayoutBlocks(): void
    {
        $this->client->invalidateLayoutBlocks([Uuid::uuid4()->toString(), Uuid::uuid4()->toString()]);

        // We fake the assertion count to disable risky warning
        $this->addToAssertionCount(1);
    }

    /**
     * @covers \Netgen\Layouts\HttpCache\NullClient::invalidateAllBlocks
     */
    public function testInvalidateAllBlocks(): void
    {
        $this->client->invalidateAllBlocks();

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
