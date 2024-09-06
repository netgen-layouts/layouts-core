<?php

declare(strict_types=1);

namespace Netgen\Bundle\LayoutsAdminBundle\Tests\Controller\API\Block;

use Netgen\Bundle\LayoutsAdminBundle\Tests\Controller\API\JsonApiTestCase;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

final class CreateInZoneTest extends JsonApiTestCase
{
    /**
     * @covers \Netgen\Bundle\LayoutsAdminBundle\Controller\API\Block\CreateInZone::__construct
     * @covers \Netgen\Bundle\LayoutsAdminBundle\Controller\API\Block\CreateInZone::__invoke
     * @covers \Netgen\Bundle\LayoutsAdminBundle\Controller\API\Block\Utils\CreateStructBuilder::buildCreateStruct
     */
    public function testCreateInZone(): void
    {
        $data = $this->jsonEncode(
            [
                'block_type' => 'list',
                'layout_id' => '81168ed3-86f9-55ea-b153-101f96f2c136',
                'zone_identifier' => 'bottom',
                'parent_position' => 0,
            ],
        );

        $this->client->request(
            Request::METHOD_POST,
            '/nglayouts/app/api/en/blocks?html=false',
            [],
            [],
            [],
            $data,
        );

        $this->assertResponse(
            $this->client->getResponse(),
            'blocks/create_block_in_zone',
            Response::HTTP_CREATED,
        );
    }

    /**
     * @covers \Netgen\Bundle\LayoutsAdminBundle\Controller\API\Block\CreateInZone::__invoke
     * @covers \Netgen\Bundle\LayoutsAdminBundle\Controller\API\Block\Utils\CreateStructBuilder::buildCreateStruct
     */
    public function testCreateInZoneWithNoPosition(): void
    {
        $data = $this->jsonEncode(
            [
                'block_type' => 'list',
                'layout_id' => '81168ed3-86f9-55ea-b153-101f96f2c136',
                'zone_identifier' => 'right',
            ],
        );

        $this->client->request(
            Request::METHOD_POST,
            '/nglayouts/app/api/en/blocks?html=false',
            [],
            [],
            [],
            $data,
        );

        $this->assertResponse(
            $this->client->getResponse(),
            'blocks/create_block_in_zone_at_end',
            Response::HTTP_CREATED,
        );
    }

    /**
     * @covers \Netgen\Bundle\LayoutsAdminBundle\Controller\API\Block\CreateInZone::__invoke
     */
    public function testCreateInZoneWithInvalidLayoutId(): void
    {
        $data = $this->jsonEncode(
            [
                'block_type' => 'title',
                'layout_id' => 42,
                'zone_identifier' => 'bottom',
                'parent_position' => 0,
            ],
        );

        $this->client->request(
            Request::METHOD_POST,
            '/nglayouts/app/api/en/blocks',
            [],
            [],
            [],
            $data,
        );

        $this->assertException(
            $this->client->getResponse(),
            Response::HTTP_BAD_REQUEST,
            '/^There was an error validating "layout_id": This (value )?is not a valid UUID.$/',
        );
    }

    /**
     * @covers \Netgen\Bundle\LayoutsAdminBundle\Controller\API\Block\CreateInZone::__invoke
     */
    public function testCreateInZoneWithMissingLayoutId(): void
    {
        $data = $this->jsonEncode(
            [
                'block_type' => 'title',
                'zone_identifier' => 'bottom',
                'parent_position' => 0,
            ],
        );

        $this->client->request(
            Request::METHOD_POST,
            '/nglayouts/app/api/en/blocks',
            [],
            [],
            [],
            $data,
        );

        $this->assertException(
            $this->client->getResponse(),
            Response::HTTP_BAD_REQUEST,
            'There was an error validating "layout_id": This value should not be blank.',
        );
    }

