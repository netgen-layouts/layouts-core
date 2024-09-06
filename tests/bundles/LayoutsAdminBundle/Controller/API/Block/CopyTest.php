<?php

declare(strict_types=1);

namespace Netgen\Bundle\LayoutsAdminBundle\Tests\Controller\API\Block;

use Netgen\Bundle\LayoutsAdminBundle\Tests\Controller\API\JsonApiTestCase;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

final class CopyTest extends JsonApiTestCase
{
    /**
     * @covers \Netgen\Bundle\LayoutsAdminBundle\Controller\API\Block\Copy::__construct
     * @covers \Netgen\Bundle\LayoutsAdminBundle\Controller\API\Block\Copy::__invoke
     */
    public function testCopy(): void
    {
        $data = $this->jsonEncode(
            [
                'parent_block_id' => 'e666109d-f1db-5fd5-97fa-346f50e9ae59',
                'parent_placeholder' => 'left',
            ],
        );

        $this->client->request(
            Request::METHOD_POST,
            '/nglayouts/app/api/en/blocks/42446cc9-24c3-573c-9022-6b3a764727b5/copy?html=false',
            [],
            [],
            [],
            $data,
        );

        $this->assertResponse(
            $this->client->getResponse(),
            'blocks/copy_block',
            Response::HTTP_CREATED,
        );
    }

    /**
     * @covers \Netgen\Bundle\LayoutsAdminBundle\Controller\API\Block\Copy::__invoke
     */
    public function testCopyWithNonExistentBlock(): void
    {
        $this->client->request(
            Request::METHOD_POST,
            '/nglayouts/app/api/en/blocks/ffffffff-ffff-ffff-ffff-ffffffffffff/copy',
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
     * @covers \Netgen\Bundle\LayoutsAdminBundle\Controller\API\Block\Copy::__invoke
     */
    public function testCopyWithNonExistentTargetBlock(): void
    {
        $data = $this->jsonEncode(
            [
                // This is a random UUID.
                'parent_block_id' => 'cbdb1617-9a2c-48e3-9870-d0f707dbff1f',
                'parent_placeholder' => 'main',
            ],
        );

        $this->client->request(
            Request::METHOD_POST,
            '/nglayouts/app/api/en/blocks/42446cc9-24c3-573c-9022-6b3a764727b5/copy',
            [],
            [],
            [],
            $data,
        );

        $this->assertException(
            $this->client->getResponse(),
            Response::HTTP_NOT_FOUND,
            'Could not find block with identifier "cbdb1617-9a2c-48e3-9870-d0f707dbff1f"',
        );
    }

    /**
     * @covers \Netgen\Bundle\LayoutsAdminBundle\Controller\API\Block\Copy::__invoke
     */
    public function testCopyWithNonExistentPlaceholder(): void
    {
        $data = $this->jsonEncode(
            [
                'parent_block_id' => 'e666109d-f1db-5fd5-97fa-346f50e9ae59',
                'parent_placeholder' => 'unknown',
            ],
        );

        $this->client->request(
            Request::METHOD_POST,
            '/nglayouts/app/api/en/blocks/42446cc9-24c3-573c-9022-6b3a764727b5/copy',
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
     * @covers \Netgen\Bundle\LayoutsAdminBundle\Controller\API\Block\Copy::__invoke
     */
    public function testCopyWithNonContainerTargetBlock(): void
    {
        $data = $this->jsonEncode(
            [
                'parent_block_id' => '129f51de-a535-5094-8517-45d672e06302',
                'parent_placeholder' => 'main',
            ],
        );

        $this->client->request(
            Request::METHOD_POST,
            '/nglayouts/app/api/en/blocks/42446cc9-24c3-573c-9022-6b3a764727b5/copy',
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
     * @covers \Netgen\Bundle\LayoutsAdminBundle\Controller\API\Block\Copy::__invoke
     */
    public function testCopyWithContainerInsideContainer(): void
    {
        $data = $this->jsonEncode(
            [
                'parent_block_id' => 'a2806e8a-ea8c-5c3b-8f84-2cbdae1a07f6',
                'parent_placeholder' => 'main',
            ],
        );

        $this->client->request(
            Request::METHOD_POST,
            '/nglayouts/app/api/en/blocks/e666109d-f1db-5fd5-97fa-346f50e9ae59/copy',
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
     * @covers \Netgen\Bundle\LayoutsAdminBundle\Controller\API\Block\Copy::__invoke
     */
    public function testCopyWithInvalidBlockId(): void
    {
        $data = $this->jsonEncode(
            [
                'parent_block_id' => 42,
                'parent_placeholder' => 'main',
            ],
        );

        $this->client->request(
            Request::METHOD_POST,
            '/nglayouts/app/api/en/blocks/42446cc9-24c3-573c-9022-6b3a764727b5/copy',
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
     * @covers \Netgen\Bundle\LayoutsAdminBundle\Controller\API\Block\Copy::__invoke
     */
    public function testCopyWithMissingBlockId(): void
    {
        $data = $this->jsonEncode(
            [
                'parent_placeholder' => 'main',
            ],
        );

        $this->client->request(
            Request::METHOD_POST,
            '/nglayouts/app/api/en/blocks/42446cc9-24c3-573c-9022-6b3a764727b5/copy',
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
