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
     */
    public function testLoad(): void
    {
        self::assertNull($this->valueLoader->load(42));
    }

    /**
     * @covers \Netgen\BlockManager\Item\ValueLoader\NullValueLoader::loadByRemoteId
     */
    public function testLoadByRemoteId(): void
    {
        self::assertNull($this->valueLoader->loadByRemoteId('abc'));
    }
}
