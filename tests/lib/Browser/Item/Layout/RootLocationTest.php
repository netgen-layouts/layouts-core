<?php

declare(strict_types=1);

namespace Netgen\BlockManager\Tests\Browser\Item\Layout;

use Netgen\BlockManager\Browser\Item\Layout\RootLocation;
use PHPUnit\Framework\TestCase;

final class RootLocationTest extends TestCase
{
    /**
     * @var \Netgen\BlockManager\Browser\Item\Layout\RootLocation
     */
    private $location;

    public function setUp(): void
    {
        $this->location = new RootLocation();
    }

    /**
     * @covers \Netgen\BlockManager\Browser\Item\Layout\RootLocation::getLocationId
     */
    public function testGetLocationId(): void
    {
        self::assertSame(0, $this->location->getLocationId());
    }

    /**
     * @covers \Netgen\BlockManager\Browser\Item\Layout\RootLocation::getName
     */
    public function testGetName(): void
    {
        self::assertSame('All layouts', $this->location->getName());
    }

    /**
     * @covers \Netgen\BlockManager\Browser\Item\Layout\RootLocation::getParentId
     */
    public function testGetParentId(): void
    {
        self::assertNull($this->location->getParentId());
    }
}
