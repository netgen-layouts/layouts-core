<?php

declare(strict_types=1);

namespace Netgen\Bundle\LayoutsAdminBundle\Tests\Controller\API\Layout;

use Netgen\Bundle\LayoutsAdminBundle\Controller\API\Layout\LoadZoneBlocks;
use Netgen\Bundle\LayoutsAdminBundle\Tests\Controller\API\JsonApiTestCase;
use PHPUnit\Framework\Attributes\CoversClass;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

#[CoversClass(LoadZoneBlocks::class)]
final class LoadZoneBlocksTest extends JsonApiTestCase
{
    public function testLoadZoneBlocks(): void
    {
        $this->client->request(Request::METHOD_GET, '/nglayouts/app/api/en/layouts/81168ed3-86f9-55ea-b153-101f96f2c136/zones/right/blocks?html=false');

        $this->assertResponse(
            $this->client->getResponse(),
            'layouts/load_zone_blocks',
            Response::HTTP_OK,
        );
    }

    public function testLoadZoneBlocksInPublishedState(): void
    {
        $this->client->request(Request::METHOD_GET, '/nglayouts/app/api/en/layouts/81168ed3-86f9-55ea-b153-101f96f2c136/zones/right/blocks?published=true&html=false');

        $this->assertResponse(
            $this->client->getResponse(),
            'layouts/load_published_zone_blocks',
            Response::HTTP_OK,
        );
    }

    public function testLoadZoneBlocksWithNonExistentZone(): void
    {
        $this->client->request(Request::METHOD_GET, '/nglayouts/app/api/en/layouts/81168ed3-86f9-55ea-b153-101f96f2c136/zones/unknown/blocks');

        $this->assertException(
            $this->client->getResponse(),
            Response::HTTP_NOT_FOUND,
            'Could not find zone with identifier "unknown"',
        );
    }

    public function testLoadZoneBlocksWithNonExistentLayout(): void
    {
        $this->client->request(Request::METHOD_GET, '/nglayouts/app/api/en/layouts/ffffffff-ffff-ffff-ffff-ffffffffffff/zones/right/blocks');

        $this->assertException(
            $this->client->getResponse(),
            Response::HTTP_NOT_FOUND,
            'Could not find layout with identifier "ffffffff-ffff-ffff-ffff-ffffffffffff"',
        );
    }

    public function testLoadZoneBlocksWithNonExistentLayoutLocale(): void
    {
        $this->client->request(Request::METHOD_GET, '/nglayouts/app/api/unknown/layouts/81168ed3-86f9-55ea-b153-101f96f2c136/zones/right/blocks');

        $this->assertException(
            $this->client->getResponse(),
            Response::HTTP_NOT_FOUND,
            'Could not find layout with identifier "81168ed3-86f9-55ea-b153-101f96f2c136"',
        );
    }
}
