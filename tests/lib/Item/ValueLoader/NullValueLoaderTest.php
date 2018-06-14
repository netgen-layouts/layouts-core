<?php

declare(strict_types=1);

namespace Netgen\BlockManager\Tests\Item\ValueLoader;

use Netgen\BlockManager\Item\ValueLoader\NullValueLoader;
use PHPUnit\Framework\TestCase;

final class NullValueLoaderTest extends TestCase
{
    /**
     * @var \Netgen\BlockManager\Item\ValueLoader\NullValueLoader
     */
    private $valueLoader;

    public function setUp(): void
    {
        $this->valueLoader = new NullValueLoader();
    }

    /**
     * @covers \Netgen\BlockManager\Item\ValueLoader\NullValueLoader::load
     * @expectedException \Netgen\BlockManager\Exception\Item\ItemException
     * @expectedExceptionMessage Item with ID "42" could not be loaded.
     */
    public function testLoad(): void
    {
        $this->valueLoader->load(42);
    }

    /**
     * @covers \Netgen\BlockManager\Item\ValueLoader\NullValueLoader::loadByRemoteId
     * @expectedException \Netgen\BlockManager\Exception\Item\ItemException
     * @expectedExceptionMessage Item with remote ID "abc" could not be loaded.
     */
    public function testLoadByRemoteId(): void
    {
        $this->valueLoader->loadByRemoteId('abc');
    }
}
