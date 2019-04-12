<?php

declare(strict_types=1);

namespace Netgen\Bundle\LayoutsBundle\Tests\Controller\API\V1\Block;

use Netgen\Bundle\LayoutsBundle\Tests\Controller\API\JsonApiTestCase;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

final class CopyToZoneTest extends JsonApiTestCase
{
    /**
     * @covers \Netgen\Bundle\LayoutsBundle\Controller\API\V1\Block\CopyToZone::__construct
     * @covers \Netgen\Bundle\LayoutsBundle\Controller\API\V1\Block\CopyToZone::__invoke
     */
    public function testCopyToZone(): void
    {
        $data = $this->jsonEncode(
            [
                'layout_id' => 1,
                'zone_identifier' => 'left',
            ]
        );

        $this->client->request(
            Request::METHOD_POST,
            '/nglayouts/api/v1/en/blocks/31/copy/zone?html=false',
            [],
            [],
            [],
            $data
        );

        self::assertResponse(
            $this->client->getResponse(),
            'v1/blocks/copy_block_to_zone',
            Response::HTTP_CREATED
        );
    }

    /**
     * @covers \Netgen\Bundle\LayoutsBundle\Controller\API\V1\Block\CopyToZone::__invoke
     */
    public function testCopyToZoneWithNonExistentBlock(): void
    {
        $data = $this->jsonEncode([]);

        $this->client->request(
            Request::METHOD_POST,
            '/nglayouts/api/v1/en/blocks/9999/copy/zone',
            [],
            [],
            [],
            $data
        );

        $this->assertException(
            $this->client->getResponse(),
            Response::HTTP_NOT_FOUND,
            'Could not find block with identifier "9999"'
        );
    }

    /**
     * @covers \Netgen\Bundle\LayoutsBundle\Controller\API\V1\Block\CopyToZone::__invoke
     */
    public function testCopyToZoneWithNonExistentLayout(): void
    {
        $data = $this->jsonEncode(
            [
                'layout_id' => 9999,
                'zone_identifier' => 'left',
            ]
        );

        $this->client->request(
            Request::METHOD_POST,
            '/nglayouts/api/v1/en/blocks/31/copy/zone',
            [],
            [],
            [],
            $data
        );

        $this->assertException(
            $this->client->getResponse(),
            Response::HTTP_NOT_FOUND,
            'Could not find zone with identifier "left"'
        );
    }

    /**
     * @covers \Netgen\Bundle\LayoutsBundle\Controller\API\V1\Block\CopyToZone::__invoke
     */
    public function testCopyToZoneWithNonExistentZone(): void
    {
        $data = $this->jsonEncode(
            [
                'layout_id' => 1,
                'zone_identifier' => 'unknown',
            ]
        );

        $this->client->request(
            Request::METHOD_POST,
            '/nglayouts/api/v1/en/blocks/31/copy/zone',
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
     * @covers \Netgen\Bundle\LayoutsBundle\Controller\API\V1\Block\CopyToZone::__invoke
     */
    public function testCopyToZoneWithNotAllowedBlockDefinition(): void
    {
        $data = $this->jsonEncode(
            [
                'layout_id' => 1,
                'zone_identifier' => 'top',
            ]
        );

        $this->client->request(
            Request::METHOD_POST,
            '/nglayouts/api/v1/en/blocks/31/copy/zone',
            [],
            [],
            [],
            $data
        );

        $this->assertException(
            $this->client->getResponse(),
            Response::HTTP_UNPROCESSABLE_ENTITY,
            'Argument "zone" has an invalid state. Block is not allowed in specified zone.'
        );
    }

    /**
     * @covers \Netgen\Bundle\LayoutsBundle\Controller\API\V1\Block\CopyToZone::__invoke
     */
    public function testCopyToZoneWithInvalidLayoutId(): void
    {
        $data = $this->jsonEncode(
            [
                'layout_id' => [42],
                'zone_identifier' => 'left',
            ]
        );

        $this->client->request(
            Request::METHOD_POST,
            '/nglayouts/api/v1/en/blocks/31/copy/zone',
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
     * @covers \Netgen\Bundle\LayoutsBundle\Controller\API\V1\Block\CopyToZone::__invoke
     */
    public function testCopyToZoneWithMissingLayoutId(): void
    {
        $data = $this->jsonEncode(
            [
                'zone_identifier' => 'left',
            ]
        );

        $this->client->request(
            Request::METHOD_POST,
            '/nglayouts/api/v1/en/blocks/31/copy/zone',
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
}
