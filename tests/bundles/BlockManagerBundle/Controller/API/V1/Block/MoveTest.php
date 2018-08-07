<?php

declare(strict_types=1);

namespace Netgen\Bundle\BlockManagerBundle\Tests\Controller\API\V1\Block;

use Netgen\Bundle\BlockManagerBundle\Tests\Controller\API\JsonApiTestCase;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

final class MoveTest extends JsonApiTestCase
{
    /**
     * @covers \Netgen\Bundle\BlockManagerBundle\Controller\API\V1\Block\Move::__construct
     * @covers \Netgen\Bundle\BlockManagerBundle\Controller\API\V1\Block\Move::__invoke
     */
    public function testMove(): void
    {
        $data = $this->jsonEncode(
            [
                'parent_block_id' => 33,
                'parent_placeholder' => 'left',
                'parent_position' => 0,
            ]
        );

        $this->client->request(
            Request::METHOD_POST,
            '/bm/api/v1/en/blocks/34/move',
            [],
            [],
            [],
            $data
        );

        self::assertEmptyResponse($this->client->getResponse());
    }

    /**
     * @covers \Netgen\Bundle\BlockManagerBundle\Controller\API\V1\Block\Move::__invoke
     */
    public function testMoveToDifferentPlaceholder(): void
    {
        $data = $this->jsonEncode(
            [
                'parent_block_id' => 33,
                'parent_placeholder' => 'right',
                'parent_position' => 0,
            ]
        );

        $this->client->request(
            Request::METHOD_POST,
            '/bm/api/v1/en/blocks/37/move',
            [],
            [],
            [],
            $data
        );

        self::assertEmptyResponse($this->client->getResponse());
    }

    /**
     * @covers \Netgen\Bundle\BlockManagerBundle\Controller\API\V1\Block\Move::__invoke
     */
    public function testMoveToDifferentBlock(): void
    {
        $data = $this->jsonEncode(
            [
                'parent_block_id' => 38,
                'parent_placeholder' => 'main',
                'parent_position' => 0,
            ]
        );

        $this->client->request(
            Request::METHOD_POST,
            '/bm/api/v1/en/blocks/37/move',
            [],
            [],
            [],
            $data
        );

        self::assertEmptyResponse($this->client->getResponse());
    }

    /**
     * @covers \Netgen\Bundle\BlockManagerBundle\Controller\API\V1\Block\Move::__invoke
     */
    public function testMoveWithNonExistentBlock(): void
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

        self::assertException(
            $this->client->getResponse(),
            Response::HTTP_NOT_FOUND,
            'Could not find block with identifier "9999"'
        );
    }

    /**
     * @covers \Netgen\Bundle\BlockManagerBundle\Controller\API\V1\Block\Move::__invoke
     */
    public function testMoveWithNonExistentTargetBlock(): void
    {
        $data = $this->jsonEncode(
            [
                'parent_block_id' => 9999,
                'parent_placeholder' => 'main',
                'parent_position' => 1,
            ]
        );

        $this->client->request(
            Request::METHOD_POST,
            '/bm/api/v1/en/blocks/32/move',
            [],
            [],
            [],
            $data
        );

        self::assertException(
            $this->client->getResponse(),
            Response::HTTP_NOT_FOUND,
            'Could not find block with identifier "9999"'
        );
    }

    /**
     * @covers \Netgen\Bundle\BlockManagerBundle\Controller\API\V1\Block\Move::__invoke
     */
    public function testMoveWithNonExistentPlaceholder(): void
    {
        $data = $this->jsonEncode(
            [
                'parent_block_id' => 33,
                'parent_placeholder' => 'unknown',
                'parent_position' => 1,
            ]
        );

        $this->client->request(
            Request::METHOD_POST,
            '/bm/api/v1/en/blocks/34/move',
            [],
            [],
            [],
            $data
        );

        self::assertException(
            $this->client->getResponse(),
            Response::HTTP_UNPROCESSABLE_ENTITY,
            'Argument "placeholder" has an invalid state. Target block does not have the specified placeholder.'
        );
    }

    /**
     * @covers \Netgen\Bundle\BlockManagerBundle\Controller\API\V1\Block\Move::__invoke
     */
    public function testMoveWithNonContainerTargetBlock(): void
    {
        $data = $this->jsonEncode(
            [
                'parent_block_id' => 32,
                'parent_placeholder' => 'main',
                'parent_position' => 0,
            ]
        );

        $this->client->request(
            Request::METHOD_POST,
            '/bm/api/v1/en/blocks/31/move',
            [],
            [],
            [],
            $data
        );

        self::assertException(
            $this->client->getResponse(),
            Response::HTTP_UNPROCESSABLE_ENTITY,
            'Argument "targetBlock" has an invalid state. Target block is not a container.'
        );
    }

    /**
     * @covers \Netgen\Bundle\BlockManagerBundle\Controller\API\V1\Block\Move::__invoke
     */
    public function testMoveWithOutOfRangePosition(): void
    {
        $data = $this->jsonEncode(
            [
                'parent_block_id' => 33,
                'parent_placeholder' => 'left',
                'parent_position' => 9999,
            ]
        );

        $this->client->request(
            Request::METHOD_POST,
            '/bm/api/v1/en/blocks/34/move',
            [],
            [],
            [],
            $data
        );

        self::assertException(
            $this->client->getResponse(),
            Response::HTTP_UNPROCESSABLE_ENTITY,
            'Argument "position" has an invalid state. Position is out of range.'
        );
    }

    /**
     * @covers \Netgen\Bundle\BlockManagerBundle\Controller\API\V1\Block\Move::__invoke
     */
    public function testMoveWithContainerInsideContainer(): void
    {
        $data = $this->jsonEncode(
            [
                'parent_block_id' => 38,
                'parent_placeholder' => 'main',
                'parent_position' => 0,
            ]
        );

        $this->client->request(
            Request::METHOD_POST,
            '/bm/api/v1/en/blocks/33/move',
            [],
            [],
            [],
            $data
        );

        self::assertException(
            $this->client->getResponse(),
            Response::HTTP_UNPROCESSABLE_ENTITY,
            'Argument "block" has an invalid state. Containers cannot be placed inside containers.'
        );
    }

    /**
     * @covers \Netgen\Bundle\BlockManagerBundle\Controller\API\V1\Block\Move::__invoke
     */
    public function testMoveWithInvalidBlockId(): void
    {
        $data = $this->jsonEncode(
            [
                'parent_block_id' => [42],
                'parent_placeholder' => 'main',
                'parent_position' => 1,
            ]
        );

        $this->client->request(
            Request::METHOD_POST,
            '/bm/api/v1/en/blocks/32/move',
            [],
            [],
            [],
            $data
        );

        self::assertException(
            $this->client->getResponse(),
            Response::HTTP_BAD_REQUEST,
            'There was an error validating "blockId": This value should be of type scalar.'
        );
    }

    /**
     * @covers \Netgen\Bundle\BlockManagerBundle\Controller\API\V1\Block\Move::__invoke
     */
    public function testMoveWithMissingBlockId(): void
    {
        $data = $this->jsonEncode(
            [
                'parent_placeholder' => 'main',
                'parent_position' => 1,
            ]
        );

        $this->client->request(
            Request::METHOD_POST,
            '/bm/api/v1/en/blocks/32/move',
            [],
            [],
            [],
            $data
        );

        self::assertException(
            $this->client->getResponse(),
            Response::HTTP_BAD_REQUEST,
            'There was an error validating "blockId": This value should not be blank.'
        );
    }
}
