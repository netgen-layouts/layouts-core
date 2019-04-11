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
                'linked_layout_id' => 5,
                'linked_zone_identifier' => 'right',
            ]
        );

        $this->client->request(
            Request::METHOD_POST,
            '/bm/api/v1/layouts/1/zones/right/link',
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
        $data = $this->jsonEncode([]);

        $this->client->request(
            Request::METHOD_POST,
            '/bm/api/v1/layouts/1/zones/unknown/link',
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
    public function testLinkZoneWithNonExistentLayout(): void
    {
        $data = $this->jsonEncode([]);

        $this->client->request(
            Request::METHOD_POST,
            '/bm/api/v1/layouts/9999/zones/right/link',
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
            '/bm/api/v1/layouts/1/zones/right/link',
            [],
            [],
            [],
            $data
        );

        $this->assertException(
            $this->client->getResponse(),
            Response::HTTP_BAD_REQUEST,
            'There was an error validating "layoutId": This value should not be blank.'
        );
    }

    /**
     * @covers \Netgen\Bundle\LayoutsBundle\Controller\API\V1\Layout\LinkZone::__invoke
     */
    public function testLinkZoneWithInvalidLinkedLayoutId(): void
    {
        $data = $this->jsonEncode(
            [
                'linked_layout_id' => [42],
                'linked_zone_identifier' => 'right',
            ]
        );

        $this->client->request(
            Request::METHOD_POST,
            '/bm/api/v1/layouts/1/zones/right/link',
            [],
            [],
            [],
            $data
        );

        $this->assertException(
            $this->client->getResponse(),
            Response::HTTP_BAD_REQUEST,
            'There was an error validating "layoutId": This value should be of type scalar.'
        );
    }

    /**
     * @covers \Netgen\Bundle\LayoutsBundle\Controller\API\V1\Layout\LinkZone::__invoke
     */
    public function testLinkZoneWithNonExistentLinkedZone(): void
    {
        $data = $this->jsonEncode(
            [
                'linked_layout_id' => 5,
                'linked_zone_identifier' => 'unknown',
            ]
        );

        $this->client->request(
            Request::METHOD_POST,
            '/bm/api/v1/layouts/1/zones/right/link',
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
                'linked_layout_id' => 9999,
                'linked_zone_identifier' => 'right',
            ]
        );

        $this->client->request(
            Request::METHOD_POST,
            '/bm/api/v1/layouts/1/zones/right/link',
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

    /**
     * @covers \Netgen\Bundle\LayoutsBundle\Controller\API\V1\Layout\LinkZone::__invoke
     */
    public function testLinkZoneWithNonSharedLinkedLayout(): void
    {
        $data = $this->jsonEncode(
            [
                'linked_layout_id' => 2,
                'linked_zone_identifier' => 'right',
            ]
        );

        $this->client->request(
            Request::METHOD_POST,
            '/bm/api/v1/layouts/1/zones/right/link',
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
