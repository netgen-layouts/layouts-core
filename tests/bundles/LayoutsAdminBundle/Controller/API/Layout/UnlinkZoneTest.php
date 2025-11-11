<?php

declare(strict_types=1);

namespace Netgen\Bundle\LayoutsAdminBundle\Tests\Controller\API\Layout;

use Netgen\Bundle\LayoutsAdminBundle\Controller\API\Layout\UnlinkZone;
use Netgen\Bundle\LayoutsAdminBundle\Tests\Controller\API\JsonApiTestCase;
use PHPUnit\Framework\Attributes\CoversClass;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

#[CoversClass(UnlinkZone::class)]
final class UnlinkZoneTest extends JsonApiTestCase
{
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
