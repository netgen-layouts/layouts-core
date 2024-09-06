<?php

declare(strict_types=1);

namespace Netgen\Bundle\LayoutsAdminBundle\Tests\Controller\API\Block;

use Netgen\Bundle\LayoutsAdminBundle\Tests\Controller\API\JsonApiTestCase;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

final class MoveTest extends JsonApiTestCase
{
    /**
     * @covers \Netgen\Bundle\LayoutsAdminBundle\Controller\API\Block\Move::__construct
     * @covers \Netgen\Bundle\LayoutsAdminBundle\Controller\API\Block\Move::__invoke
     */
    public function testMove(): void
    {
        $data = $this->jsonEncode(
            [
                'parent_block_id' => 'e666109d-f1db-5fd5-97fa-346f50e9ae59',
                'parent_placeholder' => 'left',
                'parent_position' => 0,
            ],
        );

        $this->client->request(
            Request::METHOD_POST,
            '/nglayouts/app/api/en/blocks/42446cc9-24c3-573c-9022-6b3a764727b5/move',
            [],
            [],
            [],
            $data,
        );

        $this->assertEmptyResponse($this->client->getResponse());
    }

    /**
     * @covers \Netgen\Bundle\LayoutsAdminBundle\Controller\API\Block\Move::__invoke
     */
    public function testMoveToDifferentPlaceholder(): void
    {
        $data = $this->jsonEncode(
            [
                'parent_block_id' => 'e666109d-f1db-5fd5-97fa-346f50e9ae59',
                'parent_placeholder' => 'right',
                'parent_position' => 0,
            ],
        );

        $this->client->request(
            Request::METHOD_POST,
            '/nglayouts/app/api/en/blocks/129f51de-a535-5094-8517-45d672e06302/move',
            [],
            [],
            [],
            $data,
        );

        $this->assertEmptyResponse($this->client->getResponse());
    }

    /**
     * @covers \Netgen\Bundle\LayoutsAdminBundle\Controller\API\Block\Move::__invoke
     */
    public function testMoveToDifferentBlock(): void
    {
        $data = $this->jsonEncode(
            [
                'parent_block_id' => 'a2806e8a-ea8c-5c3b-8f84-2cbdae1a07f6',
                'parent_placeholder' => 'main',
                'parent_position' => 0,
            ],
        );

        $this->client->request(
            Request::METHOD_POST,
            '/nglayouts/app/api/en/blocks/129f51de-a535-5094-8517-45d672e06302/move',
            [],
            [],
            [],
            $data,
        );

        $this->assertEmptyResponse($this->client->getResponse());
    }

    /**
     * @covers \Netgen\Bundle\LayoutsAdminBundle\Controller\API\Block\Move::__invoke
     */
    public function testMoveWithNonExistentBlock(): void
    {
        $this->client->request(
            Request::METHOD_POST,
            '/nglayouts/app/api/en/blocks/ffffffff-ffff-ffff-ffff-ffffffffffff/move/zone',
            [],
            [],
            [],
            $this->jsonEncode([]),
        );

        $this->assertException(
            $this->client->getResponse(),
            Response::HTTP_NOT_FOUND,
            'Could not find block with identifier "ffffffff-ffff-ffff-ffff-ffffffffffff"',
        );
    }

    /**
     * @covers \Netgen\Bundle\LayoutsAdminBundle\Controller\API\Block\Move::__invoke
     */
    public function testMoveWithNonExistentTargetBlock(): void
    {
        $data = $this->jsonEncode(
            [
                // This is a random UUID.
                'parent_block_id' => '2c9e3553-8fa5-49f7-9672-e5a5218ce812',
                'parent_placeholder' => 'main',
                'parent_position' => 1,
            ],
        );

        $this->client->request(
            Request::METHOD_POST,
            '/nglayouts/app/api/en/blocks/b07d3a85-bcdb-5af2-9b6f-deba36c700e7/move',
            [],
            [],
            [],
            $data,
        );

        $this->assertException(
            $this->client->getResponse(),
            Response::HTTP_NOT_FOUND,
            'Could not find block with identifier "2c9e3553-8fa5-49f7-9672-e5a5218ce812"',
        );
    }

