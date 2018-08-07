<?php

declare(strict_types=1);

namespace Netgen\BlockManager\Tests\Layout\Type;

use Netgen\BlockManager\Layout\Type\Zone;
use PHPUnit\Framework\TestCase;

final class ZoneTest extends TestCase
{
    /**
     * @var \Netgen\BlockManager\Layout\Type\Zone
     */
    private $zone;

    public function setUp(): void
    {
        $this->zone = Zone::fromArray(
            [
                'identifier' => 'left',
                'name' => 'Left',
                'allowedBlockDefinitions' => ['title'],
            ]
        );
    }

    /**
     * @covers \Netgen\BlockManager\Layout\Type\Zone::getIdentifier
     */
    public function testGetIdentifier(): void
    {
        self::assertSame('left', $this->zone->getIdentifier());
    }

    /**
     * @covers \Netgen\BlockManager\Layout\Type\Zone::getName
     */
    public function testGetName(): void
    {
        self::assertSame('Left', $this->zone->getName());
    }

    /**
     * @covers \Netgen\BlockManager\Layout\Type\Zone::getAllowedBlockDefinitions
     */
    public function testGetAllowedBlockDefinitions(): void
    {
        self::assertSame(['title'], $this->zone->getAllowedBlockDefinitions());
    }
}
