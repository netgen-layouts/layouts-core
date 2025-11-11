<?php

declare(strict_types=1);

namespace Netgen\Bundle\LayoutsAdminBundle\Tests\Controller\API\Layout;

use Netgen\Bundle\LayoutsAdminBundle\Controller\API\Layout\LoadLayoutBlocks;
use Netgen\Bundle\LayoutsAdminBundle\Tests\Controller\API\JsonApiTestCase;
use PHPUnit\Framework\Attributes\CoversClass;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

#[CoversClass(LoadLayoutBlocks::class)]
final class LoadLayoutBlocksTest extends JsonApiTestCase
{
    public function testLoadLayoutBlocks(): void
    {
        $this->client->request(Request::METHOD_GET, '/nglayouts/app/api/en/layouts/81168ed3-86f9-55ea-b153-101f96f2c136/blocks?html=false');

        $this->assertResponse(
            $this->client->getResponse(),
            'layouts/load_layout_blocks',
            Response::HTTP_OK,
        );
    }

    public function testLoadLayoutBlocksInPublishedState(): void
    {
        $this->client->request(Request::METHOD_GET, '/nglayouts/app/api/en/layouts/81168ed3-86f9-55ea-b153-101f96f2c136/blocks?published=true&html=false');

        $this->assertResponse(
            $this->client->getResponse(),
            'layouts/load_published_layout_blocks',
            Response::HTTP_OK,
        );
    }

    public function testLoadLayoutBlocksWithNonExistentLayout(): void
    {
        $this->client->request(Request::METHOD_GET, '/nglayouts/app/api/en/layouts/ffffffff-ffff-ffff-ffff-ffffffffffff/blocks');

        $this->assertException(
            $this->client->getResponse(),
            Response::HTTP_NOT_FOUND,
            'Could not find layout with identifier "ffffffff-ffff-ffff-ffff-ffffffffffff"',
        );
    }

    public function testLoadLayoutBlocksWithNonExistentLayoutLocale(): void
    {
        $this->client->request(Request::METHOD_GET, '/nglayouts/app/api/unknown/layouts/81168ed3-86f9-55ea-b153-101f96f2c136/blocks');

        $this->assertException(
            $this->client->getResponse(),
            Response::HTTP_NOT_FOUND,
            'Could not find layout with identifier "81168ed3-86f9-55ea-b153-101f96f2c136"',
        );
    }
}
