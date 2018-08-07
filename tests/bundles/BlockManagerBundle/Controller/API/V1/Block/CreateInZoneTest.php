<?php

declare(strict_types=1);

namespace Netgen\Bundle\BlockManagerBundle\Tests\Controller\API\V1\Block;

use Netgen\Bundle\BlockManagerBundle\Tests\Controller\API\JsonApiTestCase;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

final class CreateInZoneTest extends JsonApiTestCase
{
    /**
     * @covers \Netgen\Bundle\BlockManagerBundle\Controller\API\V1\Block\CreateInZone::__construct
     * @covers \Netgen\Bundle\BlockManagerBundle\Controller\API\V1\Block\CreateInZone::__invoke
     * @covers \Netgen\Bundle\BlockManagerBundle\Controller\API\V1\Block\Utils\CreateStructBuilder::buildCreateStruct
     * @covers \Netgen\Bundle\BlockManagerBundle\Controller\API\V1\Block\Utils\CreateStructValidator::validateCreateBlock
     */
    public function testCreateInZone(): void
    {
        $data = $this->jsonEncode(
            [
                'block_type' => 'list',
                'layout_id' => 1,
                'zone_identifier' => 'bottom',
                'parent_position' => 0,
            ]
        );

        $this->client->request(
            Request::METHOD_POST,
            '/bm/api/v1/en/blocks?html=false',
            [],
            [],
            [],
            $data
        );

        self::assertResponse(
            $this->client->getResponse(),
            'v1/blocks/create_block_in_zone',
            Response::HTTP_CREATED
        );
    }

    /**
     * @covers \Netgen\Bundle\BlockManagerBundle\Controller\API\V1\Block\CreateInZone::__invoke
     * @covers \Netgen\Bundle\BlockManagerBundle\Controller\API\V1\Block\Utils\CreateStructBuilder::buildCreateStruct
     * @covers \Netgen\Bundle\BlockManagerBundle\Controller\API\V1\Block\Utils\CreateStructValidator::validateCreateBlock
     */
    public function testCreateInZoneWithNoPosition(): void
    {
        $data = $this->jsonEncode(
            [
                'block_type' => 'list',
                'layout_id' => 1,
                'zone_identifier' => 'right',
            ]
        );

        $this->client->request(
            Request::METHOD_POST,
            '/bm/api/v1/en/blocks?html=false',
            [],
            [],
            [],
            $data
        );

        self::assertResponse(
            $this->client->getResponse(),
            'v1/blocks/create_block_in_zone_at_end',
            Response::HTTP_CREATED
        );
    }

    /**
     * @covers \Netgen\Bundle\BlockManagerBundle\Controller\API\V1\Block\CreateInZone::__invoke
     * @covers \Netgen\Bundle\BlockManagerBundle\Controller\API\V1\Block\Utils\CreateStructValidator::validateCreateBlock
     */
    public function testCreateInZoneWithInvalidBlockType(): void
    {
        $data = $this->jsonEncode(
            [
                'block_type' => 42,
                'layout_id' => 1,
                'zone_identifier' => 'bottom',
                'parent_position' => 0,
            ]
        );

        $this->client->request(
            Request::METHOD_POST,
            '/bm/api/v1/en/blocks',
            [],
            [],
            [],
            $data
        );

        $this->assertException(
            $this->client->getResponse(),
            Response::HTTP_BAD_REQUEST,
            'There was an error validating "block_type": This value should be of type string.'
        );
    }

    /**
     * @covers \Netgen\Bundle\BlockManagerBundle\Controller\API\V1\Block\CreateInZone::__invoke
     * @covers \Netgen\Bundle\BlockManagerBundle\Controller\API\V1\Block\Utils\CreateStructValidator::validateCreateBlock
     */
    public function testCreateInZoneWithMissingBlockType(): void
    {
        $data = $this->jsonEncode(
            [
                'layout_id' => 1,
                'zone_identifier' => 'bottom',
                'parent_position' => 0,
            ]
        );

        $this->client->request(
            Request::METHOD_POST,
            '/bm/api/v1/en/blocks',
            [],
            [],
            [],
            $data
        );

        $this->assertException(
            $this->client->getResponse(),
            Response::HTTP_BAD_REQUEST,
            'There was an error validating "block_type": This value should not be blank.'
        );
    }

