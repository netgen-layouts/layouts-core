<?php

declare(strict_types=1);

namespace Netgen\Layouts\Tests\HttpCache\Varnish;

use Netgen\Layouts\HttpCache\Varnish\FOSHostHeaderProvider;
use PHPUnit\Framework\TestCase;

final class FOSHostHeaderProviderTest extends TestCase
{
    /**
     * @covers \Netgen\Layouts\HttpCache\Varnish\FOSHostHeaderProvider::__construct
     * @covers \Netgen\Layouts\HttpCache\Varnish\FOSHostHeaderProvider::provideHostHeader
     */
    public function testProvideHostHeader(): void
    {
        $hostHeaderProvider = new FOSHostHeaderProvider(['http://localhost:4242', 'http://localhost:2424']);
        self::assertSame('http://localhost:4242', $hostHeaderProvider->provideHostHeader());
    }

    /**
     * @covers \Netgen\Layouts\HttpCache\Varnish\FOSHostHeaderProvider::provideHostHeader
     */
    public function testProvideHostHeaderWithNoServers(): void
    {
        $hostHeaderProvider = new FOSHostHeaderProvider();
        self::assertSame('', $hostHeaderProvider->provideHostHeader());
    }
}
