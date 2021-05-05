<?php

declare(strict_types=1);

namespace Netgen\Layouts\Tests\Layout\Type;

use Netgen\Layouts\Layout\Type\Zone;
use PHPUnit\Framework\TestCase;

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

    /**
     * @covers \Netgen\Layouts\Layout\Type\Zone::getIdentifier
     */
    public function testGetIdentifier(): void
    {
        self::assertSame('left', $this->zone->getIdentifier());
    }

    /**
     * @covers \Netgen\Layouts\Layout\Type\Zone::getName
     */
    public function testGetName(): void
    {
        self::assertSame('Left', $this->zone->getName());
    }

    /**
     * @covers \Netgen\Layouts\Layout\Type\Zone::getAllowedBlockDefinitions
     */
    public function testGetAllowedBlockDefinitions(): void
    {
        self::assertSame(['title'], $this->zone->getAllowedBlockDefinitions());
    }
}