    /**
     * @covers \Netgen\Bundle\BlockManagerBundle\Controller\API\V1\Block\CreateInZone::__invoke
     * @covers \Netgen\Bundle\BlockManagerBundle\Controller\API\V1\Block\Utils\CreateStructValidator::validateCreateBlock
     */
    public function testCreateInZoneWithInvalidLayoutId(): void
    {
        $data = $this->jsonEncode(
            [
                'block_type' => 'title',
                'layout_id' => [42],
                'zone_identifier' => 'bottom',
                'parent_position' => 0,
            ]
        );

        $this->client->request(
            Request::METHOD_POST,
            '/bm/api/v1/en/blocks',
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
     * @covers \Netgen\Bundle\BlockManagerBundle\Controller\API\V1\Block\CreateInZone::__invoke
     * @covers \Netgen\Bundle\BlockManagerBundle\Controller\API\V1\Block\Utils\CreateStructValidator::validateCreateBlock
     */
    public function testCreateInZoneWithMissingLayoutId(): void
    {
        $data = $this->jsonEncode(
            [
                'block_type' => 'title',
                'zone_identifier' => 'bottom',
                'parent_position' => 0,
            ]
        );

        $this->client->request(
            Request::METHOD_POST,
            '/bm/api/v1/en/blocks',
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
     * @covers \Netgen\Bundle\BlockManagerBundle\Controller\API\V1\Block\CreateInZone::__invoke
     * @covers \Netgen\Bundle\BlockManagerBundle\Controller\API\V1\Block\Utils\CreateStructValidator::validateCreateBlock
     */
    public function testCreateInZoneWithNonExistentBlockType(): void
    {
        $data = $this->jsonEncode(
            [
                'block_type' => 'unknown',
                'layout_id' => 1,
                'zone_identifier' => 'bottom',
                'parent_position' => 0,
            ]
        );

        $this->client->request(
            Request::METHOD_POST,
            '/bm/api/v1/en/blocks',
            [],
            [],
            [],
            $data
        );

        $this->assertException(
            $this->client->getResponse(),
            Response::HTTP_UNPROCESSABLE_ENTITY,
            'Argument "block_type" has an invalid state. Block type does not exist.'
        );
    }

    /**
     * @covers \Netgen\Bundle\BlockManagerBundle\Controller\API\V1\Block\CreateInZone::__invoke
     * @covers \Netgen\Bundle\BlockManagerBundle\Controller\API\V1\Block\Utils\CreateStructValidator::validateCreateBlock
     */
    public function testCreateInZoneWithNonExistentLayout(): void
    {
        $data = $this->jsonEncode(
            [
                'block_type' => 'title',
                'layout_id' => 9999,
                'zone_identifier' => 'bottom',
                'parent_position' => 0,
            ]
        );

        $this->client->request(
            Request::METHOD_POST,
            '/bm/api/v1/en/blocks',
            [],
            [],
            [],
            $data
        );

        $this->assertException(
            $this->client->getResponse(),
            Response::HTTP_NOT_FOUND,
            'Could not find zone with identifier "bottom"'
        );
    }

    /**
     * @covers \Netgen\Bundle\BlockManagerBundle\Controller\API\V1\Block\CreateInZone::__invoke
     * @covers \Netgen\Bundle\BlockManagerBundle\Controller\API\V1\Block\Utils\CreateStructValidator::validateCreateBlock
     */
    public function testCreateInZoneWithNonExistentLayoutZone(): void
    {
        $data = $this->jsonEncode(
            [
                'block_type' => 'title',
                'layout_id' => 1,
                'zone_identifier' => 'unknown',
                'parent_position' => 0,
            ]
        );

        $this->client->request(
            Request::METHOD_POST,
            '/bm/api/v1/en/blocks',
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
     * @covers \Netgen\Bundle\BlockManagerBundle\Controller\API\V1\Block\CreateInZone::__invoke
     * @covers \Netgen\Bundle\BlockManagerBundle\Controller\API\V1\Block\Utils\CreateStructValidator::validateCreateBlock
     */
    public function testCreateInZoneWithOutOfRangePosition(): void
    {
        $data = $this->jsonEncode(
            [
                'block_type' => 'title',
                'layout_id' => 1,
                'zone_identifier' => 'bottom',
                'parent_position' => 9999,
            ]
        );

        $this->client->request(
            Request::METHOD_POST,
            '/bm/api/v1/en/blocks',
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
     * @covers \Netgen\Bundle\BlockManagerBundle\Controller\API\V1\Block\CreateInZone::__invoke
     * @covers \Netgen\Bundle\BlockManagerBundle\Controller\API\V1\Block\Utils\CreateStructValidator::validateCreateBlock
     */
    public function testCreateInZoneWithNotAllowedBlockDefinition(): void
    {
        $data = $this->jsonEncode(
            [
                'block_type' => 'list',
                'layout_id' => 1,
                'zone_identifier' => 'top',
                'parent_position' => 0,
            ]
        );

        $this->client->request(
            Request::METHOD_POST,
            '/bm/api/v1/en/blocks',
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
}
