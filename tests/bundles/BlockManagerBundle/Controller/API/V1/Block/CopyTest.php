<?php

declare(strict_types=1);

namespace Netgen\Bundle\BlockManagerBundle\Tests\Controller\API\V1\Block;

use Netgen\Bundle\BlockManagerBundle\Tests\Controller\API\JsonApiTestCase;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

final class CopyTest extends JsonApiTestCase
{
    /**
     * @covers \Netgen\Bundle\BlockManagerBundle\Controller\API\V1\Block\Copy::__construct
     * @covers \Netgen\Bundle\BlockManagerBundle\Controller\API\V1\Block\Copy::__invoke
     */
    public function testCopy()
    {
        $data = $this->jsonEncode(
            [
                'block_id' => 33,
                'placeholder' => 'left',
            ]
        );

        $this->client->request(
            Request::METHOD_POST,
            '/bm/api/v1/en/blocks/34/copy?html=false',
            [],
            [],
            [],
            $data
        );

        $this->assertResponse(
            $this->client->getResponse(),
            'v1/blocks/copy_block',
            Response::HTTP_CREATED
        );
    }

    /**
     * @covers \Netgen\Bundle\BlockManagerBundle\Controller\API\V1\Block\Copy::__invoke
     */
    public function testCopyWithNonExistentBlock()
    {
        $data = $this->jsonEncode([]);

        $this->client->request(
            Request::METHOD_POST,
            '/bm/api/v1/en/blocks/9999/copy',
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
     * @covers \Netgen\Bundle\BlockManagerBundle\Controller\API\V1\Block\Copy::__invoke
     */
    public function testCopyWithNonExistentTargetBlock()
    {
        $data = $this->jsonEncode(
            [
                'block_id' => 9999,
                'placeholder' => 'main',
            ]
        );

        $this->client->request(
            Request::METHOD_POST,
            '/bm/api/v1/en/blocks/34/copy',
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
     * @covers \Netgen\Bundle\BlockManagerBundle\Controller\API\V1\Block\Copy::__invoke
     */
    public function testCopyWithNonExistentPlaceholder()
    {
        $data = $this->jsonEncode(
            [
                'block_id' => 33,
                'placeholder' => 'unknown',
            ]
        );

        $this->client->request(
            Request::METHOD_POST,
            '/bm/api/v1/en/blocks/34/copy',
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
     * @covers \Netgen\Bundle\BlockManagerBundle\Controller\API\V1\Block\Copy::__invoke
     */
    public function testCopyWithNonContainerTargetBlock()
    {
        $data = $this->jsonEncode(
            [
                'block_id' => 37,
                'placeholder' => 'main',
            ]
        );

        $this->client->request(
            Request::METHOD_POST,
            '/bm/api/v1/en/blocks/34/copy',
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
     * @covers \Netgen\Bundle\BlockManagerBundle\Controller\API\V1\Block\Copy::__invoke
     */
    public function testCopyWithContainerInsideContainer()
    {
        $data = $this->jsonEncode(
            [
                'block_id' => 38,
                'placeholder' => 'main',
            ]
        );

        $this->client->request(
            Request::METHOD_POST,
            '/bm/api/v1/en/blocks/33/copy',
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
     * @covers \Netgen\Bundle\BlockManagerBundle\Controller\API\V1\Block\Copy::__invoke
     */
    public function testCopyWithInvalidBlockId()
    {
        $data = $this->jsonEncode(
            [
                'block_id' => [42],
                'placeholder' => 'main',
            ]
        );

        $this->client->request(
            Request::METHOD_POST,
            '/bm/api/v1/en/blocks/34/copy',
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
     * @covers \Netgen\Bundle\BlockManagerBundle\Controller\API\V1\Block\Copy::__invoke
     */
    public function testCopyWithInvalidPlaceholder()
    {
        $data = $this->jsonEncode(
            [
                'block_id' => 33,
                'placeholder' => 42,
            ]
        );

        $this->client->request(
            Request::METHOD_POST,
            '/bm/api/v1/en/blocks/34/copy',
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
     * @covers \Netgen\Bundle\BlockManagerBundle\Controller\API\V1\Block\Copy::__invoke
     */
    public function testCopyWithMissingBlockId()
    {
        $data = $this->jsonEncode(
            [
                'placeholder' => 'main',
            ]
        );

        $this->client->request(
            Request::METHOD_POST,
            '/bm/api/v1/en/blocks/34/copy',
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
     * @covers \Netgen\Bundle\BlockManagerBundle\Controller\API\V1\Block\Copy::__invoke
     */
    public function testCopyWithMissingPlaceholder()
    {
        $data = $this->jsonEncode(
            [
                'block_id' => 33,
            ]
        );

        $this->client->request(
            Request::METHOD_POST,
            '/bm/api/v1/en/blocks/34/copy',
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
}
