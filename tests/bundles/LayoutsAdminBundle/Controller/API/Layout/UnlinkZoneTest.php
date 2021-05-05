<?php

declare(strict_types=1);

namespace Netgen\Bundle\LayoutsAdminBundle\Tests\Controller\API\Layout;

use Netgen\Bundle\LayoutsAdminBundle\Tests\Controller\API\JsonApiTestCase;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

final class UnlinkZoneTest extends JsonApiTestCase
{
    /**
     * @covers \Netgen\Bundle\LayoutsAdminBundle\Controller\API\Layout\UnlinkZone::__construct
     * @covers \Netgen\Bundle\LayoutsAdminBundle\Controller\API\Layout\UnlinkZone::__invoke
     */
    public function testUnlinkZone(): void
    {
        $this->client->request(
            Request::METHOD_DELETE,
            '/nglayouts/app/api/layouts/81168ed3-86f9-55ea-b153-101f96f2c136/zones/right/link',
            [],
            [],
            [],
            $this->jsonEncode([]),
        );

        $this->assertEmptyResponse($this->client->getResponse());
    }

    /**
     * @covers \Netgen\Bundle\LayoutsAdminBundle\Controller\API\Layout\UnlinkZone::__invoke
     */
    public function testUnlinkZoneWithNonExistentZone(): void
    {
        $this->client->request(
            Request::METHOD_DELETE,
            '/nglayouts/app/api/layouts/81168ed3-86f9-55ea-b153-101f96f2c136/zones/unknown/link',
            [],
            [],
            [],
            $this->jsonEncode([]),
        );

        $this->assertException(
            $this->client->getResponse(),
            Response::HTTP_NOT_FOUND,
            'Could not find zone with identifier "unknown"',
        );
    }

    /**
     * @covers \Netgen\Bundle\LayoutsAdminBundle\Controller\API\Layout\UnlinkZone::__invoke
     */
    public function testUnlinkZoneWithNonExistentLayout(): void
    {
        $this->client->request(
            Request::METHOD_DELETE,
            '/nglayouts/app/api/layouts/ffffffff-ffff-ffff-ffff-ffffffffffff/zones/right/link',
            [],
            [],
            [],
            $this->jsonEncode([]),
        );

        $this->assertException(
            $this->client->getResponse(),
            Response::HTTP_NOT_FOUND,
            'Could not find layout with identifier "ffffffff-ffff-ffff-ffff-ffffffffffff"',
        );
    }
}
