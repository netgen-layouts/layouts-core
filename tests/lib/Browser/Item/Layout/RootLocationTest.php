<?php

declare(strict_types=1);

namespace Netgen\Layouts\Tests\Browser\Item\Layout;

use Netgen\Layouts\Browser\Item\Layout\RootLocation;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\TestCase;

#[CoversClass(RootLocation::class)]
final class RootLocationTest extends TestCase
{
    private RootLocation $location;

    protected function setUp(): void
    {
        $this->location = new RootLocation();
    }

    public function testGetLocationId(): void
    {
        self::assertSame('', $this->location->getLocationId());
    }

    public function testGetName(): void
    {
        self::assertSame('All layouts', $this->location->getName());
    }
}
