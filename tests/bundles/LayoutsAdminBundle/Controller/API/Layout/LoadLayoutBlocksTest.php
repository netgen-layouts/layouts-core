<?php

declare(strict_types=1);

namespace Netgen\Bundle\LayoutsAdminBundle\Tests\Controller\API\Layout;

use Netgen\Bundle\LayoutsAdminBundle\Controller\API\Layout\LoadLayoutBlocks;
use Netgen\Bundle\LayoutsAdminBundle\Tests\Controller\API\ApiTestCase;
use PHPUnit\Framework\Attributes\CoversClass;
use Symfony\Component\HttpFoundation\Response;

#[CoversClass(LoadLayoutBlocks::class)]
final class LoadLayoutBlocksTest extends ApiTestCase
{
    public function testLoadLayoutBlocks(): void
    {
        $this->browser()
            ->get('/nglayouts/app/api/en/layouts/81168ed3-86f9-55ea-b153-101f96f2c136/blocks?html=false')
            ->assertJson()
            ->assertStatus(Response::HTTP_OK)
            ->assertJsonIs('layouts/load_layout_blocks');
    }

    public function testLoadLayoutBlocksInPublishedState(): void
    {
        $this->browser()
            ->get('/nglayouts/app/api/en/layouts/81168ed3-86f9-55ea-b153-101f96f2c136/blocks?published=true&html=false')
            ->assertJson()
            ->assertStatus(Response::HTTP_OK)
            ->assertJsonIs('layouts/load_published_layout_blocks');
    }

    public function testLoadLayoutBlocksWithNonExistentLayout(): void
    {
        $this->browser()
            ->get('/nglayouts/app/api/en/layouts/ffffffff-ffff-ffff-ffff-ffffffffffff/blocks')
            ->assertJson()
            ->assertStatus(Response::HTTP_NOT_FOUND)
            ->assertJsonMatches('message', 'Could not find layout with identifier "ffffffff-ffff-ffff-ffff-ffffffffffff"');
    }

    public function testLoadLayoutBlocksWithNonExistentLayoutLocale(): void
    {
        $this->browser()
            ->get('/nglayouts/app/api/unknown/layouts/81168ed3-86f9-55ea-b153-101f96f2c136/blocks')
            ->assertJson()
            ->assertStatus(Response::HTTP_NOT_FOUND)
            ->assertJsonMatches('message', 'Could not find layout with identifier "81168ed3-86f9-55ea-b153-101f96f2c136"');
    }
}
