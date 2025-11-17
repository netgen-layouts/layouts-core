<?php

declare(strict_types=1);

namespace Netgen\Bundle\LayoutsAdminBundle\Tests\Controller\API\Layout;

use Netgen\Bundle\LayoutsAdminBundle\Controller\API\Layout\UnlinkZone;
use Netgen\Bundle\LayoutsAdminBundle\Tests\Controller\API\ApiTestCase;
use PHPUnit\Framework\Attributes\CoversClass;
use Symfony\Component\HttpFoundation\Response;

#[CoversClass(UnlinkZone::class)]
final class UnlinkZoneTest extends ApiTestCase
{
    public function testUnlinkZone(): void
    {
        $this->browser()
            ->delete('/nglayouts/app/api/layouts/81168ed3-86f9-55ea-b153-101f96f2c136/zones/right/link')
            ->assertStatus(Response::HTTP_NO_CONTENT);
    }

    public function testUnlinkZoneWithNonExistentZone(): void
    {
        $this->browser()
            ->delete('/nglayouts/app/api/layouts/81168ed3-86f9-55ea-b153-101f96f2c136/zones/unknown/link')
            ->assertJson()
            ->assertStatus(Response::HTTP_NOT_FOUND)
            ->assertJsonMatches('message', 'Could not find zone with identifier "unknown"');
    }

    public function testUnlinkZoneWithNonExistentLayout(): void
    {
        $this->browser()
            ->delete('/nglayouts/app/api/layouts/ffffffff-ffff-ffff-ffff-ffffffffffff/zones/right/link')
            ->assertJson()
            ->assertStatus(Response::HTTP_NOT_FOUND)
            ->assertJsonMatches('message', 'Could not find layout with identifier "ffffffff-ffff-ffff-ffff-ffffffffffff"');
    }
}
