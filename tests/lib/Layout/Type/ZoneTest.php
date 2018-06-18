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
        $this->zone = new Zone(
            [
                'identifier' => 'left',
                'name' => 'Left',
                'allowedBlockDefinitions' => ['title'],
            ]
        );
    }

    /**
     * @covers \Netgen\BlockManager\Layout\Type\Zone::__construct
     * @covers \Netgen\BlockManager\Layout\Type\Zone::getIdentifier
     */
    public function testGetIdentifier(): void
    {
        $this->assertSame('left', $this->zone->getIdentifier());
    }

    /**
     * @covers \Netgen\BlockManager\Layout\Type\Zone::getName
     */
    public function testGetName(): void
    {
        $this->assertSame('Left', $this->zone->getName());
    }

    /**
     * @covers \Netgen\BlockManager\Layout\Type\Zone::getAllowedBlockDefinitions
     */
    public function testGetAllowedBlockDefinitions(): void
    {
        $this->assertSame(['title'], $this->zone->getAllowedBlockDefinitions());
    }
}
