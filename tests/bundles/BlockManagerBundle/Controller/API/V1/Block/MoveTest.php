<?php

namespace Netgen\Bundle\BlockManagerBundle\Tests\Controller\API\V1\Block;

use Netgen\Bundle\BlockManagerBundle\Tests\Controller\API\JsonApiTestCase;
use Symfony\Component\HttpFoundation\Response;

final class MoveTest extends JsonApiTestCase
{
    /**
     * @covers \Netgen\Bundle\BlockManagerBundle\Controller\API\V1\Block\Move::__construct
     * @covers \Netgen\Bundle\BlockManagerBundle\Controller\API\V1\Block\Move::__invoke
     */
    public function testMove()
    {
        $data = $this->jsonEncode(
            [
                'block_id' => 33,
                'placeholder' => 'left',
                'position' => 0,
            ]
        );

        $this->client->request(
            'POST',
            '/bm/api/v1/en/blocks/34/move',
            [],
            [],
            [],
            $data
        );

        $this->assertEmptyResponse($this->client->getResponse());
    }

    /**
     * @covers \Netgen\Bundle\BlockManagerBundle\Controller\API\V1\Block\Move::__invoke
     */
    public function testMoveToDifferentPlaceholder()
    {
        $data = $this->jsonEncode(
            [
                'block_id' => 33,
                'placeholder' => 'right',
                'position' => 0,
            ]
        );

        $this->client->request(
            'POST',
            '/bm/api/v1/en/blocks/37/move',
            [],
            [],
            [],
            $data
        );

        $this->assertEmptyResponse($this->client->getResponse());
    }

    /**
     * @covers \Netgen\Bundle\BlockManagerBundle\Controller\API\V1\Block\Move::__invoke
     */
    public function testMoveToDifferentBlock()
    {
        $data = $this->jsonEncode(
            [
                'block_id' => 38,
                'placeholder' => 'main',
                'position' => 0,
            ]
        );

        $this->client->request(
            'POST',
            '/bm/api/v1/en/blocks/37/move',
            [],
            [],
            [],
            $data
        );

        $this->assertEmptyResponse($this->client->getResponse());
    }

