<?php

namespace Netgen\BlockManager\Tests\HttpCache;

use Netgen\BlockManager\HttpCache\NullClient;
use PHPUnit\Framework\TestCase;

final class NullClientTest extends TestCase
{
    /**
     * @var \Netgen\BlockManager\HttpCache\NullClient
     */
    private $client;

    public function setUp()
    {
        $this->client = new NullClient();
    }

    /**
     * @covers \Netgen\BlockManager\HttpCache\NullClient::invalidateLayouts
     */
    public function testInvalidateLayouts()
    {
        $this->assertNull($this->client->invalidateLayouts([24, 42]));
    }

    /**
     * @covers \Netgen\BlockManager\HttpCache\NullClient::invalidateAllLayouts
     */
    public function testInvalidateAllLayouts()
    {
        $this->assertNull($this->client->invalidateAllLayouts());
    }

    /**
     * @covers \Netgen\BlockManager\HttpCache\NullClient::invalidateBlocks
     */
    public function testInvalidateBlocks()
    {
        $this->assertNull($this->client->invalidateBlocks([24, 42]));
    }

    /**
     * @covers \Netgen\BlockManager\HttpCache\NullClient::invalidateLayoutBlocks
     */
    public function testInvalidateLayoutBlocks()
    {
        $this->assertNull($this->client->invalidateLayoutBlocks([24, 42]));
    }

    /**
     * @covers \Netgen\BlockManager\HttpCache\NullClient::invalidateAllBlocks
     */
    public function testInvalidateAllBlocks()
    {
        $this->assertNull($this->client->invalidateAllBlocks());
    }

    /**
     * @covers \Netgen\BlockManager\HttpCache\NullClient::commit
     */
    public function testCommit()
    {
        $this->assertTrue($this->client->commit());
    }
}
