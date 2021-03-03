<?php

declare(strict_types=1);

namespace Netgen\Layouts\Tests\Browser\Item\Layout;

use Netgen\Layouts\Browser\Item\Layout\RootLocation;
use PHPUnit\Framework\TestCase;

final class RootLocationTest extends TestCase
{
    private RootLocation $location;

    protected function setUp(): void
    {
        $this->location = new RootLocation();
    }

    /**
     * @covers \Netgen\Layouts\Browser\Item\Layout\RootLocation::getLocationId
     */
    public function testGetLocationId(): void
    {
        self::assertSame('', $this->location->getLocationId());
    }

    /**
     * @covers \Netgen\Layouts\Browser\Item\Layout\RootLocation::getName
     */
    public function testGetName(): void
    {
        self::assertSame('All layouts', $this->location->getName());
    }

    /**
     * @covers \Netgen\Layouts\Browser\Item\Layout\RootLocation::getParentId
     */
    public function testGetParentId(): void
    {
        self::assertNull($this->location->getParentId());
    }
}