    /**
     * @covers \Netgen\Bundle\BlockManagerBundle\Controller\API\V1\Block\Move::__invoke
     */
    public function testMoveWithNonExistentBlock()
    {
        $data = $this->jsonEncode([]);

        $this->client->request(
            'POST',
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
     * @covers \Netgen\Bundle\BlockManagerBundle\Controller\API\V1\Block\Move::__invoke
     */
    public function testMoveWithNonExistentTargetBlock()
    {
        $data = $this->jsonEncode(
            [
                'block_id' => 9999,
                'placeholder' => 'main',
                'position' => 1,
            ]
        );

        $this->client->request(
            'POST',
            '/bm/api/v1/en/blocks/32/move',
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
     * @covers \Netgen\Bundle\BlockManagerBundle\Controller\API\V1\Block\Move::__invoke
     */
    public function testMoveWithNonExistentPlaceholder()
    {
        $data = $this->jsonEncode(
            [
                'block_id' => 33,
                'placeholder' => 'unknown',
                'position' => 1,
            ]
        );

        $this->client->request(
            'POST',
            '/bm/api/v1/en/blocks/34/move',
            [],
            [],
            [],
            $data
        );

        $this->assertException(
            $this->client->getResponse(),
            Response::HTTP_UNPROCESSABLE_ENTITY,
            'Argument "placeholder" has an invalid state. Target block does not have the specified placeholder.'
        );
    }

    /**
     * @covers \Netgen\Bundle\BlockManagerBundle\Controller\API\V1\Block\Move::__invoke
     */
    public function testMoveWithNonContainerTargetBlock()
    {
        $data = $this->jsonEncode(
            [
                'block_id' => 32,
                'placeholder' => 'main',
                'position' => 0,
            ]
        );

        $this->client->request(
            'POST',
            '/bm/api/v1/en/blocks/31/move',
            [],
            [],
            [],
            $data
        );

        $this->assertException(
            $this->client->getResponse(),
            Response::HTTP_UNPROCESSABLE_ENTITY,
            'Argument "targetBlock" has an invalid state. Target block is not a container.'
        );
    }

    /**
     * @covers \Netgen\Bundle\BlockManagerBundle\Controller\API\V1\Block\Move::__invoke
     */
    public function testMoveWithOutOfRangePosition()
    {
        $data = $this->jsonEncode(
            [
                'block_id' => 33,
                'placeholder' => 'left',
                'position' => 9999,
            ]
        );

        $this->client->request(
            'POST',
            '/bm/api/v1/en/blocks/34/move',
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
     * @covers \Netgen\Bundle\BlockManagerBundle\Controller\API\V1\Block\Move::__invoke
     */
    public function testMoveWithContainerInsideContainer()
    {
        $data = $this->jsonEncode(
            [
                'block_id' => 38,
                'placeholder' => 'main',
                'position' => 0,
            ]
        );

        $this->client->request(
            'POST',
            '/bm/api/v1/en/blocks/33/move',
            [],
            [],
            [],
            $data
        );

        $this->assertException(
            $this->client->getResponse(),
            Response::HTTP_UNPROCESSABLE_ENTITY,
            'Argument "block" has an invalid state. Containers cannot be placed inside containers.'
        );
    }

    /**
     * @covers \Netgen\Bundle\BlockManagerBundle\Controller\API\V1\Block\Move::__invoke
     */
    public function testMoveWithInvalidBlockId()
    {
        $data = $this->jsonEncode(
            [
                'block_id' => [42],
                'placeholder' => 'main',
                'position' => 1,
            ]
        );

        $this->client->request(
            'POST',
            '/bm/api/v1/en/blocks/32/move',
            [],
            [],
            [],
            $data
        );

        $this->assertException(
            $this->client->getResponse(),
            Response::HTTP_BAD_REQUEST,
            'There was an error validating "blockId": This value should be of type scalar.'
        );
    }

    /**
     * @covers \Netgen\Bundle\BlockManagerBundle\Controller\API\V1\Block\Move::__invoke
     */
    public function testMoveWithInvalidPlaceholder()
    {
        $data = $this->jsonEncode(
            [
                'block_id' => 33,
                'placeholder' => 42,
                'position' => 1,
            ]
        );

        $this->client->request(
            'POST',
            '/bm/api/v1/en/blocks/34/move',
            [],
            [],
            [],
            $data
        );

        $this->assertException(
            $this->client->getResponse(),
            Response::HTTP_BAD_REQUEST,
            'There was an error validating "placeholder": This value should be of type string.'
        );
    }

    /**
     * @covers \Netgen\Bundle\BlockManagerBundle\Controller\API\V1\Block\Move::__invoke
     */
    public function testMoveWithInvalidPosition()
    {
        $data = $this->jsonEncode(
            [
                'block_id' => 33,
                'placeholder' => 'main',
                'position' => '1',
            ]
        );

        $this->client->request(
            'POST',
            '/bm/api/v1/en/blocks/34/move',
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
     * @covers \Netgen\Bundle\BlockManagerBundle\Controller\API\V1\Block\Move::__invoke
     */
    public function testMoveWithMissingBlockId()
    {
        $data = $this->jsonEncode(
            [
                'placeholder' => 'main',
                'position' => 1,
            ]
        );

        $this->client->request(
            'POST',
            '/bm/api/v1/en/blocks/32/move',
            [],
            [],
            [],
            $data
        );

        $this->assertException(
            $this->client->getResponse(),
            Response::HTTP_BAD_REQUEST,
            'There was an error validating "blockId": This value should not be blank.'
        );
    }

    /**
     * @covers \Netgen\Bundle\BlockManagerBundle\Controller\API\V1\Block\Move::__invoke
     */
    public function testMoveWithMissingPlaceholder()
    {
        $data = $this->jsonEncode(
            [
                'block_id' => 33,
                'position' => 1,
            ]
        );

        $this->client->request(
            'POST',
            '/bm/api/v1/en/blocks/34/move',
            [],
            [],
            [],
            $data
        );

        $this->assertException(
            $this->client->getResponse(),
            Response::HTTP_BAD_REQUEST,
            'There was an error validating "placeholder": This value should not be blank.'
        );
    }

    /**
     * @covers \Netgen\Bundle\BlockManagerBundle\Controller\API\V1\Block\Move::__invoke
     */
    public function testMoveWithMissingPosition()
    {
        $data = $this->jsonEncode(
            [
                'block_id' => 33,
                'placeholder' => 'main',
            ]
        );

        $this->client->request(
            'POST',
            '/bm/api/v1/en/blocks/34/move',
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
