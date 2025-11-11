<?php

declare(strict_types=1);

namespace Netgen\Layouts\Tests\Layout\Type;

use Netgen\Layouts\Layout\Type\Zone;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\TestCase;

#[CoversClass(Zone::class)]
final class ZoneTest extends TestCase
{
    private Zone $zone;

    protected function setUp(): void
    {
        $this->zone = Zone::fromArray(
            [
                'identifier' => 'left',
                'name' => 'Left',
                'allowedBlockDefinitions' => ['title'],
            ],
        );
    }

    public function testGetIdentifier(): void
    {
        self::assertSame('left', $this->zone->getIdentifier());
    }

    public function testGetName(): void
    {
        self::assertSame('Left', $this->zone->getName());
    }

    public function testGetAllowedBlockDefinitions(): void
    {
        self::assertSame(['title'], $this->zone->getAllowedBlockDefinitions());
    }
}
