<?php

declare(strict_types=1);

namespace Netgen\Layouts\Tests\Item\ValueLoader;

use Netgen\Layouts\Item\ValueLoader\NullValueLoader;
use PHPUnit\Framework\TestCase;

final class NullValueLoaderTest extends TestCase
{
    private NullValueLoader $valueLoader;

    protected function setUp(): void
    {
        $this->valueLoader = new NullValueLoader();
    }

    /**
     * @covers \Netgen\Layouts\Item\ValueLoader\NullValueLoader::load
     */
    public function testLoad(): void
    {
        self::assertNull($this->valueLoader->load(42));
    }

    /**
     * @covers \Netgen\Layouts\Item\ValueLoader\NullValueLoader::loadByRemoteId
     */
    public function testLoadByRemoteId(): void
    {
        self::assertNull($this->valueLoader->loadByRemoteId('abc'));
    }
}
