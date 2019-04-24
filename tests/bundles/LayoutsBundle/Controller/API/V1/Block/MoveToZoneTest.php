<?php

declare(strict_types=1);

namespace Netgen\Bundle\LayoutsBundle\Tests\Controller\API\V1\Block;

use Netgen\Bundle\LayoutsBundle\Tests\Controller\API\JsonApiTestCase;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

final class MoveToZoneTest extends JsonApiTestCase
{
    /**
     * @covers \Netgen\Bundle\LayoutsBundle\Controller\API\V1\Block\MoveToZone::__construct
     * @covers \Netgen\Bundle\LayoutsBundle\Controller\API\V1\Block\MoveToZone::__invoke
     */
    public function testMoveToZone(): void
    {
        $data = $this->jsonEncode(
            [
                'layout_id' => '81168ed3-86f9-55ea-b153-101f96f2c136',
                'zone_identifier' => 'left',
                'parent_position' => 0,
            ]
        );

        $this->client->request(
            Request::METHOD_POST,
            '/nglayouts/api/v1/en/blocks/28df256a-2467-5527-b398-9269ccc652de/move/zone',
            [],
            [],
            [],
            $data
        );

        $this->assertEmptyResponse($this->client->getResponse());
    }

    /**
     * @covers \Netgen\Bundle\LayoutsBundle\Controller\API\V1\Block\MoveToZone::__invoke
     */
    public function testMoveToZoneWithNonExistentBlock(): void
    {
        $data = $this->jsonEncode([]);

        $this->client->request(
            Request::METHOD_POST,
            '/nglayouts/api/v1/en/blocks/ffffffff-ffff-ffff-ffff-ffffffffffff/move/zone',
            [],
            [],
            [],
            $data
        );

        $this->assertException(
            $this->client->getResponse(),
            Response::HTTP_NOT_FOUND,
            'Could not find block with identifier "ffffffff-ffff-ffff-ffff-ffffffffffff"'
        );
    }

    /**
     * @covers \Netgen\Bundle\LayoutsBundle\Controller\API\V1\Block\MoveToZone::__invoke
     */
    public function testMoveToZoneWithNonExistentLayout(): void
    {
        $data = $this->jsonEncode(
            [
                'layout_id' => 'ffffffff-ffff-ffff-ffff-ffffffffffff',
                'zone_identifier' => 'left',
                'parent_position' => 1,
            ]
        );

        $this->client->request(
            Request::METHOD_POST,
            '/nglayouts/api/v1/en/blocks/28df256a-2467-5527-b398-9269ccc652de/move/zone',
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
     * @covers \Netgen\Bundle\LayoutsBundle\Controller\API\V1\Block\MoveToZone::__invoke
     */
    public function testMoveToZoneWithNonExistentZoneIdentifier(): void
    {
        $data = $this->jsonEncode(
            [
                'layout_id' => '81168ed3-86f9-55ea-b153-101f96f2c136',
                'zone_identifier' => 'unknown',
                'parent_position' => 1,
            ]
        );

        $this->client->request(
            Request::METHOD_POST,
            '/nglayouts/api/v1/en/blocks/28df256a-2467-5527-b398-9269ccc652de/move/zone',
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
     * @covers \Netgen\Bundle\LayoutsBundle\Controller\API\V1\Block\MoveToZone::__invoke
     */
    public function testMoveToZoneWithNotAllowedBlockDefinition(): void
    {
        $data = $this->jsonEncode(
            [
                'layout_id' => '81168ed3-86f9-55ea-b153-101f96f2c136',
                'zone_identifier' => 'top',
                'parent_position' => 0,
            ]
        );

        $this->client->request(
            Request::METHOD_POST,
            '/nglayouts/api/v1/en/blocks/28df256a-2467-5527-b398-9269ccc652de/move/zone',
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
     * @covers \Netgen\Bundle\LayoutsBundle\Controller\API\V1\Block\MoveToZone::__invoke
     */
    public function testMoveToZoneWithOutOfRangePosition(): void
    {
        $data = $this->jsonEncode(
            [
                'layout_id' => '81168ed3-86f9-55ea-b153-101f96f2c136',
                'zone_identifier' => 'left',
                'parent_position' => 9999,
            ]
        );

        $this->client->request(
            Request::METHOD_POST,
            '/nglayouts/api/v1/en/blocks/28df256a-2467-5527-b398-9269ccc652de/move/zone',
            [],
            [],
            [],
            $data
        );

        $this->assertException(
            $this->client->getResponse(),
            Response::HTTP_UNPROCESSABLE_ENTITY,
            'Argument "position" has an invalid state. Position is out of range.'
        );
    }

    /**
     * @covers \Netgen\Bundle\LayoutsBundle\Controller\API\V1\Block\MoveToZone::__invoke
     */
    public function testMoveToZoneWithInvalidLayoutId(): void
    {
        $data = $this->jsonEncode(
            [
                'layout_id' => 42,
                'zone_identifier' => 'left',
                'parent_position' => 1,
            ]
        );

        $this->client->request(
            Request::METHOD_POST,
            '/nglayouts/api/v1/en/blocks/28df256a-2467-5527-b398-9269ccc652de/move/zone',
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
     * @covers \Netgen\Bundle\LayoutsBundle\Controller\API\V1\Block\MoveToZone::__invoke
     */
    public function testMoveToZoneWithMissingLayoutId(): void
    {
        $data = $this->jsonEncode(
            [
                'zone_identifier' => 'left',
                'parent_position' => 1,
            ]
        );

        $this->client->request(
            Request::METHOD_POST,
            '/nglayouts/api/v1/en/blocks/28df256a-2467-5527-b398-9269ccc652de/move/zone',
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
}
