<?php

declare(strict_types=1);

namespace Netgen\Bundle\LayoutsAdminBundle\Tests\Controller\API\Layout;

use Netgen\Bundle\LayoutsAdminBundle\Controller\API\Layout\Load;
use Netgen\Bundle\LayoutsAdminBundle\Tests\Controller\API\ApiTestCase;
use PHPUnit\Framework\Attributes\CoversClass;
use Symfony\Component\HttpFoundation\Response;

#[CoversClass(Load::class)]
final class LoadTest extends ApiTestCase
{
    public function testLoad(): void
    {
        $this->browser()
            ->get('/nglayouts/app/api/layouts/81168ed3-86f9-55ea-b153-101f96f2c136?html=false')
            ->assertJson()
            ->assertStatus(Response::HTTP_OK)
            ->assertJsonIs('layouts/load_layout');
    }

    public function testLoadInPublishedState(): void
    {
        $this->browser()
            ->get('/nglayouts/app/api/layouts/81168ed3-86f9-55ea-b153-101f96f2c136?published=true&html=false')
            ->assertJson()
            ->assertStatus(Response::HTTP_OK)
            ->assertJsonIs('layouts/load_published_layout');
    }

    public function testLoadWithNonExistentLayout(): void
    {
        $this->browser()
            ->get('/nglayouts/app/api/layouts/ffffffff-ffff-ffff-ffff-ffffffffffff')
            ->assertJson()
            ->assertStatus(Response::HTTP_NOT_FOUND)
            ->assertJsonMatches('message', 'Could not find layout with identifier "ffffffff-ffff-ffff-ffff-ffffffffffff"');
    }
}
