<?php

namespace Netgen\Bundle\BlockManagerBundle\Tests\Controller\API\V1\Block;

use Netgen\Bundle\BlockManagerBundle\Tests\Controller\API\JsonApiTestCase;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

final class MoveToZoneTest extends JsonApiTestCase
{
    /**
     * @covers \Netgen\Bundle\BlockManagerBundle\Controller\API\V1\Block\MoveToZone::__construct
     * @covers \Netgen\Bundle\BlockManagerBundle\Controller\API\V1\Block\MoveToZone::__invoke
     */
    public function testMoveToZone()
    {
        $data = $this->jsonEncode(
            [
                'layout_id' => 1,
                'zone_identifier' => 'left',
                'position' => 0,
            ]
        );

        $this->client->request(
            Request::METHOD_POST,
            '/bm/api/v1/en/blocks/31/move/zone',
            [],
            [],
            [],
            $data
        );

        $this->assertEmptyResponse($this->client->getResponse());
    }

    /**
     * @covers \Netgen\Bundle\BlockManagerBundle\Controller\API\V1\Block\MoveToZone::__invoke
     */
    public function testMoveToZoneWithNonExistentBlock()
    {
        $data = $this->jsonEncode([]);

        $this->client->request(
            Request::METHOD_POST,
            '/bm/api/v1/en/blocks/9999/move/zone',
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
     * @covers \Netgen\Bundle\BlockManagerBundle\Controller\API\V1\Block\MoveToZone::__invoke
     */
    public function testMoveToZoneWithNonExistentLayout()
    {
        $data = $this->jsonEncode(
            [
                'layout_id' => 9999,
                'zone_identifier' => 'left',
                'position' => 1,
            ]
        );

        $this->client->request(
            Request::METHOD_POST,
            '/bm/api/v1/en/blocks/31/move/zone',
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
     * @covers \Netgen\Bundle\BlockManagerBundle\Controller\API\V1\Block\MoveToZone::__invoke
     */
    public function testMoveToZoneWithNonExistentZoneIdentifier()
    {
        $data = $this->jsonEncode(
            [
                'layout_id' => 1,
                'zone_identifier' => 'unknown',
                'position' => 1,
            ]
        );

        $this->client->request(
            Request::METHOD_POST,
            '/bm/api/v1/en/blocks/31/move/zone',
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
     * @covers \Netgen\Bundle\BlockManagerBundle\Controller\API\V1\Block\MoveToZone::__invoke
     */
    public function testMoveToZoneWithNotAllowedBlockDefinition()
    {
        $data = $this->jsonEncode(
            [
                'layout_id' => 1,
                'zone_identifier' => 'top',
                'position' => 0,
            ]
        );

        $this->client->request(
            Request::METHOD_POST,
            '/bm/api/v1/en/blocks/31/move/zone',
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
     * @covers \Netgen\Bundle\BlockManagerBundle\Controller\API\V1\Block\MoveToZone::__invoke
     */
    public function testMoveToZoneWithOutOfRangePosition()
    {
        $data = $this->jsonEncode(
            [
                'layout_id' => 1,
                'zone_identifier' => 'left',
                'position' => 9999,
            ]
        );

        $this->client->request(
            Request::METHOD_POST,
            '/bm/api/v1/en/blocks/31/move/zone',
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
     * @covers \Netgen\Bundle\BlockManagerBundle\Controller\API\V1\Block\MoveToZone::__invoke
     */
    public function testMoveToZoneWithInvalidLayoutId()
    {
        $data = $this->jsonEncode(
            [
                'layout_id' => [42],
                'zone_identifier' => 'left',
                'position' => 1,
            ]
        );

        $this->client->request(
            Request::METHOD_POST,
            '/bm/api/v1/en/blocks/31/move/zone',
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
     * @covers \Netgen\Bundle\BlockManagerBundle\Controller\API\V1\Block\MoveToZone::__invoke
     */
    public function testMoveToZoneWithInvalidZoneIdentifier()
    {
        $data = $this->jsonEncode(
            [
                'layout_id' => 1,
                'zone_identifier' => 42,
                'position' => 1,
            ]
        );

        $this->client->request(
            Request::METHOD_POST,
            '/bm/api/v1/en/blocks/31/move/zone',
            [],
            [],
            [],
            $data
        );

        $this->assertException(
            $this->client->getResponse(),
            Response::HTTP_BAD_REQUEST,
            'There was an error validating "identifier": This value should be of type string.'
        );
    }

    /**
     * @covers \Netgen\Bundle\BlockManagerBundle\Controller\API\V1\Block\MoveToZone::__invoke
     */
    public function testMoveToZoneWithInvalidPosition()
    {
        $data = $this->jsonEncode(
            [
                'layout_id' => 1,
                'zone_identifier' => 'left',
                'position' => '1',
            ]
        );

        $this->client->request(
            Request::METHOD_POST,
            '/bm/api/v1/en/blocks/31/move/zone',
            [],
            [],
            [],
            $data
        );

        $this->assertException(
            $this->client->getResponse(),
            Response::HTTP_BAD_REQUEST,
            'There was an error validating "position": This value should be of type int.'
        );
    }

    /**
     * @covers \Netgen\Bundle\BlockManagerBundle\Controller\API\V1\Block\MoveToZone::__invoke
     */
    public function testMoveToZoneWithMissingLayoutId()
    {
        $data = $this->jsonEncode(
            [
                'zone_identifier' => 'left',
                'position' => 1,
            ]
        );

        $this->client->request(
            Request::METHOD_POST,
            '/bm/api/v1/en/blocks/31/move/zone',
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
     * @covers \Netgen\Bundle\BlockManagerBundle\Controller\API\V1\Block\MoveToZone::__invoke
     */
    public function testMoveToZoneWithMissingZoneIdentifier()
    {
        $data = $this->jsonEncode(
            [
                'layout_id' => 1,
                'position' => 1,
            ]
        );

        $this->client->request(
            Request::METHOD_POST,
            '/bm/api/v1/en/blocks/31/move/zone',
            [],
            [],
            [],
            $data
        );

        $this->assertException(
            $this->client->getResponse(),
            Response::HTTP_BAD_REQUEST,
            'There was an error validating "identifier": This value should not be blank.'
        );
    }

    /**
     * @covers \Netgen\Bundle\BlockManagerBundle\Controller\API\V1\Block\MoveToZone::__invoke
     */
    public function testMoveToZoneWithMissingPosition()
    {
        $data = $this->jsonEncode(
            [
                'layout_id' => 1,
                'zone_identifier' => 'left',
            ]
        );

        $this->client->request(
            Request::METHOD_POST,
            '/bm/api/v1/en/blocks/31/move/zone',
            [],
            [],
            [],
            $data
        );

        $this->assertException(
            $this->client->getResponse(),
            Response::HTTP_BAD_REQUEST,
            'There was an error validating "position": This value should not be blank.'
        );
    }
}
