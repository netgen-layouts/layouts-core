<?php

declare(strict_types=1);

namespace Netgen\Bundle\LayoutsAdminBundle\Tests\Controller\API\Layout;

use Netgen\Bundle\LayoutsAdminBundle\Controller\API\Layout\LoadZoneBlocks;
use Netgen\Bundle\LayoutsAdminBundle\Tests\Controller\API\ApiTestCase;
use PHPUnit\Framework\Attributes\CoversClass;
use Symfony\Component\HttpFoundation\Response;

#[CoversClass(LoadZoneBlocks::class)]
final class LoadZoneBlocksTest extends ApiTestCase
{
    public function testLoadZoneBlocks(): void
    {
        $this->browser()
            ->get('/nglayouts/app/api/en/layouts/81168ed3-86f9-55ea-b153-101f96f2c136/zones/right/blocks?html=false')
            ->assertJson()
            ->assertStatus(Response::HTTP_OK)
            ->assertJsonIs('layouts/load_zone_blocks');
    }

    public function testLoadZoneBlocksInPublishedState(): void
    {
        $this->browser()
            ->get('/nglayouts/app/api/en/layouts/81168ed3-86f9-55ea-b153-101f96f2c136/zones/right/blocks?published=true&html=false')
            ->assertJson()
            ->assertStatus(Response::HTTP_OK)
            ->assertJsonIs('layouts/load_published_zone_blocks');
    }

    public function testLoadZoneBlocksWithNonExistentZone(): void
    {
        $this->browser()
            ->get('/nglayouts/app/api/en/layouts/81168ed3-86f9-55ea-b153-101f96f2c136/zones/unknown/blocks')
            ->assertJson()
            ->assertStatus(Response::HTTP_NOT_FOUND)
            ->assertJsonMatches('message', 'Could not find zone with identifier "unknown"');
    }

    public function testLoadZoneBlocksWithNonExistentLayout(): void
    {
        $this->browser()
            ->get('/nglayouts/app/api/en/layouts/ffffffff-ffff-ffff-ffff-ffffffffffff/zones/right/blocks')
            ->assertJson()
            ->assertStatus(Response::HTTP_NOT_FOUND)
            ->assertJsonMatches('message', 'Could not find layout with identifier "ffffffff-ffff-ffff-ffff-ffffffffffff"');
    }

    public function testLoadZoneBlocksWithNonExistentLayoutLocale(): void
    {
        $this->browser()
            ->get('/nglayouts/app/api/unknown/layouts/81168ed3-86f9-55ea-b153-101f96f2c136/zones/right/blocks')
            ->assertJson()
            ->assertStatus(Response::HTTP_NOT_FOUND)
            ->assertJsonMatches('message', 'Could not find layout with identifier "81168ed3-86f9-55ea-b153-101f96f2c136"');
    }
}
