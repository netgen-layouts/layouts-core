<?php

declare(strict_types=1);

namespace Netgen\Bundle\BlockManagerBundle\Tests\Controller\API\V1\Layout;

use Netgen\Bundle\BlockManagerBundle\Tests\Controller\API\JsonApiTestCase;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

final class LoadZoneBlocksTest extends JsonApiTestCase
{
    /**
     * @covers \Netgen\Bundle\BlockManagerBundle\Controller\API\V1\Layout\LoadZoneBlocks::__construct
     * @covers \Netgen\Bundle\BlockManagerBundle\Controller\API\V1\Layout\LoadZoneBlocks::__invoke
     */
    public function testLoadZoneBlocks(): void
    {
        $this->client->request(Request::METHOD_GET, '/bm/api/v1/en/layouts/1/zones/right/blocks?html=false');

        $this->assertResponse(
            $this->client->getResponse(),
            'v1/layouts/load_zone_blocks',
            Response::HTTP_OK
        );
    }

    /**
     * @covers \Netgen\Bundle\BlockManagerBundle\Controller\API\V1\Layout\LoadZoneBlocks::__invoke
     */
    public function testLoadZoneBlocksInPublishedState(): void
    {
        $this->client->request(Request::METHOD_GET, '/bm/api/v1/en/layouts/1/zones/right/blocks?published=true&html=false');

        $this->assertResponse(
            $this->client->getResponse(),
            'v1/layouts/load_published_zone_blocks',
            Response::HTTP_OK
        );
    }

    /**
     * @covers \Netgen\Bundle\BlockManagerBundle\Controller\API\V1\Layout\LoadZoneBlocks::__invoke
     */
    public function testLoadZoneBlocksWithNonExistentZone(): void
    {
        $this->client->request(Request::METHOD_GET, '/bm/api/v1/en/layouts/1/zones/unknown/blocks');

        $this->assertException(
            $this->client->getResponse(),
            Response::HTTP_NOT_FOUND,
            'Could not find zone with identifier "unknown"'
        );
    }

    /**
     * @covers \Netgen\Bundle\BlockManagerBundle\Controller\API\V1\Layout\LoadZoneBlocks::__invoke
     */
    public function testLoadZoneBlocksWithNonExistentLayout(): void
    {
        $this->client->request(Request::METHOD_GET, '/bm/api/v1/en/layouts/9999/zones/right/blocks');

        $this->assertException(
            $this->client->getResponse(),
            Response::HTTP_NOT_FOUND,
            'Could not find zone with identifier "right"'
        );
    }

    /**
     * @covers \Netgen\Bundle\BlockManagerBundle\Controller\API\V1\Layout\LoadZoneBlocks::__invoke
     */
    public function testLoadZoneBlocksWithNonExistentLayoutLocale(): void
    {
        $this->client->request(Request::METHOD_GET, '/bm/api/v1/unknown/layouts/1/zones/right/blocks');

        $this->assertException(
            $this->client->getResponse(),
            Response::HTTP_NOT_FOUND,
            'Could not find layout with identifier "1"'
        );
    }
}
