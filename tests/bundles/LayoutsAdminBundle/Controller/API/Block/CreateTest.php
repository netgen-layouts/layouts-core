<?php

declare(strict_types=1);

namespace Netgen\Bundle\LayoutsAdminBundle\Tests\Controller\API\Block;

use Netgen\Bundle\LayoutsAdminBundle\Tests\Controller\API\JsonApiTestCase;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

final class CreateTest extends JsonApiTestCase
{
    /**
     * @covers \Netgen\Bundle\LayoutsAdminBundle\Controller\API\Block\Create::__construct
     * @covers \Netgen\Bundle\LayoutsAdminBundle\Controller\API\Block\Create::__invoke
     * @covers \Netgen\Bundle\LayoutsAdminBundle\Controller\API\Block\Utils\CreateStructBuilder::__construct
     * @covers \Netgen\Bundle\LayoutsAdminBundle\Controller\API\Block\Utils\CreateStructBuilder::buildCreateStruct
     */
    public function testCreate(): void
    {
        $data = $this->jsonEncode(
            [
                'block_type' => 'list',
                'parent_placeholder' => 'left',
                'parent_position' => 0,
            ],
        );

        $this->client->request(
            Request::METHOD_POST,
            '/nglayouts/app/api/en/blocks/e666109d-f1db-5fd5-97fa-346f50e9ae59?html=false',
            [],
            [],
            [],
            $data,
        );

        $this->assertResponse(
            $this->client->getResponse(),
            'blocks/create_block',
            Response::HTTP_CREATED,
        );
    }

    /**
     * @covers \Netgen\Bundle\LayoutsAdminBundle\Controller\API\Block\Create::__invoke
     * @covers \Netgen\Bundle\LayoutsAdminBundle\Controller\API\Block\Utils\CreateStructBuilder::buildCreateStruct
     */
    public function testCreateWithViewType(): void
    {
        $data = $this->jsonEncode(
            [
                'block_type' => 'grid',
                'parent_placeholder' => 'left',
                'parent_position' => 0,
            ],
        );

        $this->client->request(
            Request::METHOD_POST,
            '/nglayouts/app/api/en/blocks/e666109d-f1db-5fd5-97fa-346f50e9ae59?html=false',
            [],
            [],
            [],
            $data,
        );

        $this->assertResponse(
            $this->client->getResponse(),
            'blocks/create_block_with_view_type',
            Response::HTTP_CREATED,
        );
    }

    /**
     * @covers \Netgen\Bundle\LayoutsAdminBundle\Controller\API\Block\Create::__invoke
     * @covers \Netgen\Bundle\LayoutsAdminBundle\Controller\API\Block\Utils\CreateStructBuilder::buildCreateStruct
     */
    public function testCreateWithItemViewType(): void
    {
        $data = $this->jsonEncode(
            [
                'block_type' => 'grid',
                'parent_placeholder' => 'left',
                'parent_position' => 0,
            ],
        );

        $this->client->request(
            Request::METHOD_POST,
            '/nglayouts/app/api/en/blocks/e666109d-f1db-5fd5-97fa-346f50e9ae59?html=false',
            [],
            [],
            [],
            $data,
        );

        $this->assertResponse(
            $this->client->getResponse(),
            'blocks/create_block_with_item_view_type',
            Response::HTTP_CREATED,
        );
    }

    /**
     * @covers \Netgen\Bundle\LayoutsAdminBundle\Controller\API\Block\Create::__invoke
     * @covers \Netgen\Bundle\LayoutsAdminBundle\Controller\API\Block\Utils\CreateStructBuilder::buildCreateStruct
     */
    public function testCreateWithNoPosition(): void
    {
        $data = $this->jsonEncode(
            [
                'block_type' => 'list',
                'parent_placeholder' => 'left',
            ],
        );

        $this->client->request(
            Request::METHOD_POST,
            '/nglayouts/app/api/en/blocks/e666109d-f1db-5fd5-97fa-346f50e9ae59?html=false',
            [],
            [],
            [],
            $data,
        );

        $this->assertResponse(
            $this->client->getResponse(),
            'blocks/create_block_at_end',
            Response::HTTP_CREATED,
        );
    }

    /**
     * @covers \Netgen\Bundle\LayoutsAdminBundle\Controller\API\Block\Create::__invoke
     */
    public function testCreateWithNonContainerTargetBlock(): void
    {
        $data = $this->jsonEncode(
            [
                'block_type' => 'list',
                'parent_placeholder' => 'main',
                'parent_position' => 0,
            ],
        );

        $this->client->request(
            Request::METHOD_POST,
            '/nglayouts/app/api/en/blocks/28df256a-2467-5527-b398-9269ccc652de',
            [],
            [],
            [],
            $data,
        );

        $this->assertException(
            $this->client->getResponse(),
            Response::HTTP_UNPROCESSABLE_ENTITY,
            'Argument "targetBlock" has an invalid state. Target block is not a container.',
        );
    }

    /**
     * @covers \Netgen\Bundle\LayoutsAdminBundle\Controller\API\Block\Create::__invoke
     */
    public function testCreateWithContainerInsideContainer(): void
    {
        $data = $this->jsonEncode(
            [
                'block_type' => 'column',
                'parent_placeholder' => 'left',
                'parent_position' => 0,
            ],
        );

        $this->client->request(
            Request::METHOD_POST,
            '/nglayouts/app/api/en/blocks/e666109d-f1db-5fd5-97fa-346f50e9ae59',
            [],
            [],
            [],
            $data,
        );

        $this->assertException(
            $this->client->getResponse(),
            Response::HTTP_UNPROCESSABLE_ENTITY,
            'Argument "blockCreateStruct" has an invalid state. Containers cannot be placed inside containers.',
        );
    }

    /**
     * @covers \Netgen\Bundle\LayoutsAdminBundle\Controller\API\Block\Create::__invoke
     */
    public function testCreateWithNonExistentBlockType(): void
    {
        $data = $this->jsonEncode(
            [
                'block_type' => 'unknown',
                'parent_placeholder' => 'main',
                'parent_position' => 0,
            ],
        );

        $this->client->request(
            Request::METHOD_POST,
            '/nglayouts/app/api/en/blocks/e666109d-f1db-5fd5-97fa-346f50e9ae59',
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
     * @covers \Netgen\Bundle\LayoutsAdminBundle\Controller\API\Block\Create::__invoke
     */
    public function testCreateWithNonExistentPlaceholder(): void
    {
        $data = $this->jsonEncode(
            [
                'block_type' => 'title',
                'parent_placeholder' => 'unknown',
                'parent_position' => 0,
            ],
        );

        $this->client->request(
            Request::METHOD_POST,
            '/nglayouts/app/api/en/blocks/e666109d-f1db-5fd5-97fa-346f50e9ae59',
            [],
            [],
            [],
            $data,
        );

        $this->assertException(
            $this->client->getResponse(),
            Response::HTTP_UNPROCESSABLE_ENTITY,
            'Argument "placeholder" has an invalid state. Target block does not have the specified placeholder.',
        );
    }

    /**
     * @covers \Netgen\Bundle\LayoutsAdminBundle\Controller\API\Block\Create::__invoke
     */
    public function testCreateWithOutOfRangePosition(): void
    {
        $data = $this->jsonEncode(
            [
                'block_type' => 'list',
                'parent_placeholder' => 'left',
                'parent_position' => 9999,
            ],
        );

        $this->client->request(
            Request::METHOD_POST,
            '/nglayouts/app/api/en/blocks/e666109d-f1db-5fd5-97fa-346f50e9ae59',
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
}
