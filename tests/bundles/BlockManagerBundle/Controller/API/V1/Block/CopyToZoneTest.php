<?php

namespace Netgen\Bundle\BlockManagerBundle\Tests\Controller\API\V1\Block;

use Netgen\Bundle\BlockManagerBundle\Tests\Controller\API\JsonApiTestCase;
use Symfony\Component\HttpFoundation\Response;

final class CopyToZoneTest extends JsonApiTestCase
{
    /**
     * @covers \Netgen\Bundle\BlockManagerBundle\Controller\API\V1\Block\CopyToZone::__construct
     * @covers \Netgen\Bundle\BlockManagerBundle\Controller\API\V1\Block\CopyToZone::__invoke
     */
    public function testCopyToZone()
    {
        $data = $this->jsonEncode(
            [
                'layout_id' => 1,
                'zone_identifier' => 'left',
            ]
        );

        $this->client->request(
            'POST',
            '/bm/api/v1/en/blocks/31/copy/zone?html=false',
            [],
            [],
            [],
            $data
        );

        $this->assertResponse(
            $this->client->getResponse(),
            'v1/blocks/copy_block_to_zone',
            Response::HTTP_CREATED
        );
    }

    /**
     * @covers \Netgen\Bundle\BlockManagerBundle\Controller\API\V1\Block\CopyToZone::__invoke
     */
    public function testCopyToZoneWithNonExistentBlock()
    {
        $data = $this->jsonEncode([]);

        $this->client->request(
            'POST',
            '/bm/api/v1/en/blocks/9999/copy/zone',
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
     * @covers \Netgen\Bundle\BlockManagerBundle\Controller\API\V1\Block\CopyToZone::__invoke
     */
    public function testCopyToZoneWithNonExistentLayout()
    {
        $data = $this->jsonEncode(
            [
                'layout_id' => 9999,
                'zone_identifier' => 'left',
            ]
        );

        $this->client->request(
            'POST',
            '/bm/api/v1/en/blocks/31/copy/zone',
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
     * @covers \Netgen\Bundle\BlockManagerBundle\Controller\API\V1\Block\CopyToZone::__invoke
     */
    public function testCopyToZoneWithNonExistentZone()
    {
        $data = $this->jsonEncode(
            [
                'layout_id' => 1,
                'zone_identifier' => 'unknown',
            ]
        );

        $this->client->request(
            'POST',
            '/bm/api/v1/en/blocks/31/copy/zone',
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
     * @covers \Netgen\Bundle\BlockManagerBundle\Controller\API\V1\Block\CopyToZone::__invoke
     */
    public function testCopyToZoneWithNotAllowedBlockDefinition()
    {
        $data = $this->jsonEncode(
            [
                'layout_id' => 1,
                'zone_identifier' => 'top',
            ]
        );

        $this->client->request(
            'POST',
            '/bm/api/v1/en/blocks/31/copy/zone',
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
     * @covers \Netgen\Bundle\BlockManagerBundle\Controller\API\V1\Block\CopyToZone::__invoke
     */
    public function testCopyToZoneWithInvalidLayoutId()
    {
        $data = $this->jsonEncode(
            [
                'layout_id' => [42],
                'zone_identifier' => 'left',
            ]
        );

        $this->client->request(
            'POST',
            '/bm/api/v1/en/blocks/31/copy/zone',
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
     * @covers \Netgen\Bundle\BlockManagerBundle\Controller\API\V1\Block\CopyToZone::__invoke
     */
    public function testCopyToZoneWithInvalidZoneIdentifier()
    {
        $data = $this->jsonEncode(
            [
                'layout_id' => 1,
                'zone_identifier' => 42,
            ]
        );

        $this->client->request(
            'POST',
            '/bm/api/v1/en/blocks/31/copy/zone',
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
     * @covers \Netgen\Bundle\BlockManagerBundle\Controller\API\V1\Block\CopyToZone::__invoke
     */
    public function testCopyToZoneWithMissingLayoutId()
    {
        $data = $this->jsonEncode(
            [
                'zone_identifier' => 'left',
            ]
        );

        $this->client->request(
            'POST',
            '/bm/api/v1/en/blocks/31/copy/zone',
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
     * @covers \Netgen\Bundle\BlockManagerBundle\Controller\API\V1\Block\CopyToZone::__invoke
     */
    public function testCopyToZoneWithMissingZoneIdentifier()
    {
        $data = $this->jsonEncode(
            [
                'layout_id' => 1,
            ]
        );

        $this->client->request(
            'POST',
            '/bm/api/v1/en/blocks/31/copy/zone',
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
}