    /**
     * @covers \Netgen\Bundle\LayoutsAdminBundle\Controller\API\Block\CreateInZone::__invoke
     */
    public function testCreateInZoneWithNonExistentBlockType(): void
    {
        $data = $this->jsonEncode(
            [
                'block_type' => 'unknown',
                'layout_id' => '81168ed3-86f9-55ea-b153-101f96f2c136',
                'zone_identifier' => 'bottom',
                'parent_position' => 0,
            ],
        );

        $this->client->request(
            Request::METHOD_POST,
            '/nglayouts/app/api/en/blocks',
            [],
            [],
            [],
            $data,
        );

        $this->assertException(
            $this->client->getResponse(),
            Response::HTTP_UNPROCESSABLE_ENTITY,
            'Argument "block_type" has an invalid state. Block type does not exist.',
        );
    }

    /**
     * @covers \Netgen\Bundle\LayoutsAdminBundle\Controller\API\Block\CreateInZone::__invoke
     */
    public function testCreateInZoneWithNonExistentLayout(): void
    {
        $data = $this->jsonEncode(
            [
                'block_type' => 'title',
                // This is a random UUID.
                'layout_id' => '7418fe82-a082-48ec-b156-03904819c8eb',
                'zone_identifier' => 'bottom',
                'parent_position' => 0,
            ],
        );

        $this->client->request(
            Request::METHOD_POST,
            '/nglayouts/app/api/en/blocks',
            [],
            [],
            [],
            $data,
        );

        $this->assertException(
            $this->client->getResponse(),
            Response::HTTP_NOT_FOUND,
            'Could not find layout with identifier "7418fe82-a082-48ec-b156-03904819c8eb"',
        );
    }

    /**
     * @covers \Netgen\Bundle\LayoutsAdminBundle\Controller\API\Block\CreateInZone::__invoke
     */
    public function testCreateInZoneWithNonExistentLayoutZone(): void
    {
        $data = $this->jsonEncode(
            [
                'block_type' => 'title',
                'layout_id' => '81168ed3-86f9-55ea-b153-101f96f2c136',
                'zone_identifier' => 'unknown',
                'parent_position' => 0,
            ],
        );

        $this->client->request(
            Request::METHOD_POST,
            '/nglayouts/app/api/en/blocks',
            [],
            [],
            [],
            $data,
        );

        $this->assertException(
            $this->client->getResponse(),
            Response::HTTP_NOT_FOUND,
            'Could not find zone with identifier "unknown"',
        );
    }

    /**
     * @covers \Netgen\Bundle\LayoutsAdminBundle\Controller\API\Block\CreateInZone::__invoke
     */
    public function testCreateInZoneWithOutOfRangePosition(): void
    {
        $data = $this->jsonEncode(
            [
                'block_type' => 'title',
                'layout_id' => '81168ed3-86f9-55ea-b153-101f96f2c136',
                'zone_identifier' => 'bottom',
                'parent_position' => 9999,
            ],
        );

        $this->client->request(
            Request::METHOD_POST,
            '/nglayouts/app/api/en/blocks',
            [],
            [],
            [],
            $data,
        );

        $this->assertException(
            $this->client->getResponse(),
            Response::HTTP_UNPROCESSABLE_ENTITY,
            'Argument "position" has an invalid state. Position is out of range.',
        );
    }

    /**
     * @covers \Netgen\Bundle\LayoutsAdminBundle\Controller\API\Block\CreateInZone::__invoke
     */
    public function testCreateInZoneWithNotAllowedBlockDefinition(): void
    {
        $data = $this->jsonEncode(
            [
                'block_type' => 'list',
                'layout_id' => '81168ed3-86f9-55ea-b153-101f96f2c136',
                'zone_identifier' => 'top',
                'parent_position' => 0,
            ],
        );

        $this->client->request(
            Request::METHOD_POST,
            '/nglayouts/app/api/en/blocks',
            [],
            [],
            [],
            $data,
        );

        $this->assertException(
            $this->client->getResponse(),
            Response::HTTP_UNPROCESSABLE_ENTITY,
            'Argument "zone" has an invalid state. Block is not allowed in specified zone.',
        );
    }
}
