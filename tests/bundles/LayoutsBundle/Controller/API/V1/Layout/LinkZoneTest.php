<?php

declare(strict_types=1);

namespace Netgen\Bundle\LayoutsBundle\Tests\Controller\API\V1\Layout;

use Netgen\Bundle\LayoutsBundle\Tests\Controller\API\JsonApiTestCase;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

final class LinkZoneTest extends JsonApiTestCase
{
    /**
     * @covers \Netgen\Bundle\LayoutsBundle\Controller\API\V1\Layout\LinkZone::__construct
     * @covers \Netgen\Bundle\LayoutsBundle\Controller\API\V1\Layout\LinkZone::__invoke
     */
    public function testLinkZone(): void
    {
        $data = $this->jsonEncode(
            [
                'linked_layout_id' => '399ad9ac-777a-50ba-945a-06e9f57add12',
                'linked_zone_identifier' => 'right',
            ]
        );

        $this->client->request(
            Request::METHOD_POST,
            '/nglayouts/api/v1/layouts/81168ed3-86f9-55ea-b153-101f96f2c136/zones/right/link',
            [],
            [],
            [],
            $data
        );

        $this->assertEmptyResponse($this->client->getResponse());
    }

    /**
     * @covers \Netgen\Bundle\LayoutsBundle\Controller\API\V1\Layout\LinkZone::__invoke
     */
    public function testLinkZoneWithNonExistentZone(): void
    {
        $this->client->request(
            Request::METHOD_POST,
            '/nglayouts/api/v1/layouts/81168ed3-86f9-55ea-b153-101f96f2c136/zones/unknown/link',
            [],
            [],
            [],
            $this->jsonEncode([])
        );

        $this->assertException(
            $this->client->getResponse(),
            Response::HTTP_NOT_FOUND,
            'Could not find zone with identifier "unknown"'
        );
    }

    /**
     * @covers \Netgen\Bundle\LayoutsBundle\Controller\API\V1\Layout\LinkZone::__invoke
     */
    public function testLinkZoneWithNonExistentLayout(): void
    {
        $this->client->request(
            Request::METHOD_POST,
            '/nglayouts/api/v1/layouts/ffffffff-ffff-ffff-ffff-ffffffffffff/zones/right/link',
            [],
            [],
            [],
            $this->jsonEncode([])
        );

        $this->assertException(
            $this->client->getResponse(),
            Response::HTTP_NOT_FOUND,
            'Could not find layout with identifier "ffffffff-ffff-ffff-ffff-ffffffffffff"'
        );
    }

    /**
     * @covers \Netgen\Bundle\LayoutsBundle\Controller\API\V1\Layout\LinkZone::__invoke
     */
    public function testLinkZoneWithMissingLinkedLayoutId(): void
    {
        $data = $this->jsonEncode(
            [
                'linked_zone_identifier' => 'right',
            ]
        );

        $this->client->request(
            Request::METHOD_POST,
            '/nglayouts/api/v1/layouts/81168ed3-86f9-55ea-b153-101f96f2c136/zones/right/link',
            [],
            [],
            [],
            $data
        );

        $this->assertException(
            $this->client->getResponse(),
            Response::HTTP_BAD_REQUEST,
            'Invalid UUID string: '
        );
    }

    /**
     * @covers \Netgen\Bundle\LayoutsBundle\Controller\API\V1\Layout\LinkZone::__invoke
     */
    public function testLinkZoneWithInvalidLinkedLayoutId(): void
    {
        $data = $this->jsonEncode(
            [
                'linked_layout_id' => 42,
                'linked_zone_identifier' => 'right',
            ]
        );

        $this->client->request(
            Request::METHOD_POST,
            '/nglayouts/api/v1/layouts/81168ed3-86f9-55ea-b153-101f96f2c136/zones/right/link',
            [],
            [],
            [],
            $data
        );

        $this->assertException(
            $this->client->getResponse(),
            Response::HTTP_BAD_REQUEST,
            'Invalid UUID string: 42'
        );
    }

    /**
     * @covers \Netgen\Bundle\LayoutsBundle\Controller\API\V1\Layout\LinkZone::__invoke
     */
    public function testLinkZoneWithNonExistentLinkedZone(): void
    {
        $data = $this->jsonEncode(
            [
                'linked_layout_id' => '399ad9ac-777a-50ba-945a-06e9f57add12',
                'linked_zone_identifier' => 'unknown',
            ]
        );

        $this->client->request(
            Request::METHOD_POST,
            '/nglayouts/api/v1/layouts/81168ed3-86f9-55ea-b153-101f96f2c136/zones/right/link',
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
     * @covers \Netgen\Bundle\LayoutsBundle\Controller\API\V1\Layout\LinkZone::__invoke
     */
    public function testLinkZoneWithNonExistentLinkedLayout(): void
    {
        $data = $this->jsonEncode(
            [
                'linked_layout_id' => 'ffffffff-ffff-ffff-ffff-ffffffffffff',
                'linked_zone_identifier' => 'right',
            ]
        );

        $this->client->request(
            Request::METHOD_POST,
            '/nglayouts/api/v1/layouts/81168ed3-86f9-55ea-b153-101f96f2c136/zones/right/link',
            [],
            [],
            [],
            $data
        );

        $this->assertException(
            $this->client->getResponse(),
            Response::HTTP_NOT_FOUND,
            'Could not find layout with identifier "ffffffff-ffff-ffff-ffff-ffffffffffff"'
        );
    }

    /**
     * @covers \Netgen\Bundle\LayoutsBundle\Controller\API\V1\Layout\LinkZone::__invoke
     */
    public function testLinkZoneWithNonSharedLinkedLayout(): void
    {
        $data = $this->jsonEncode(
            [
                'linked_layout_id' => '71cbe281-430c-51d5-8e21-c3cc4e656dac',
                'linked_zone_identifier' => 'right',
            ]
        );

        $this->client->request(
            Request::METHOD_POST,
            '/nglayouts/api/v1/layouts/81168ed3-86f9-55ea-b153-101f96f2c136/zones/right/link',
            [],
            [],
            [],
            $data
        );

        $this->assertException(
            $this->client->getResponse(),
            Response::HTTP_UNPROCESSABLE_ENTITY,
            'Argument "linkedZone" has an invalid state. Linked zone is not in the shared layout.'
        );
    }
}