    /**
     * @covers \Netgen\Bundle\LayoutsAdminBundle\Controller\API\Block\Move::__invoke
     */
    public function testMoveWithNonExistentPlaceholder(): void
    {
        $data = $this->jsonEncode(
            [
                'parent_block_id' => 'e666109d-f1db-5fd5-97fa-346f50e9ae59',
                'parent_placeholder' => 'unknown',
                'parent_position' => 1,
            ],
        );

        $this->client->request(
            Request::METHOD_POST,
            '/nglayouts/app/api/en/blocks/42446cc9-24c3-573c-9022-6b3a764727b5/move',
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
     * @covers \Netgen\Bundle\LayoutsAdminBundle\Controller\API\Block\Move::__invoke
     */
    public function testMoveWithNonContainerTargetBlock(): void
    {
        $data = $this->jsonEncode(
            [
                'parent_block_id' => 'b07d3a85-bcdb-5af2-9b6f-deba36c700e7',
                'parent_placeholder' => 'main',
                'parent_position' => 0,
            ],
        );

        $this->client->request(
            Request::METHOD_POST,
            '/nglayouts/app/api/en/blocks/28df256a-2467-5527-b398-9269ccc652de/move',
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
     * @covers \Netgen\Bundle\LayoutsAdminBundle\Controller\API\Block\Move::__invoke
     */
    public function testMoveWithOutOfRangePosition(): void
    {
        $data = $this->jsonEncode(
            [
                'parent_block_id' => 'e666109d-f1db-5fd5-97fa-346f50e9ae59',
                'parent_placeholder' => 'left',
                'parent_position' => 9999,
            ],
        );

        $this->client->request(
            Request::METHOD_POST,
            '/nglayouts/app/api/en/blocks/42446cc9-24c3-573c-9022-6b3a764727b5/move',
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
     * @covers \Netgen\Bundle\LayoutsAdminBundle\Controller\API\Block\Move::__invoke
     */
    public function testMoveWithContainerInsideContainer(): void
    {
        $data = $this->jsonEncode(
            [
                'parent_block_id' => 'a2806e8a-ea8c-5c3b-8f84-2cbdae1a07f6',
                'parent_placeholder' => 'main',
                'parent_position' => 0,
            ],
        );

        $this->client->request(
            Request::METHOD_POST,
            '/nglayouts/app/api/en/blocks/e666109d-f1db-5fd5-97fa-346f50e9ae59/move',
            [],
            [],
            [],
            $data,
        );

        $this->assertException(
            $this->client->getResponse(),
            Response::HTTP_UNPROCESSABLE_ENTITY,
            'Argument "block" has an invalid state. Containers cannot be placed inside containers.',
        );
    }

    /**
     * @covers \Netgen\Bundle\LayoutsAdminBundle\Controller\API\Block\Move::__invoke
     */
    public function testMoveWithInvalidBlockId(): void
    {
        $data = $this->jsonEncode(
            [
                'parent_block_id' => 42,
                'parent_placeholder' => 'main',
                'parent_position' => 1,
            ],
        );

        $this->client->request(
            Request::METHOD_POST,
            '/nglayouts/app/api/en/blocks/b07d3a85-bcdb-5af2-9b6f-deba36c700e7/move',
            [],
            [],
            [],
            $data,
        );

        $this->assertException(
            $this->client->getResponse(),
            Response::HTTP_BAD_REQUEST,
            '/^There was an error validating "parent_block_id": This (value )?is not a valid UUID.$/',
        );
    }

    /**
     * @covers \Netgen\Bundle\LayoutsAdminBundle\Controller\API\Block\Move::__invoke
     */
    public function testMoveWithMissingBlockId(): void
    {
        $data = $this->jsonEncode(
            [
                'parent_placeholder' => 'main',
                'parent_position' => 1,
            ],
        );

        $this->client->request(
            Request::METHOD_POST,
            '/nglayouts/app/api/en/blocks/b07d3a85-bcdb-5af2-9b6f-deba36c700e7/move',
            [],
            [],
            [],
            $data,
        );

        $this->assertException(
            $this->client->getResponse(),
            Response::HTTP_BAD_REQUEST,
            'There was an error validating "parent_block_id": This value should not be blank.',
        );
    }
}
