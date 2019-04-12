<?php

declare(strict_types=1);

namespace Netgen\Bundle\LayoutsBundle\Tests\Controller\API\V1\Layout;

use Netgen\Bundle\LayoutsBundle\Tests\Controller\API\JsonApiTestCase;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

final class UnlinkZoneTest extends JsonApiTestCase
{
    /**
     * @covers \Netgen\Bundle\LayoutsBundle\Controller\API\V1\Layout\UnlinkZone::__construct
     * @covers \Netgen\Bundle\LayoutsBundle\Controller\API\V1\Layout\UnlinkZone::__invoke
     */
    public function testUnlinkZone(): void
    {
        $data = $this->jsonEncode([]);

        $this->client->request(
            Request::METHOD_DELETE,
            '/nglayouts/api/v1/layouts/1/zones/right/link',
            [],
            [],
            [],
            $data
        );

        $this->assertEmptyResponse($this->client->getResponse());
    }

    /**
     * @covers \Netgen\Bundle\LayoutsBundle\Controller\API\V1\Layout\UnlinkZone::__invoke
     */
    public function testUnlinkZoneWithNonExistentZone(): void
    {
        $data = $this->jsonEncode([]);

        $this->client->request(
            Request::METHOD_DELETE,
            '/nglayouts/api/v1/layouts/1/zones/unknown/link',
            [],
            [],
            [],
            $data
        );

        $this->assertException(
            $this->client->getResponse(),
            Response::HTTP_NOT_FOUND,
            'Could not find zone with identifier "unknown"'
        );
    }

    /**
     * @covers \Netgen\Bundle\LayoutsBundle\Controller\API\V1\Layout\UnlinkZone::__invoke
     */
    public function testUnlinkZoneWithNonExistentLayout(): void
    {
        $data = $this->jsonEncode([]);

        $this->client->request(
            Request::METHOD_DELETE,
            '/nglayouts/api/v1/layouts/9999/zones/right/link',
            [],
            [],
            [],
            $data
        );

        $this->assertException(
            $this->client->getResponse(),
            Response::HTTP_NOT_FOUND,
            'Could not find zone with identifier "right"'
        );
    }
}
